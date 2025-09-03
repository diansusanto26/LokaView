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

      {{-- TOAST mengambang (gabungan terbaru) --}}
      @php
        $flashMsg  = session('success') ?? session('info') ?? ($errors->any() ? $errors->first() : null);
        $flashType = session('success') ? 'success' : (session('info') ? 'info' : ($errors->any() ? 'error' : null));
      @endphp
      @if($flashMsg && $flashType)
        <style>
          #toast-root{position:fixed;left:0;right:0;top:12px;z-index:10000;padding:0 16px;display:flex;justify-content:center;pointer-events:none}
          #toast-card{max-width:520px;width:100%;pointer-events:auto;border-radius:12px;color:#fff;overflow:hidden;border:1px solid rgba(255,255,255,.12);
            background:rgba(17,17,24,.9);backdrop-filter:saturate(140%) blur(6px);box-shadow:0 10px 30px rgba(0,0,0,.5);transform:translateY(-8px);opacity:0;transition:.25s}
          #toast-card.show{transform:translateY(0);opacity:1}
          #toast-card.success{border-color:rgba(16,185,129,.35);background:rgba(6,95,70,.2)}
          #toast-card.info   {border-color:rgba(56,189,248,.35);background:rgba(3,105,161,.2)}
          #toast-card.error  {border-color:rgba(248,113,113,.35);background:rgba(127,29,29,.22)}
          #toast-head{display:flex;gap:12px;align-items:flex-start;padding:12px 14px;position:relative}
          #toast-title{font-weight:700;margin-bottom:2px}
          #toast-text{color:rgba(255,255,255,.9);line-height:1.4;word-break:break-word}
          #toast-close{position:absolute;right:6px;top:6px;border:0;background:transparent;color:#fff;opacity:.85;padding:6px;border-radius:8px;cursor:pointer}
          #toast-close:hover{background:rgba(255,255,255,.1)}
          #toast-track{height:4px;background:rgba(255,255,255,.15)}
          #toast-bar{height:100%;width:100%;transform-origin:left center}
          #toast-bar.success{background:#34d399} #toast-bar.info{background:#38bdf8} #toast-bar.error{background:#f87171}
        </style>
        <div id="toast-root" role="status" aria-live="polite">
          <div id="toast-card" class="{{ $flashType }}">
            <div id="toast-head">
              <div style="margin-top:2px">
                @if($flashType==='success')
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="color:#34d399">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                  </svg>
                @elseif($flashType==='info')
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="color:#38bdf8">
                    <circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-width="2" d="M12 8h.01M11 12h2v5h-2z"/>
                  </svg>
                @else
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="color:#f87171">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86l-7.2 12.47A2 2 0 0 0 4.8 19h14.4a2 2 0 0 0 1.71-2.67l-7.2-12.47a2 2 0 0 0-3.42 0z"/>
                  </svg>
                @endif
              </div>
              <div style="min-width:0;padding-right:28px">
                <div id="toast-title">
                  @if($flashType==='success') Berhasil
                  @elseif($flashType==='info')   Info
                  @else                          Gagal
                  @endif
                </div>
                <div id="toast-text">{{ $flashMsg }}</div>
              </div>
              <button id="toast-close" aria-label="Tutup">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            <div id="toast-track"><div id="toast-bar" class="{{ $flashType }}"></div></div>
          </div>
        </div>
        <script>
          (function(){const c=document.getElementById('toast-card'),x=document.getElementById('toast-close'),b=document.getElementById('toast-bar');
            const D=4200;requestAnimationFrame(()=>c.classList.add('show'));
            const t0=performance.now();let r;function k(t){const p=Math.min(1,(t-t0)/D);b.style.transform='scaleX('+(1-p)+')';p<1?r=requestAnimationFrame(k):h()}
            r=requestAnimationFrame(k);function h(){cancelAnimationFrame(r);c.classList.remove('show');setTimeout(()=>document.getElementById('toast-root')?.remove(),250)}
            x?.addEventListener('click',h)})();
        </script>
      @endif

      <h1 class="text-xl md:text-2xl font-semibold mb-4">
        {{ $series->title }} <span class="opacity-70">— {{ $episode->title }}</span>
      </h1>

      {{-- PLAYER --}}
      <div class="w-full rounded-xl overflow-hidden shadow-lg bg-black relative">
        <video id="player"
               class="w-full block bg-black player-el"
               style="aspect-ratio:16/9"
               playsinline controls preload="metadata">
          <source src="{{ route('series.stream', $episode->id) }}" type="video/mp4">
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
            <li>[/]: turunkan/naikkan speed (atau via ikon <b>gear</b>)</li>
          </ul>
          <label class="mt-3 flex items-center gap-2 text-xs">
            <input id="invert-check" type="checkbox" class="accent-red-600">
            Balik arah kiri/kanan
          </label>
        </div>
      </div>
    </div>

    <style>
      .no-scrollbar::-webkit-scrollbar { display: none; }
      .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
      .clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
      .plyr { z-index: 30; }
      .plyr--video .plyr__controls { opacity: 1 !important; display: flex !important; }
      .sheet { position: fixed; left: 0; right: 0; bottom: 0; transform: translateY(100%); transition: transform .28s ease-out;
        z-index: 9999; background: #0B0B12; box-shadow: 0 -8px 24px rgba(0,0,0,.6); }
      .sheet.show { transform: translateY(0%); }
      .lock-icon{ width:14px; height:14px; display:block; flex:0 0 14px; }
      .lock-badge{ font-size:12px; line-height:1; }
      .seek-feedback{ position:absolute; top:50%; transform:translateY(-50%); padding:6px 10px; background:rgba(0,0,0,.55);
        border-radius:8px; font-size:12px; pointer-events:none; opacity:0; transition:opacity .15s ease; color:#fff; z-index:40; }
      .seek-feedback.left{ left:10px; } .seek-feedback.right{ right:10px; }
      .seek-feedback.show{ opacity:1; }
      .help-bubble{ position:absolute; right:12px; top:12px; z-index:40; width:min(280px, 86%); padding:12px;
        background:rgba(17,17,24,.92); border:1px solid rgba(255,255,255,.08); border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,.5); }
      .hidden{ display:none; }
      .modal-mask{ position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:11000; display:none; }
      .modal-mask.show{ display:block; }
      .modal-card{ position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); width:min(520px,92%);
        background:#111118; border:1px solid rgba(255,255,255,.08); border-radius:14px; box-shadow:0 20px 60px rgba(0,0,0,.6); overflow:hidden; }
      .btn{ padding:.6rem .9rem; border-radius:.6rem; font-weight:600; font-size:.875rem; }
      .btn-ghost{ background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); }
      .btn-warn{ background:#d97706; }
      .btn:hover{ filter:brightness(1.1); }
    </style>

    {{-- Bottom Sheet: Daftar Episode --}}
    <div id="episode-sheet" class="sheet">
      <div class="max-w-[1200px] mx-auto px-4 py-4">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-base font-semibold">Daftar Episode</h2>
          <button id="close-sheet" class="p-2 rounded hover:bg-white/10">✕</button>
        </div>

        @php
          $coinUnlockedIds = $coinUnlockedIds ?? [];
        @endphp

        <ul class="divide-y divide-white/10 max-h-[60vh] overflow-y-auto no-scrollbar">
          @foreach(($series->episodes ?? collect()) as $ep)
            @php
              $active = $ep->id === $episode->id;

              $free = (int) ($adAccess[$ep->id] ?? 0);
              $freeLabel = '';
              if ($free > 0) {
                if ($free >= 60) { $h = intdiv($free, 60); $m = $free % 60; $freeLabel = $h.' j '.str_pad((string)$m, 2, '0', STR_PAD_LEFT).' mnt'; }
                else { $freeLabel = $free.' mnt'; }
              }

              $isCoinUnlocked = in_array($ep->id, $coinUnlockedIds, true);
              $isUnlocked = !$ep->is_locked || $isCoinUnlocked || $free > 0;

              $hasUnlockRoute = \Illuminate\Support\Facades\Route::has('series.unlock');
            @endphp

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

                <div class="shrink-0 text-right flex flex-col items-end gap-2 min-w-[230px]">
                  @if($isUnlocked)
                    @if($free > 0)
                      <div class="text-[11px] opacity-75 whitespace-nowrap">Gratis s/d {{ $freeLabel }}</div>
                    @endif
                    <a href="{{ route('series.play', [$series->slug, $ep->id]) }}"
                       class="px-3 py-1.5 rounded bg-red-600 hover:bg-red-700 text-xs font-semibold">
                      Tonton
                    </a>
                  @else
                    <div class="flex items-center gap-2">
                      <button
                        type="button"
                        class="px-3 py-1.5 rounded bg-white/10 hover:bg-white/20 text-xs font-semibold border border-white/10 js-open-lock"
                        data-epid="{{ $ep->id }}"
                        data-eptitle="{{ $ep->title }}"
                        data-ads="{{ route('series.ad', [$series->slug, $ep->id]) }}"
                        data-cost="{{ (int)($ep->unlock_cost ?? 0) }}"
                        data-canunlock="{{ $hasUnlockRoute ? '1' : '0' }}"
                        data-unlock="{{ $hasUnlockRoute ? route('series.unlock', $ep->id) : '' }}"
                      >
                        Pilih Akses
                      </button>

                      <form action="{{ $hasUnlockRoute ? route('series.unlock', $ep->id) : '#' }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit"
                                onclick="this.disabled=true; this.innerText='Memproses…'; this.form.submit();"
                                class="px-3 py-1.5 rounded bg-amber-600 hover:bg-amber-700 text-xs font-semibold">
                          Buka ({{ (int)($ep->unlock_cost ?? 0) }} koin)
                        </button>
                      </form>
                    </div>

                    <span class="lock-badge inline-flex items-center gap-1.5 px-2 py-1 rounded bg-zinc-700/80 text-gray-300">
                      <svg class="lock-icon" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 11a2 2 0 10-4 0v2a2 2 0 002 2h4a2 2 0 002-2v-2a2 2 0 00-2-2h-4zm0 0V9a4 4 0 118 0v2"/>
                      </svg>
                      Terkunci
                    </span>
                  @endif
                </div>
              </div>
            </li>
          @endforeach
        </ul>

        <div class="pt-3 text-xs opacity-70">{{ $episode->title }}</div>
      </div>
    </div>

    {{-- Modal Pilihan Akses --}}
    <div id="lock-modal" class="modal-mask">
      <div class="modal-card">
        <div class="p-4 flex items-start justify-between">
          <div>
            <div class="text-sm opacity-70">Buka Episode</div>
            <div id="lm-title" class="text-lg font-semibold">—</div>
          </div>
          <button id="lm-close" class="p-2 rounded hover:bg-white/10">✕</button>
        </div>

        <div class="px-4 pb-4 space-y-3">
          <div class="text-xs opacity-70">Pilih salah satu metode akses:</div>

          <div class="flex flex-col sm:flex-row gap-3">
            <a id="lm-ad" href="#" class="btn btn-ghost flex-1 text-center">Tonton Iklan (Gratis)</a>

            <form id="lm-coin-form" method="POST" class="flex-1 hidden">
              @csrf
              <button type="submit" id="lm-coin-btn"
                      onclick="this.disabled=true; this.innerText='Memproses…'; this.form.submit();"
                      class="btn btn-warn w-full">
                Buka dengan koin
              </button>
            </form>
          </div>

          <div id="lm-coin-note" class="text-[11px] opacity-60 hidden">
            Tombol koin disembunyikan karena route <code>series.unlock</code> belum tersedia.
          </div>

          <div class="pt-2 flex justify-end">
            <button id="lm-cancel" class="btn btn-ghost">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Plyr CDN --}}
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css">
  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const videoEl    = document.getElementById('player');
    const sheet      = document.getElementById('episode-sheet');
    const openBtn    = document.getElementById('open-sheet');
    const closeBtn   = document.getElementById('close-sheet');
    const helpBtn    = document.getElementById('help-btn');
    const helpBubble = document.getElementById('help-bubble');
    const invertCheck= document.getElementById('invert-check');

    let player = null;
    try {
      player = new Plyr(videoEl, {
        controls: ['play-large','play','progress','current-time','mute','volume','settings','fullscreen'],
        settings: ['speed'],
        speed: { selected: 1, options: [0.5, 1, 1.25, 1.5, 2] },
        seekTime: 5,
        keyboard: { focused: false, global: false },
        hideControls: false,
        fullscreen: { enabled: true, fallback: true, iosNative: true },
      });
    } catch (e) {
      console.warn('Plyr gagal diinisialisasi:', e);
      player = { currentTime: 0, duration: 0, speed: 1, on(){}, once(){}, get playing(){ return !videoEl.paused; } };
    }

    const INVERT_KEY = 'lokaview:seek_invert';
    let invert = false;
    try { invert = JSON.parse(localStorage.getItem(INVERT_KEY) || 'false'); } catch {}
    if (invertCheck) {
      invertCheck.checked = invert;
      invertCheck.addEventListener('change', () => {
        invert = invertCheck.checked;
        localStorage.setItem(INVERT_KEY, JSON.stringify(invert));
      });
    }

    const fbL = document.getElementById('seek-left-fb');
    const fbR = document.getElementById('seek-right-fb');
    const showFB = (side) => {
      const el = side === 'left' ? fbL : fbR;
      if (!el) return;
      el.classList.add('show'); clearTimeout(el._t);
      el._t = setTimeout(() => el.classList.remove('show'), 300);
    };
    const seekBy = (sec) => {
      const cur = videoEl?.currentTime || 0;
      const dur = videoEl?.duration || (cur + Math.abs(sec));
      if (videoEl) videoEl.currentTime = Math.max(0, Math.min(dur, cur + sec));
    };
    const deltaForSide = (side) => invert ? (side === 'left' ? +10 : -10) : (side === 'left' ? -10 : +10);
    const deltaForKey  = (isLeft) => invert ? (isLeft ? +10 : -10) : (isLeft ? -10 : +10);

    const plyrRoot  = (player?.elements && player.elements.container) || videoEl?.parentElement;
    const videoWrap = plyrRoot?.querySelector?.('.plyr__video-wrapper') || plyrRoot || videoEl;

    if (videoWrap) {
      videoWrap.addEventListener('dblclick', (e) => {
        if (e.target.closest?.('.plyr__controls')) return;
        e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation?.();
        const rect = videoWrap.getBoundingClientRect();
        const side = (e.clientX - rect.left < rect.width / 2) ? 'left' : 'right';
        seekBy(deltaForSide(side)); showFB(side);
      }, { capture: true });

      let lastTap = 0;
      videoWrap.addEventListener('pointerdown', (e) => {
        if (e.target.closest?.('.plyr__controls')) return;
        const now = performance.now();
        if (now - lastTap < 300) {
          e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation?.();
          const rect = videoWrap.getBoundingClientRect();
          const side = (e.clientX - rect.left < rect.width / 2) ? 'left' : 'right';
          seekBy(deltaForSide(side)); showFB(side);
        }
        lastTap = now;
      }, { capture: true });
    }

    const showSheet = () => { sheet?.classList.add('show'); openBtn?.classList?.add('hidden'); };
    const hideSheet = () => { sheet?.classList.remove('show'); openBtn?.classList?.remove('hidden'); };
    player?.on?.('play', () => { if (!window.__firstPlayShown) { window.__firstPlayShown = true; showSheet(); } });
    closeBtn?.addEventListener('click', hideSheet);
    openBtn?.addEventListener('click', showSheet);

    window.addEventListener('keydown', (e) => {
      if (['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) return;
      if (e.key === 'h' || e.key === '?') { e.preventDefault(); helpBubble?.classList.toggle('hidden'); return; }
      const k = e.key;
      if (k === 'ArrowLeft' || k.toLowerCase() === 'j') {
        e.preventDefault(); e.stopPropagation();
        seekBy(deltaForKey(true));  showFB('left');
      } else if (k === 'ArrowRight' || k.toLowerCase() === 'l') {
        e.preventDefault(); e.stopPropagation();
        seekBy(deltaForKey(false)); showFB('right');
      } else if (k === '[') {
        player.speed = Math.max(0.25, (player.speed - 0.25).toFixed ? (player.speed - 0.25).toFixed(2) : 1);
      } else if (k === ']') {
        const sp = parseFloat(player.speed) || 1;
        player.speed = Math.min(2.5, (sp + 0.25).toFixed ? (sp + 0.25).toFixed(2) : 1.25);
      }
    }, { capture: true });

    helpBtn?.addEventListener('click', () => helpBubble?.classList.toggle('hidden'));

    if (videoEl) {
      const STORAGE_KEY = 'lokaview:' + @json($series->id) + ':' + @json($episode->id);
      const saved = localStorage.getItem(STORAGE_KEY);
      if (saved) {
        try {
          const { t } = JSON.parse(saved);
          if (!isNaN(t) && t > 0) {
            if (player?.once) player.once('canplay', () => { videoEl.currentTime = Math.min(t, videoEl.duration || t); });
            else videoEl.currentTime = Math.min(t, videoEl.duration || t);
          }
        } catch {}
      }
      const saveProgress = () => localStorage.setItem(STORAGE_KEY, JSON.stringify({ t: videoEl.currentTime, d: videoEl.duration, ts: Date.now() }));
      videoEl.addEventListener('timeupdate', () => { if (Math.floor(videoEl.currentTime) % 5 === 0) saveProgress(); });
      videoEl.addEventListener('pause', saveProgress);
      window.addEventListener('beforeunload', saveProgress);
    }

    // Modal pilih akses
    const modal   = document.getElementById('lock-modal');
    const lmTitle = document.getElementById('lm-title');
    const lmAd    = document.getElementById('lm-ad');
    const lmForm  = document.getElementById('lm-coin-form');
    const lmBtn   = document.getElementById('lm-coin-btn');
    const lmNote  = document.getElementById('lm-coin-note');
    const lmClose = document.getElementById('lm-close');
    const lmCancel= document.getElementById('lm-cancel');

    function openLockModal({ title, adUrl, canUnlock, unlockUrl, cost }) {
      if (!modal) return;
      if (lmTitle) lmTitle.textContent = title || 'Buka Episode';
      if (lmAd) lmAd.href = adUrl || '#';

      if (canUnlock && unlockUrl && lmForm) {
        lmForm.classList.remove('hidden');
        lmForm.action = unlockUrl;
        if (lmBtn) lmBtn.textContent = cost > 0 ? `Buka dengan ${cost.toLocaleString('id-ID')} koin` : 'Buka dengan koin';
        lmNote?.classList.add('hidden');
      } else {
        lmForm?.classList.add('hidden');
        lmNote?.classList.remove('hidden');
      }
      modal.classList.add('show');
    }
    function closeLockModal(){ modal?.classList.remove('show'); }

    document.addEventListener('click', (e) => {
      const btn = e.target.closest?.('.js-open-lock');
      if (!btn) return;
      e.preventDefault();
      openLockModal({
        title: `Episode ${btn.dataset.epid} — ${btn.dataset.eptitle || ''}`,
        adUrl: btn.dataset.ads,
        canUnlock: btn.dataset.canunlock === '1',
        unlockUrl: btn.dataset.unlock || '',
        cost: parseInt(btn.dataset.cost || '0', 10)
      });
    });

    lmClose?.addEventListener('click', closeLockModal);
    lmCancel?.addEventListener('click', closeLockModal);
    modal?.addEventListener('click', (e) => { if (e.target === modal) closeLockModal(); });
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal?.classList.contains('show')) closeLockModal(); });
  });
  </script>
</x-app-layout>
