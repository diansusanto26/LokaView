{{-- resources/views/pages/series/play.blade.php --}}
<x-app-layout :title="$series->title" :showNavbar="false">
  <div class="min-h-[100vh] bg-black text-white flex flex-col">

    <div class="w-full max-w-[1200px] mx-auto px-3 py-6">
      <div class="flex items-center justify-between mb-4">
        <a href="{{ route('series.show', $series->slug) }}"
           class="inline-flex items-center gap-2 px-3 py-2 rounded bg-white/10 hover:bg-white/20">
          ← Kembali
        </a>

        <div class="flex items-center gap-2">
          <button id="help-btn"
                  class="w-9 h-9 grid place-items-center rounded-full bg-white/10 hover:bg-white/20 border border-white/10 text-sm">
            ?
          </button>
          <button id="open-sheet"
                  class="rounded-full px-3 py-1.5 bg-white/10 hover:bg-white/20 border border-white/10">
            Daftar Episode
          </button>
        </div>
      </div>

      <h1 class="text-xl md:text-2xl font-semibold mb-4">
        {{ $series->title }} <span class="opacity-70">— {{ $episode->title }}</span>
      </h1>

      {{-- PLAYER --}}
      <div class="w-full rounded-xl overflow-hidden shadow-lg bg-black relative">
        <video id="player"
               class="w-full block bg-black player-el"
               style="aspect-ratio:16/9"
               playsinline
               controls
               preload="metadata">
          <source src="{{ $episode->file_url ?? route('series.stream', $episode->id) }}" type="video/mp4">
          Browser Anda tidak mendukung video.
        </video>

        {{-- indikator double-tap --}}
        <div id="seek-left-fb"  class="seek-feedback left">⏪ −10s</div>
        <div id="seek-right-fb" class="seek-feedback right">+10s ⏩</div>

        {{-- bubble bantuan --}}
        <div id="help-bubble" class="help-bubble hidden">
          <div class="text-sm font-semibold mb-2">Bantuan</div>
          <ul class="text-xs space-y-1.5">
            <li>⏩ / ⏪ Double-tap kanan/kiri: maju/mundur 10s</li>
            <li>→ / L: maju 10s • ← / J: mundur 10s</li>
            <li>[/]: turunkan/naikkan speed</li>
            <li>Atur speed dari ikon <b>gear</b> → <b>Speed</b></li>
          </ul>
          <label class="mt-3 flex items-center gap-2 text-xs">
            <input id="invert-check" type="checkbox" class="accent-red-600">
            Balik arah kiri/kanan
          </label>
        </div>
      </div>
      {{-- tidak ada lagi baris “Kecepatan … / Hotkeys …” di sini --}}
    </div>

    <style>
      .no-scrollbar::-webkit-scrollbar { display: none; }
      .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
      .clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

      /* Plyr di atas & controls terlihat */
      .plyr { z-index: 30; }
      .plyr--video .plyr__controls { opacity: 1 !important; display: flex !important; }

      .sheet {
        position: fixed; left: 0; right: 0; bottom: 0;
        transform: translateY(100%); transition: transform .28s ease-out;
        z-index: 9999; background: #0B0B12; box-shadow: 0 -8px 24px rgba(0,0,0,.6);
      }
      .sheet.show { transform: translateY(0%); }

      .lock-icon{ width:14px; height:14px; display:block; flex:0 0 14px; }
      .lock-badge{ font-size:12px; line-height:1; }

      .seek-feedback{
        position:absolute; top:50%; transform:translateY(-50%);
        padding:6px 10px; background:rgba(0,0,0,.55);
        border-radius:8px; font-size:12px; pointer-events:none;
        opacity:0; transition:opacity .15s ease; color:#fff; z-index:40;
      }
      .seek-feedback.left{ left:10px; }
      .seek-feedback.right{ right:10px; }
      .seek-feedback.show{ opacity:1; }

      /* help bubble */
      .help-bubble{
        position:absolute; right:12px; top:12px; z-index:40;
        width:min(280px, 86%); padding:12px;
        background:rgba(17,17,24,.92); border:1px solid rgba(255,255,255,.08);
        border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,.5);
      }
      .hidden{ display:none; }
    </style>

    {{-- Bottom Sheet: Daftar Episode --}}
    <div id="episode-sheet" class="sheet">
      <div class="max-w-[1200px] mx-auto px-4 py-4">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-base font-semibold">Daftar Episode</h2>
          <button id="close-sheet" class="p-2 rounded hover:bg-white/10">✕</button>
        </div>

        <ul class="divide-y divide-white/10 max-h-[60vh] overflow-y-auto no-scrollbar">
          @foreach(($series->episodes ?? collect()) as $ep)
            @php $active = $ep->id === $episode->id; @endphp
            <li class="py-3">
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 flex items-center justify-center rounded bg-white/10 text-sm font-semibold shrink-0">
                  {{ $ep->episode_number }}
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-2 min-w-0">
                    <h3 class="font-semibold text-[15px] truncate">{{ $ep->title }}</h3>
                    @if($active)
                      <span class="text-xs text-red-400 whitespace-nowrap">• Sedang diputar</span>
                    @endif
                  </div>
                  <p class="mt-1 text-xs text-gray-400 line-clamp-2 clamp-2">
                    {{ Str::limit(strip_tags($ep->description), 160) }}
                  </p>
                </div>

                <div class="shrink-0 min-w-[140px] text-right flex items-center justify-end">
                  @if($ep->is_locked)
                    <span class="lock-badge inline-flex items-center gap-1.5 px-2 py-1 rounded bg-zinc-700/80 text-gray-300">
                      <svg class="lock-icon" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 11a2 2 0 10-4 0v2a2 2 0 002 2h4a2 2 0 002-2v-2a2 2 0 00-2-2h-4zm0 0V9a4 4 0 118 0v2"/>
                      </svg>
                      <span class="whitespace-nowrap">Terkunci</span>
                      <span class="opacity-70 whitespace-nowrap">({{ number_format($ep->unlock_cost) }} koin)</span>
                    </span>
                  @else
                    <a href="{{ route('series.play', [$series->slug, $ep->id]) }}"
                       class="px-3 py-1.5 rounded bg-red-600 hover:bg-red-700 text-xs font-semibold">
                      Tonton
                    </a>
                  @endif
                </div>
              </div>
            </li>
          @endforeach
        </ul>

        <div class="pt-3 text-xs opacity-70">{{ $episode->title }}</div>
      </div>
    </div>
  </div>

  {{-- Plyr CDN --}}
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css">
  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const videoEl   = document.getElementById('player');
    const sheet     = document.getElementById('episode-sheet');
    const openBtn   = document.getElementById('open-sheet');
    const closeBtn  = document.getElementById('close-sheet');
    const helpBtn   = document.getElementById('help-btn');
    const helpBubble= document.getElementById('help-bubble');
    const invertCheck = document.getElementById('invert-check');

    // Init Plyr (tanpa hotkeys bawaan)
    const player = new Plyr(videoEl, {
      controls: ['play-large','play','progress','current-time','mute','volume','settings','fullscreen'],
      settings: ['speed'], // speed ada di gear
      speed: { selected: 1, options: [0.5, 1, 1.25, 1.5, 2] },
      seekTime: 5,
      keyboard: { focused: false, global: false },
      hideControls: false,
      fullscreen: { enabled: true, fallback: true, iosNative: true },
    });

    // ===== Invert arah (persist) =====
    const INVERT_KEY = 'lokaview:seek_invert';
    let invert = JSON.parse(localStorage.getItem(INVERT_KEY) || 'false');
    invertCheck.checked = invert;
    invertCheck.addEventListener('change', () => {
      invert = invertCheck.checked;
      localStorage.setItem(INVERT_KEY, JSON.stringify(invert));
    });

    // Helpers
    const seekBy = (sec) => {
      const cur = player.currentTime || 0;
      const dur = player.duration || (cur + Math.abs(sec));
      player.currentTime = Math.max(0, Math.min(dur, cur + sec));
    };
    const fbL = document.getElementById('seek-left-fb');
    const fbR = document.getElementById('seek-right-fb');
    const showFB = (side) => {
      const el = side === 'left' ? fbL : fbR;
      el.classList.add('show');
      clearTimeout(el._t);
      el._t = setTimeout(() => el.classList.remove('show'), 300);
    };
    const deltaForSide = (side) => invert ? (side === 'left' ? +10 : -10) : (side === 'left' ? -10 : +10);
    const deltaForKey  = (isLeft) => invert ? (isLeft ? +10 : -10) : (isLeft ? -10 : +10);

    // Double-click / double-tap (tangkap dulu, cegah fullscreen)
    const plyrRoot  = player.elements?.container || videoEl.parentElement;
    const videoWrap = plyrRoot.querySelector('.plyr__video-wrapper') || plyrRoot;

    videoWrap.addEventListener('dblclick', (e) => {
      if (e.target.closest('.plyr__controls')) return;
      e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation && e.stopImmediatePropagation();
      const rect = videoWrap.getBoundingClientRect();
      const side = (e.clientX - rect.left < rect.width / 2) ? 'left' : 'right';
      seekBy(deltaForSide(side)); showFB(side);
    }, { capture: true });

    let lastTap = 0;
    videoWrap.addEventListener('pointerdown', (e) => {
      if (e.target.closest('.plyr__controls')) return;
      const now = performance.now();
      if (now - lastTap < 300) {
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation && e.stopImmediatePropagation();
        const rect = videoWrap.getBoundingClientRect();
        const side = (e.clientX - rect.left < rect.width / 2) ? 'left' : 'right';
        seekBy(deltaForSide(side)); showFB(side);
      }
      lastTap = now;
    }, { capture: true });

    // Bottom-sheet
    const showSheet = () => { sheet.classList.add('show'); openBtn?.classList?.add('hidden'); };
    const hideSheet = () => { sheet.classList.remove('show'); openBtn?.classList?.remove('hidden'); };
    let firstPlayShown = false;
    player.on('play', () => { if (!firstPlayShown) { firstPlayShown = true; showSheet(); } });
    closeBtn.addEventListener('click', hideSheet);
    openBtn.addEventListener('click', showSheet);

    // Hotkeys kustom (CAPTURE)
    window.addEventListener('keydown', (e) => {
      if (['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) return;
      if (e.key === 'h' || e.key === '?') { // toggle bantuan
        e.preventDefault(); helpBubble.classList.toggle('hidden'); return;
      }
      const k = e.key;
      if (k === 'ArrowLeft' || k.toLowerCase() === 'j') {
        e.preventDefault(); e.stopPropagation();
        seekBy(deltaForKey(true));  showFB('left');
      } else if (k === 'ArrowRight' || k.toLowerCase() === 'l') {
        e.preventDefault(); e.stopPropagation();
        seekBy(deltaForKey(false)); showFB('right');
      } else if (k === '[') {
        player.speed = Math.max(0.25, (player.speed - 0.25).toFixed(2));
      } else if (k === ']') {
        player.speed = Math.min(2.5, (parseFloat(player.speed) + 0.25).toFixed(2));
      }
    }, { capture: true });

    // Toggle help bubble via tombol '?'
    helpBtn.addEventListener('click', () => helpBubble.classList.toggle('hidden'));

    // Simpan progress (opsional)
    const STORAGE_KEY = 'lokaview:' + @json($series->id) + ':' + @json($episode->id);
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
      try {
        const { t } = JSON.parse(saved);
        if (!isNaN(t) && t > 0) player.once('canplay', () => {
          const dur = player.duration || t;
          player.currentTime = Math.min(t, dur);
        });
      } catch {}
    }
    const saveProgress = () => localStorage.setItem(STORAGE_KEY, JSON.stringify({ t: player.currentTime, d: player.duration, ts: Date.now() }));
    player.on('timeupdate', () => { if (Math.floor(player.currentTime) % 5 === 0) saveProgress(); });
    player.on('pause', saveProgress);
    window.addEventListener('beforeunload', saveProgress);
  });
  </script>
</x-app-layout>
