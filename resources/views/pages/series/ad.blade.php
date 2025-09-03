{{-- resources/views/pages/series/ad.blade.php --}}
<x-app-layout :title="$series->title . ' — Iklan'" :showNavbar="false">
  <div class="min-h-[100vh] bg-black text-white flex flex-col items-center">

    <div class="w-full max-w-[900px] mx-auto px-3 py-6">
      <div class="flex items-center justify-between mb-4">
        <a href="{{ route('series.show', $series->slug) }}"
           class="inline-flex items-center gap-2 px-3 py-2 rounded bg-white/10 hover:bg-white/20">
          ← Kembali
        </a>
        <span class="text-sm opacity-80">Menonton iklan untuk membuka episode</span>
      </div>

      <h1 class="text-lg font-semibold mb-4">{{ $series->title }} — {{ $episode->title }}</h1>

      <div class="rounded-xl overflow-hidden shadow-lg bg-black relative">
        <video id="adPlayer" class="w-full block bg-black"
               style="aspect-ratio:16/9"
               playsinline controlslist="nodownload noplaybackrate"
               preload="auto">
          <source src="{{ $adUrl }}" type="video/mp4">
          Browser Anda tidak mendukung video.
        </video>

        <div id="adOverlay" class="absolute inset-0 pointer-events-none flex items-start justify-end p-3">
          <div class="px-3 py-1 rounded bg-black/60 border border-white/10 text-xs">
            Tonton iklan: <span id="adCountdown">--</span> dtk
          </div>
        </div>
      </div>

      <div id="adNote" class="text-xs opacity-70 mt-3">
        Tidak bisa memutar iklan? Tunggu hitung mundur selesai untuk lanjut.
      </div>
    </div>
  </div>

  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css">
  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const adDurationMin = 15; // minimal detik yang harus ditonton (fallback)
      const rewardMinutes = @json($rewardMinutes);
      const videoEl = document.getElementById('adPlayer');
      const countdownEl = document.getElementById('adCountdown');

      const player = new Plyr(videoEl, {
        controls: ['play-large','play','mute','volume','fullscreen'], // tanpa progress/seek
        keyboard: { focused: false, global: false },
        tooltips: { controls: false },
        clickToPlay: true
      });

      // blok seek
      player.on('seeking', () => {
        if (player.currentTime > (player.lastTime || 0) + 1) {
          player.currentTime = player.lastTime || 0;
        }
      });
      player.on('timeupdate', () => player.lastTime = player.currentTime);

      // countdown fallback (kalau event 'ended' tak jalan)
      let left = adDurationMin;
      countdownEl.textContent = left;
      const iv = setInterval(() => {
        if (player.playing) { left = Math.max(0, left - 1); }
        countdownEl.textContent = left;
        if (left <= 0) completeAd(); // fallback selesai
      }, 1000);

      // selesai iklan normal
      player.on('ended', completeAd);

      async function completeAd(){
        clearInterval(iv);
        try {
          const resp = await fetch(@json(route('series.adComplete', $episode->id)), {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': @json(csrf_token()),
              'Accept': 'application/json'
            },
          });
          const data = await resp.json();
          if (data?.ok) {
            window.location.href = data.redirect;
          } else {
            alert('Gagal memvalidasi iklan. Coba lagi.');
          }
        } catch (e) {
          alert('Tidak bisa terhubung ke server.');
        }
      }
    });
  </script>
</x-app-layout>
