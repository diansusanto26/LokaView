<?php

namespace App\Http\Controllers;

use App\Interfaces\SeriesEpisodeRepositoryInterface;
use App\Interfaces\SeriesRepositoryInterface;
use App\Models\EpisodeAdView;
use App\Models\SeriesEpisode;
use App\Models\UnlockedEpisode;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SeriesController extends Controller
{
    protected SeriesRepositoryInterface $seriesRepository;
    protected SeriesEpisodeRepositoryInterface $seriesEpisodeRepository;

    public function __construct(
        SeriesRepositoryInterface $seriesRepository,
        SeriesEpisodeRepositoryInterface $seriesEpisodeRepository
    ) {
        $this->seriesRepository = $seriesRepository;
        $this->seriesEpisodeRepository = $seriesEpisodeRepository;
    }

    // ===================== PUBLIC PAGES =====================

    public function show(string $slug)
    {
        $series = $this->seriesRepository->getBySlug($slug);
        $relatedSeries = $this->seriesRepository->getAll(['genre_id' => $series->genre_id], 4);

        return view('pages.series.show', compact('series', 'relatedSeries'));
    }

    // ===================== PLAYER =====================

    public function play(string $slug, int $episodeId)
    {
        $series  = $this->seriesRepository->getBySlug($slug);
        $episode = $this->seriesEpisodeRepository->getById($episodeId);

        // proteksi akses
        if (!$this->allowedToWatch($episode)) {
            if ($episode->ad_required) {
                return redirect()
                    ->route('series.ad', [$series->slug, $episode->id])
                    ->with('info', 'Tonton iklan sebentar untuk membuka episode ini.');
            }
        }

        // data penanda unlock/ad-access untuk daftar episode
        $userId  = Auth::id();
        $allIds  = ($series->episodes ?? collect())->pluck('id');

        $coinUnlockedIds = UnlockedEpisode::where('user_id', $userId)
            ->whereIn('series_episode_id', $allIds)
            ->pluck('series_episode_id')
            ->all();

        $adAccess = [];
        EpisodeAdView::where('user_id', $userId)
            ->whereIn('series_episode_id', $allIds)
            ->get()
            ->each(function ($row) use (&$adAccess) {
                if ($row->expires_at && $row->expires_at->isFuture()) {
                    $adAccess[$row->series_episode_id] = now()->diffInMinutes($row->expires_at);
                }
            });

        return view('pages.series.play', compact('series', 'episode', 'coinUnlockedIds', 'adAccess'));
    }

    // ===================== AD GATE =====================

    public function ad(string $slug, SeriesEpisode $episode)
    {
        $series = $this->seriesRepository->getBySlug($slug);

        if ($this->allowedToWatch($episode)) {
            return redirect()->route('series.play', [$series->slug, $episode->id]);
        }

        $adUrl = $episode->ad_video
            ? (str_starts_with($episode->ad_video, 'http') ? $episode->ad_video : asset('storage/' . ltrim($episode->ad_video, '/')))
            : asset('assets/ads/default.mp4');

        $rewardMinutes = $episode->ad_reward_minutes ?: 60;

        return view('pages.series.ad', compact('series', 'episode', 'adUrl', 'rewardMinutes'));
    }

    public function adComplete(Request $request, SeriesEpisode $episode)
    {
        $userId = Auth::id();

        EpisodeAdView::updateOrCreate(
            ['series_episode_id' => $episode->id, 'user_id' => $userId],
            ['expires_at' => now()->addMinutes($episode->ad_reward_minutes ?: 60)]
        );

        return redirect()
            ->route('series.play', [$episode->series->slug, $episode->id])
            ->with('success', 'Iklan selesai. Akses gratis sementara dibuat.');
    }

    // ===================== COIN UNLOCK =====================

    public function unlock(Request $request, SeriesEpisode $episode)
    {
        $userId = Auth::id();

        // sudah pernah unlock?
        $already = UnlockedEpisode::where('user_id', $userId)
            ->where('series_episode_id', $episode->id)
            ->exists();

        if ($already) {
            return redirect()
                ->route('series.play', [$episode->series->slug, $episode->id])
                ->with('info', 'Episode ini sudah pernah kamu buka. Koin tidak dipotong.');
        }

        $cost = (int) ($episode->unlock_cost ?? 0);
        if ($cost <= 0) {
            UnlockedEpisode::firstOrCreate([
                'user_id'            => $userId,
                'series_episode_id'  => $episode->id,
            ]);

            return redirect()
                ->route('series.play', [$episode->series->slug, $episode->id])
                ->with('success', 'Episode berhasil dibuka.');
        }

        // transaksi aman untuk pemotongan koin + pencatatan unlock
        try {
            DB::transaction(function () use ($userId, $episode, $cost) {

                // pastikan dompet ada
                $wallet = Wallet::firstOrCreate(['user_id' => $userId], ['coin_balance' => 0]);

                // potong koin secara atomic (hindari race)
                $affected = Wallet::whereKey($wallet->id)
                    ->where('coin_balance', '>=', $cost)
                    ->decrement('coin_balance', $cost);

                if ($affected === 0) {
                    abort(400, "Koin tidak cukup.");
                }

                UnlockedEpisode::firstOrCreate([
                    'user_id'           => $userId,
                    'series_episode_id' => $episode->id,
                ]);
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['coins' => $e->getMessage() ?: 'Transaksi gagal.']);
        }

        return redirect()
            ->route('series.play', [$episode->series->slug, $episode->id])
            ->with('success', "Episode berhasil dibuka. {$cost} koin telah dipotong.");
    }

    // ===================== STREAM =====================

    public function stream(SeriesEpisode $episode): StreamedResponse
    {
        if (!$this->allowedToWatch($episode)) {
            abort(403, 'Episode terkunci. Buka dengan koin atau tonton iklan dulu.');
        }

        $path = storage_path('app/public/' . ltrim($episode->video, '/'));
        if (!is_file($path)) {
            abort(404);
        }

        $size = filesize($path);
        $start = 0;
        $end   = $size - 1;
        $status = 200;
        $headers = [
            'Content-Type'  => 'video/mp4',
            'Accept-Ranges' => 'bytes',
        ];

        if ($range = request()->header('Range')) {
            // e.g. bytes=0- or bytes=1000-2000
            if (preg_match('/bytes=(\d*)-(\d*)/i', $range, $m)) {
                if ($m[1] !== '') $start = (int) $m[1];
                if ($m[2] !== '') $end   = (int) $m[2];
                if ($end >= $size) $end = $size - 1;
                if ($start > $end) $start = 0;

                $status = 206;
                $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
            }
        }

        $length = $end - $start + 1;
        $headers['Content-Length'] = (string) $length;

        return response()->stream(function () use ($path, $start, $end) {
            $fh = fopen($path, 'rb');
            fseek($fh, $start);
            $chunk = 1024 * 8;

            while (!feof($fh) && ftell($fh) <= $end) {
                $remain = $end - ftell($fh) + 1;
                echo fread($fh, ($remain < $chunk) ? $remain : $chunk);
                @ob_flush();
                flush();
            }
            fclose($fh);
        }, $status, $headers);
    }

    // ===================== HELPERS =====================

    protected function allowedToWatch(SeriesEpisode $episode): bool
    {
        if (!$episode->is_locked) {
            return true;
        }

        $userId = Auth::id();

        // unlock via koin?
        $coin = UnlockedEpisode::where('series_episode_id', $episode->id)
            ->where('user_id', $userId)
            ->exists();
        if ($coin) return true;

        // akses gratis dari iklan?
        $ad = EpisodeAdView::where('series_episode_id', $episode->id)
            ->where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->first();

        return (bool) $ad;
    }
}
