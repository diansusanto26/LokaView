{{-- resources/views/series/play.blade.php --}}
<x-app-layout :title="$episode->title ?? 'Play'" :showNavbar="false">
  <canvas id="blur-canvas" class="absolute inset-0 h-full w-full"></canvas>
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

  <style>
    :root{
      --safe-right: env(safe-area-inset-right, 16px);
      --safe-left:  env(safe-area-inset-left, 16px);
      --safe-bottom: env(safe-area-inset-bottom, 16px);
    }
    .right-safe{ right: var(--safe-right); }
    .left-safe{ left: var(--safe-left); }
    .bottom-safe{ bottom: calc(16px + var(--safe-bottom)); }

    .app-header{ position:absolute; inset-inline:1rem; top:38px; z-index:40; transition:opacity .2s ease; }

    /* Matikan tooltip mengambang Plyr */
    .plyr__progress__tooltip, .plyr__tooltip { display:none !important; }

    /* Fallback paksa landscape via CSS */
    .force-landscape{
      position:fixed !important; inset:0 !important;
      width:100vh !important; height:100vw !important;
      transform:rotate(90deg) translateY(-100%); transform-origin:top left;
      background:#000; z-index:60;
    }
    .force-landscape video, .force-landscape .plyr{
      width:100vh !important; height:100vw !important; object-fit:cover !important;
    }

    /* (Opsional) sembunyikan big-play saat portrait */
    @media (orientation: portrait){
      .plyr__control--overlaid{ display:none !important; }
    }

    /* Auto-hide UI */
    .ui-hidden .app-header,
    .ui-hidden #rotate-btn { opacity: 0; pointer-events: none; }
    .ui-visible .app-header,
    .ui-visible #rotate-btn { opacity: 1; pointer-events: auto; }

    /* Tap-zones (kontainer tembus; bottom di-set dinamis via JS) */
    .tap-zones{
      display:none; position:absolute; left:0; right:0; top:0; z-index:35; pointer-events:none;
    }
    .seek-zones-on .tap-zones{ display:flex; justify-content:space-between; }
    .tap-left, .tap-right{
      pointer-events:auto; width:40%; height:100%;
      touch-action: manipulation;
    }

    /* Feedback +10 / −10 */
    .seek-feedback{
      position:absolute; top:50%; transform:translateY(-50%) scale(.9);
      padding:10px 14px; border-radius:9999px;
      font-weight:700; color:#fff; background:rgba(0,0,0,.45);
      box-shadow:0 4px 16px rgba(0,0,0,.35);
      opacity:0; pointer-events:none; z-index:45;
    }
    .seek-feedback.left{ left:10%; }
    .seek-feedback.right{ right:10%; }
    .seek-feedback.show{ animation: seek-pop 600ms ease-out; }
    @keyframes seek-pop{
      0%{opacity:0; transform:translateY(-50%) scale(.85);}
      15%{opacity:1; transform:translateY(-50%) scale(1);}
      85%{opacity:1; transform:translateY(-50%) scale(1);}
      100%{opacity:0; transform:translateY(-60%) scale(.92);}
    }
  </style>

  {{-- Header --}}
  <div class="app-header flex items-center gap-3">
    <a href="{{ route('home') }}" class="block p-[7px] bg-black/15 rounded-full backdrop-blur-[48px] w-fit">
      <img src="{{ asset('assets/icons/back.svg') }}" class="w-8 h-8" alt="back"/>
    </a>
    <h1 class="font-semibold tracking-[-0.41px] line-clamp-1">{{ $episode->title }}</h1>
    <div class="ml-auto flex items-center gap-2">
      <button id="list-episode" class="bg-black/10 p-3 rounded-full hover:animate-pulse">
        <img src="{{ asset('assets/icons/list-episode.svg') }}" class="w-8 h-8" alt="list"/>
      </button>
      <button class="bg-black/10 p-3 rounded-full hover:animate-pulse">
        <img src="{{ asset('assets/icons/bookmark.svg') }}" class="w-8 h-8" alt="bookmark"/>
      </button>
    </div>
  </div>

  {{-- Video + tap zones + feedback --}}
  <div id="video-wrap" class="absolute inset-0 z-10 ui-visible" data-episode-id="{{ $episode->id }}">
    <video
      id="player"
      class="plyr w-full h-auto max-h-full object-contain"
      playsinline
      crossorigin="anonymous"
      poster="{{ $episode->thumbnail ? asset('storage/' . $episode->thumbnail) : '' }}"
    >
      <source
        src="{{ asset('storage/' . $episode->video) }}"
        type="{{ \Illuminate\Support\Str::endsWith($episode->video, '.m3u8') ? 'application/x-mpegURL' : 'video/mp4' }}"
      />
    </video>

    <div class="tap-zones" aria-hidden="true">
      <div class="tap-left"></div>
      <div class="tap-right"></div>
    </div>

    <div id="seek-feedback-left" class="seek-feedback left">−10</div>
    <div id="seek-feedback-right" class="seek-feedback right">+10</div>
  </div>

  {{-- Tombol Rotate --}}
  <button
    id="rotate-btn"
    class="fixed z-50 bottom-safe right-safe p-3 rounded-full bg-black/40 hover:bg-black/60 backdrop-blur-md pointer-events-auto"
    title="Rotate (landscape)" aria-label="Rotate (landscape)" data-rotate-state="portrait"
  >
    <img id="rotate-icon" src="{{ asset('assets/icons/rotate.svg') }}" class="w-7 h-7 pointer-events-none" alt="rotate"/>
  </button>

  <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.15/dist/hls.min.js"></script>
  <script>
    /* ===== Konstanta & util ===== */
    const SKIP_SECONDS = 10;
    const wait = (ms) => new Promise(r => setTimeout(r, ms));
    function clampSeek(t, dur){ const eps=0.25; return Math.min(Math.max(t,0), Math.max((dur||0)-eps,0)); }
    function stopAll(e){ e.preventDefault(); e.stopPropagation(); if(e.stopImmediatePropagation) e.stopImmediatePropagation(); }

    /* ===== Elemen & Plyr ===== */
    const wrap = document.getElementById('video-wrap');
    const videoEl = document.getElementById('player');
    const tapZones = wrap.querySelector('.tap-zones');
    const leftZone = wrap.querySelector('.tap-left');
    const rightZone = wrap.querySelector('.tap-right');
    const fbLeft = document.getElementById('seek-feedback-left');
    const fbRight = document.getElementById('seek-feedback-right');

    const src = videoEl.querySelector('source')?.getAttribute('src') || '';
    const isHls = src.endsWith('.m3u8');

    const player = new Plyr(videoEl, {
      controls: ['play-large','play','progress','current-time','duration','mute','volume','settings','pip','airplay','fullscreen'],
      tooltips: { controls: false, seek: false },
      displayDuration: false, invertTime: false, ratio: '16:9',
      clickToPlay: true, autopause: true, autoplay: false, hideControls: false,
      keyboard: { focused: true, global: true },
    });
    window.player = player;

    if (isHls && window.Hls && Hls.isSupported()){
      const hls = new Hls(); hls.loadSource(src); hls.attachMedia(videoEl); window._hls = hls;
    } else if (isHls && videoEl.canPlayType('application/vnd.apple.mpegurl')) {
      videoEl.src = src;
    }

    /* ===== Snapshot & Restore playback (anti reset saat rotate/fs) ===== */
    let snap = { time: 0, wasPlaying: false, rate: 1, valid: false };
    function snapshotPlayback(){ snap.time=Number(videoEl.currentTime)||0; snap.wasPlaying=!videoEl.paused; snap.rate=Number(videoEl.playbackRate)||1; snap.valid=true; }
    async function restorePlayback(retries=8){
      if(!snap.valid) return;
      for(let i=0;i<retries;i++){ if(Number.isFinite(videoEl.duration)&&videoEl.duration>0) break; await wait(50*(i+1)); }
      const dur=Number(videoEl.duration); if(!Number.isFinite(dur)||dur<=0) return;
      const target=clampSeek(snap.time, dur);
      try{ if(typeof videoEl.fastSeek==='function'){ videoEl.fastSeek(target); await wait(40);} }catch(_){}
      try{ videoEl.currentTime = target; }catch(_){}
      if (snap.wasPlaying){ try{ await videoEl.play(); }catch(_){ } }
      snap.valid=false; videoEl.dispatchEvent(new Event('timeupdate'));
    }

    /* ===== Rotate (fullscreen + lock/fallback) ===== */
    let forcedCSSLandscape=false;
    const rotateBtn=document.getElementById('rotate-btn');
    const rotateIcon=document.getElementById('rotate-icon');

    function isLandscapeActive(){ return (screen.orientation && (screen.orientation.type||'').startsWith('landscape')) || forcedCSSLandscape; }
    function setRotateUI(isLand){
      if(isLand){ rotateIcon.src="{{ asset('assets/icons/rotate-back.svg') }}"; rotateBtn.title="Kembali ke portrait"; rotateBtn.dataset.rotateState='landscape'; }
      else      { rotateIcon.src="{{ asset('assets/icons/rotate.svg') }}";      rotateBtn.title="Rotate (landscape)";    rotateBtn.dataset.rotateState='portrait'; }
    }
    async function enterFullscreen(el){ if(!document.fullscreenElement && el.requestFullscreen) await el.requestFullscreen(); }

    async function toggleRotate(){
      snapshotPlayback();
      const onLand=isLandscapeActive();
      if(!onLand){
        await enterFullscreen(wrap).catch(()=>{});
        if(screen.orientation && screen.orientation.lock){
          try{ await screen.orientation.lock('landscape'); }
          catch(_){ wrap.classList.add('force-landscape'); forcedCSSLandscape=true; }
        }else{ wrap.classList.add('force-landscape'); forcedCSSLandscape=true; }
        setRotateUI(true); enableSeekZones(true);
      }else{
        if(screen.orientation && screen.orientation.lock){ try{ await screen.orientation.lock('portrait-primary'); }catch(_){} }
        if(forcedCSSLandscape){ wrap.classList.remove('force-landscape'); forcedCSSLandscape=false; }
        setRotateUI(false); enableSeekZones(false);
      }
      await wait(90); restorePlayback();
    }
    rotateBtn.addEventListener('click', toggleRotate);

    window.addEventListener('orientationchange', async()=>{ snapshotPlayback(); await wait(120); restorePlayback(); });
    document.addEventListener('fullscreenchange', async()=>{
      const active=!!document.fullscreenElement || isLandscapeActive();
      enableSeekZones(active);
      if(!document.fullscreenElement){ wrap.classList.remove('force-landscape'); forcedCSSLandscape=false; setRotateUI(false); }
      else{ setRotateUI(isLandscapeActive()); }
      await wait(60); restorePlayback();
    });

    /* ===== Auto-hide UI ===== */
    let idleTimer=null; const IDLE_MS=2500;
    function showUI(){ wrap.classList.add('ui-visible'); wrap.classList.remove('ui-hidden'); resetIdle(); }
    function hideUI(){ wrap.classList.add('ui-hidden'); wrap.classList.remove('ui-visible'); }
    function resetIdle(){ if(idleTimer) clearTimeout(idleTimer); idleTimer=setTimeout(()=>{ if(document.fullscreenElement||isLandscapeActive()) hideUI(); },IDLE_MS); }
    ['mousemove','touchstart','touchmove','keydown'].forEach(evt=>document.addEventListener(evt,showUI,{passive:true}));
    videoEl.addEventListener('play', resetIdle); videoEl.addEventListener('pause', showUI);

    /* ===== Tap-zones bottom dinamis (agar kontrol Plyr tetap klik) ===== */
    function adjustTapZonesBottom(){
      const controls = player?.elements?.controls;
      const h = controls ? controls.clientHeight : 0;
      tapZones.style.bottom = (h + 8) + 'px'; // buffer 8px
    }
    player.on('ready', adjustTapZonesBottom);
    player.on('loadedmetadata', adjustTapZonesBottom);
    window.addEventListener('resize', adjustTapZonesBottom);

    /* ===== SEEK “hard”: plyr → fastSeek → force currentTime + retry ===== */
    async function seekTo(target){
      const media = player?.media || videoEl;
      const dur = Number(media.duration);
      if (!Number.isFinite(dur) || dur <= 0) return;

      const t = clampSeek(Number(target)||0, dur);

      for (let attempt=0; attempt<2; attempt++){
        try{ if(typeof media.fastSeek==='function'){ media.fastSeek(t); await wait(40); } }catch(_){}
        try{ media.currentTime = t; }catch(_){}
        try{ if (player?.media && player.media !== media) player.media.currentTime = t; }catch(_){}
        try{ media.dispatchEvent(new Event('timeupdate')); }catch(_){}
        await wait(20);
      }
    }

    async function seekBy(sec){
      const media = player?.media || videoEl;
      const delta = Number(sec||0); if(!delta) return;
      const dur = Number(media.duration); if (!Number.isFinite(dur) || dur <= 0) return;

      const before = Number(media.currentTime) || 0;
      const target = clampSeek(before + delta, dur);

      // 1) API Plyr
      try{ if(delta>0 && player?.forward) player.forward(Math.abs(delta));
           else if(delta<0 && player?.rewind) player.rewind(Math.abs(delta)); }catch(_){}
      await wait(60);

      const now = Number(media.currentTime)||0;
      if (Math.abs(now - target) > 0.2){
        // 2/3) Hard seek dengan retry & sinkron
        await seekTo(target);

        // verifikasi sekali lagi, kalau masih belum, paksa lagi
        const after = Number(media.currentTime)||0;
        if (Math.abs(after - target) > 0.2){
          try{ media.currentTime = target; }catch(_){}
          try{ if (player?.media && player.media !== media) player.media.currentTime = target; }catch(_){}
          try{ media.dispatchEvent(new Event('timeupdate')); }catch(_){}
        }
      }
    }

    function flash(el,txt){ if(!el) return; el.textContent=txt; el.classList.remove('show'); void el.offsetWidth; el.classList.add('show'); }

    /* ===== Double-tap via overlay (MOBILE) ===== */
    let lastTapLeft=0, lastTapRight=0;
    leftZone.addEventListener('touchend',(e)=>{ stopAll(e); const now=Date.now(); if(now-lastTapLeft<300){ seekBy(-SKIP_SECONDS); flash(fbLeft,'−'+SKIP_SECONDS); } lastTapLeft=now; }, {passive:false});
    rightZone.addEventListener('touchend',(e)=>{ stopAll(e); const now=Date.now(); if(now-lastTapRight<300){ seekBy(+SKIP_SECONDS);  flash(fbRight,'+'+SKIP_SECONDS); } lastTapRight=now; }, {passive:false});
    // (opsional desktop) dblclick pada zona
    leftZone.addEventListener('dblclick',(e)=>{ stopAll(e); seekBy(-SKIP_SECONDS); flash(fbLeft,'−'+SKIP_SECONDS); }, true);
    rightZone.addEventListener('dblclick',(e)=>{ stopAll(e); seekBy(+SKIP_SECONDS);  flash(fbRight,'+'+SKIP_SECONDS); }, true);

    /* ===== Double-tap langsung di <video> (backup) ===== */
    let lastVideoTap=0;
    videoEl.addEventListener('touchend',(e)=>{
      if(!document.fullscreenElement && !isLandscapeActive()) return;
      if(e.changedTouches && e.changedTouches.length){
        const now=Date.now(), dt=now-lastVideoTap; lastVideoTap=now;
        if(dt<300){
          stopAll(e);
          const rect=videoEl.getBoundingClientRect();
          const x=e.changedTouches[0].clientX - rect.left;
          if(x<rect.width/2){ seekBy(-SKIP_SECONDS); flash(fbLeft,'−'+SKIP_SECONDS); }
          else{ seekBy(+SKIP_SECONDS); flash(fbRight,'+'+SKIP_SECONDS); }
        }
      }
    }, {passive:false});

    /* ===== Override dblclick fullscreen Plyr (DESKTOP, capture) ===== */
    // ===== Super intercept: ubah semua dblclick di area player jadi SEEK, bukan fullscreen
