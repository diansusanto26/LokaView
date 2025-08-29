<section id="trending">
            <div class="flex mx-[19px] items-center justify-between mb-4">
                <h2 class="font-medium">Sedang Trending</h2>
                <a href="#" class="text-xs text-secondary">Lihat lebih banyak</a>
            </div>

            <!-- Slider Trending -->
            <div class="w-full pb-12 mySwiper px-[19px]">
                <div class="swiper-wrapper" style="z-index: 0">
                    @foreach ($series as $item)
                        <a href="{{ route('series.show', $item->slug) }}" class="swiper-slide relative flex-shrink-0 overflow-hidden rounded-3xl">
                        <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="Drama 1" class="object-cover object-center w-full h-full" />
                        <div
                            class="absolute flex items-center bg-black/[66%] gap-1 px-[11px] py-2 rounded-full top-2 right-2 backdrop-blur-[42px]">
                            <img src="{{ asset('assets/icons/star.svg') }}" alt="star" class="w-[18px] h-[18px]" />
                            <span class="text-lg leading-[100%] tracking-[2%]">8.0</span>
                        </div>
                        <div
                            class="absolute bottom-0 h-[88px] rounded-3xl w-full bg-[linear-gradient(179.58deg,_rgba(0,0,0,0)_-6.88%,_rgba(0,0,0,0.379452)_26.04%,_rgba(0,0,0,0.57)_66.3%)] backdrop-blur-[10px] px-[11px] py-4">
                            <p class="text-2xl font-semibold tracking-[-0.54px]">
                                {{ Str::limit($item->title, 15) }}
                            </p>
                            <p class="font-medium tracking-[-0.54px]">{{ $item->genre->name }}</p>
                        </div>
                    </a>
                    @endforeach
                    
                   
                    
                </div>
            </div>
        </section>
