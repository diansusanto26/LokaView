<x-app-layout :title="$series->title" :showNavbar="false">
    <div class="h-[560px] relative">
      <div class="absolute top-[38px] right-4 left-4 flex justify-between z-20">
        <a href="{{ route('home') }}" class="block p-[7px] bg-black/[15%] rounded-full backdrop-blur-[48px]">
          <img src="{{ asset('assets/icons/back.svg') }}" class="w-8 h-8" alt="back-icon" />
        </a>

        <button class="cursor-pointer p-[7px] bg-black/[15%] rounded-full backdrop-blur-[48px]">
          <img src="{{ asset('assets/icons/bookmark.svg') }}" class="w-8 h-8" alt="bookmark" />
        </button>
      </div>
      <img src="{{ asset('storage/' . $series->thumbnail) }}" class="w-full h-full object-cover object-center" alt="trending-1" />
      <a href="{{ route('series.play', [$series->slug, $series->episodes->first()->id]) }}"
        class="bg-white/[12%] z-40 backdrop-blur-[10px] absolute p-[11px] rounded-full top-[221px] left-1/2 -translate-x-1/2 hover:animate-pulse">
        <img src="{{ asset('assets/icons/play.svg') }}" alt="play" class="w-[53px] h-[53px]" />
      </a>
      <div
        class="w-full bottom-0 h-[314px] bg-[linear-gradient(0.81deg,_#1A1A23_2.07%,_rgba(26,26,36,0.92)_23.95%,_rgba(26,26,36,0.57)_59.15%,_rgba(26,26,35,0)_95.01%)] absolute">
      </div>
    </div>

    <div class="relative z-10 -top-[129px] px-[16px] mx-auto -mb-[113px]">
      <h1 class="font-semibold text-[28px] tracking-[-0.41px]">
        {{ $series->title }}
      </h1>

      <div class="flex gap-1 mt-4">
        <div class="flex items-center justify-between gap-1 py-[6px] px-2 bg-[#3B3B3B]/[19%] rounded-full">
          <img src="{{ asset('assets/icons/age.svg') }}" alt="age-icon" class="cursor-pointer" />
          <span class="text-sm font-semibold">{{ $series->age_rating }}</span>
        </div>
        <div class="flex items-center justify-between gap-1 py-[6px] px-2 bg-[#3B3B3B]/[19%] rounded-full">
          <img src="{{ asset('assets/icons/heartbeat.svg') }}" alt="heart-icon" class="cursor-pointer" />
          <span class="text-sm">{{ $series->genre->name }}</span>
        </div>
        <div class="flex items-center justify-between gap-1 py-[6px] px-2 bg-[#3B3B3B]/[19%] rounded-full">
          <img src="{{ asset('assets/icons/calendar.svg') }}" alt="calendar-icon" class="cursor-pointer" />
          <span class="text-sm">{{ $series->release_year }}</span>
        </div>
        <div class="flex items-center justify-between gap-1 py-[6px] px-2 bg-[#3B3B3B]/[19%] rounded-full">
          <img src="{{ asset('assets/icons/film-slate.svg') }}" alt="film-slate-icon" class="cursor-pointer" />
          <span class="text-sm">{{ $series->episodes_count }} Episode</span>
        </div>
      </div>

      <div class="mt-4 text-sm leading-5">
        {!! $series->description !!}
      </div>

      <section class="mt-6">
    <h2>Rekomendasi Serupa</h2>
    <div class="flex flex-col">
        @foreach ($relatedSeries as $item)
            <a href="{{ route('series.show', $item->slug) }}"
                class="p-2 bg-light/[2%] rounded-lg flex gap-[14px] mt-4">
                <div class="w-[116px] h-28 overflow-hidden rounded-lg shrink-0">
                    <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="Popular Search 1"
                        class="w-full h-full object-cover object-center" />
                </div>
                <div class="flex flex-col justify-between flex-1 min-w-0">
                    <h3 class="font-semibold">{{ $item->title }}</h3>
                    <p class="text-sm line-clamp-3">
                        {{ Str::limit(strip_tags($item->description), 100) }}
                    </p>
                    <span class="text-sm font-semibold text-secondary">{{ $item->episodes_count }}
                        Episode</span>
                </div>
            </a>
        @endforeach
    </div>
</section>

    </div>

    <!-- Background Section -->
    <div
      class="absolute bottom-0 w-full bg-[linear-gradient(179.97deg,_rgba(19,19,30,0)_-2.04%,_rgba(19,19,30,0.56)_45.1%,_#13131E_99.36%)]">
    </div>

    <div class="w-[139px] h-[139px] -bottom-[41px] -left-[23px] rounded-full bg-[#30009B]/50 blur-3xl absolute"></div>
</x-app-layout>