function bindDblclickSeekOnly(){
  const container = player?.elements?.container || wrap;
  if (!container) return;

  // Helper: true kalau event terjadi di area video tapi bukan di toolbar kontrol
  const inVideoArea = (target) => {
    if (!container.contains(target)) return false;
    if (target.closest('.plyr__controls')) return false;
    return true;
  };

  // 1) Tangkap di WINDOW dulu supaya mendahului handler internal Plyr
  window.addEventListener('dblclick', (e) => {
    if (!inVideoArea(e.target)) return;
    // HANYA aktif saat mode nonton (fullscreen/landscape),
    // supaya dblclick di portrait tidak mengganggu
    if (!document.fullscreenElement && !isLandscapeActive()) return;

    e.preventDefault();
    e.stopPropagation();
    if (e.stopImmediatePropagation) e.stopImmediatePropagation();

    const rect = container.getBoundingClientRect();
    const x = e.clientX - rect.left;
    if (x < rect.width / 2) { seekBy(-10); flash(document.getElementById('seek-feedback-left'),  '−10'); }
    else                    { seekBy(+10); flash(document.getElementById('seek-feedback-right'), '+10'); }
  }, true); // capture=true

  // 2) Redundan—blokir dblclick di container & video (jaga-jaga)
  const block = (ev) => {
    if (!inVideoArea(ev.target)) return;
    ev.preventDefault();
    ev.stopPropagation();
    if (ev.stopImmediatePropagation) ev.stopImmediatePropagation();
  };
  container.addEventListener('dblclick', block, true);
  videoEl.addEventListener('dblclick',   block, true);
}
bindDblclickSeekOnly();


    /* ===== Enable/disable tap-zones ===== */
    function enableSeekZones(on){
      if(on){ wrap.classList.add('seek-zones-on'); adjustTapZonesBottom(); }
      else  { wrap.classList.remove('seek-zones-on'); }
    }

    /* ===== Resume posisi (localStorage) ===== */
    const episodeId=wrap.dataset.episodeId||'unknown'; const LS_KEY=`watch:${episodeId}`;
    videoEl.addEventListener('loadedmetadata',()=>{ try{ const saved=JSON.parse(localStorage.getItem(LS_KEY)||'0')||0; if(Number.isFinite(saved)&&saved>10&&Number.isFinite(videoEl.duration)&&saved<videoEl.duration-10){ videoEl.currentTime=saved; } }catch(_){} enableSeekZones(!!document.fullscreenElement||isLandscapeActive()); });
    let saveTick=null;
    videoEl.addEventListener('play',()=>{ if(saveTick) clearInterval(saveTick); saveTick=setInterval(()=>{ try{ localStorage.setItem(LS_KEY,JSON.stringify(Math.floor(videoEl.currentTime||0))); }catch(_){ } },5000); });
    ['pause','seeked','timeupdate'].forEach(ev=>{ videoEl.addEventListener(ev,()=>{ try{ localStorage.setItem(LS_KEY,JSON.stringify(Math.floor(videoEl.currentTime||0))); }catch(_){ } }); });
    videoEl.addEventListener('ended',()=>{ try{ localStorage.removeItem(LS_KEY); }catch(_){} });

    // Init
    setRotateUI(isLandscapeActive()); resetIdle();
  </script>
</x-app-layout>
