<?php

namespace App\Http\Controllers;

use App\Interfaces\SeriesEpisodeRepositoryInterface;
use App\Interfaces\SeriesRepositoryInterface;
use App\Models\SeriesEpisode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SeriesController extends Controller
{
    protected $seriesRepository;
    protected $seriesEpisodeRepository;

    public function __construct(
        SeriesRepositoryInterface $seriesRepository,
        SeriesEpisodeRepositoryInterface $seriesEpisodeRepository
    ) {
        $this->seriesRepository = $seriesRepository;
        $this->seriesEpisodeRepository = $seriesEpisodeRepository;
    }

    public function show($slug)
    {
        $series = $this->seriesRepository->getBySlug($slug);

        // Kalau butuh episodes_count di view, pastikan relasi dihitung
        if (method_exists($series, 'episodes')) {
            $series->loadCount('episodes');
        }

        $relatedSeries = $this->seriesRepository->getAll(['genre_id' => $series->genre_id], 4);

        return view('pages.series.show', compact('series', 'relatedSeries'));
    }

    /**
     * Halaman pemutar video + daftar episode (bottom sheet).
     * User harus login (route diletakkan di dalam group auth).
     */
    public function play($slug, $episodeId)
    {
        $series  = $this->seriesRepository->getBySlug($slug);
        $episode = $this->seriesEpisodeRepository->getById($episodeId);

        // Pastikan episode memang milik series ini
        abort_unless($episode && $episode->series_id === $series->id, 404);

        // Siapkan URL stream untuk <video> di Blade
        $episode->file_url = route('series.stream', $episode->id);

        // Load daftar episode untuk bottom sheet (urut nomor)
        if (method_exists($series, 'episodes')) {
            $series->load(['episodes' => fn($q) => $q->orderBy('episode_number')]);
        }

        return view('pages.series.play', compact('series', 'episode'));
    }

    /**
     * Endpoint streaming file lokal (kolom: SeriesEpisode.video) dengan dukungan HTTP Range.
     * Letakkan route-nya di dalam middleware('auth') agar hanya user login yang bisa memutar.
     */
    public function stream(Request $request, SeriesEpisode $episode)
    {
        // Pastikan field "video" terisi (path relatif di disk 'public', misal: videos/ep-1.mp4)
        if (empty($episode->video)) {
            abort(404, 'Video file not set.');
        }

        $absolutePath = Storage::disk('public')->path($episode->video);
        if (!is_file($absolutePath)) {
            abort(404, 'Video file not found.');
        }

        $fileSize = filesize($absolutePath);
        $mime     = 'video/mp4';
        $range    = $request->headers->get('Range'); // e.g. "bytes=0-"

        // ===== Tanpa Range: kirim full =====
        if (empty($range)) {
            return response()->stream(function () use ($absolutePath) {
                $stream = fopen($absolutePath, 'rb');
                fpassthru($stream);
                fclose($stream);
            }, 200, [
                'Content-Type'   => $mime,
                'Content-Length' => $fileSize,
                'Accept-Ranges'  => 'bytes',
                'Cache-Control'  => 'public, max-age=0',
            ]);
        }

        // ===== Dengan Range: partial content (206) =====
        if (!preg_match('/bytes=(\d*)-(\d*)/', $range, $m)) {
            return response('', 416, ['Content-Range' => "bytes */{$fileSize}"]);
        }

        $start = $m[1] === '' ? 0 : (int) $m[1];
        $end   = $m[2] === '' ? ($fileSize - 1) : (int) $m[2];

        $start = max(0, $start);
        $end   = min($fileSize - 1, $end);
        if ($start > $end || $start >= $fileSize) {
            return response('', 416, ['Content-Range' => "bytes */{$fileSize}"]);
        }

        $length = $end - $start + 1;

        return response()->stream(function () use ($absolutePath, $start, $length) {
            $chunk = 1024 * 1024; // 1MB
            $fp = fopen($absolutePath, 'rb');
            fseek($fp, $start);

            $left = $length;
            while ($left > 0 && !feof($fp)) {
                $read = ($left > $chunk) ? $chunk : $left;
                $buffer = fread($fp, $read);
                echo $buffer;
                flush();
                $left -= strlen($buffer);
                if (connection_aborted()) break;
            }
            fclose($fp);
        }, 206, [
            'Content-Type'   => $mime,
            'Content-Length' => $length,
            'Content-Range'  => "bytes {$start}-{$end}/{$fileSize}",
            'Accept-Ranges'  => 'bytes',
            'Cache-Control'  => 'public, max-age=0',
        ]);
    }
}
