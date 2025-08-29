<section class="relative">
            <div id="slider-hero"
                class="flex overflow-x-auto rounded-[19px] shadow-lg aspect-video snap-x snap-mandatory scroll-smooth">
                @foreach ($banners as $banner)
    <div id="slide-hero-{{ $loop->iteration }}"
        class="relative flex-shrink-0 w-full overflow-hidden snap-start">
        <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner Image"
            class="object-cover object-center w-full h-auto" />
        <div class="absolute bottom-[26px] z-10 left-[9.5px]">
            <p class="font-semibold text-[19px]">
                {{ $banner->title }}
            </p>

            <a href="{{ $banner->link }}"
                class="flex items-center gap-1 mt-2 p-[5px] bg-black/[37%] w-fit rounded-full">
                <img src="{{ asset('assets/icons/play.svg') }}" alt="" />
                <p class="mr-1">Tonton Sekarang</p>
            </a>
        </div>
    </div>
@endforeach

            </div>
            <div id="slider-hero-nav"
                class="absolute z-10 flex gap-[2.48px] p-1 bg-[#282828]/[17%] backdrop-blur-[29px] rounded-full top-3 right-0.5">
                 @foreach ($banners as $banner)
                <button data-slide="{{ $loop->iteration }}"
                    class="h-[5px] w-[5px] bg-white transition-all duration-300 rounded-full opacity-75 hover:opacity-100 cursor-pointer"></button>
                @endforeach
            </div>

            <div
                class="absolute w-full h-[135px] bg-[linear-gradient(178.6deg,_rgba(20,19,33,0)_12.03%,_#141321_92.52%)] -bottom-[5px]">
            </div>
        </section>