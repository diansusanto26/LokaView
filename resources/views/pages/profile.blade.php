<x-app-layout title="Profile">
    <div class="flex mx-[19px] items-center w-fit gap-3 p-1 rounded-full bg-light/[.08] backdrop-blur-[48px]">
      <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" class="object-cover object-center border-2 rounded-full w-9 h-9"
        alt="Profile Picture" />
      <p class="mr-2 font-medium">{{ auth()->user()->name }}</p>
    </div>

    <main class="mx-[19px] mt-6">
      <p class="font-medium">Dompet Saya</p>

      <div class="flex items-center justify-between bg-black/[17%] rounded-[32px] p-2 mt-2">
        <div class="flex items-center gap-1">
          <img src="{{ asset('assets/icons/coin.svg') }}" alt="Coin" />
          <p class="font-bold">{{ auth()->user()->wallet->coin_balance }} Koin</p>
        </div>

        <div class="bg-secondary flex items-center gap-1 p-3 rounded-full">
          <img src="{{ asset('assets/icons/card-plus.svg') }}" alt="card plus" />
          <p class="font-bold text-[#1F0E0B]">Top Up</p>
        </div>
      </div>

      <div class="mt-6">
        <div class="flex items-center justify-between">
          <p class="font-medium">Riwayat Tontonan</p>
          <img src="{{ asset('assets/icons/right-arrow.svg') }}" alt="Right Arrow" />
        </div>
      </div>

      <div class="grid grid-cols-2 mt-4 gap-1 bg-black/[17%] rounded-2xl p-2">
        <div class="p-2 bg-black/[12%] rounded-2xl">
          <div class="relative h-[88px] overflow-hidden rounded-2xl">
            <img src="{{ asset('assets/images/who-missed-1.png') }}" alt="Drama 1" class="object-cover w-full h-full object-center" />
            <div
              class="absolute inset-0 bg-[linear-gradient(0deg,rgba(0,0,0,0.88)_0%,rgba(0,0,0,0.630769)_33.52%,rgba(0,0,0,0)_100%)]">
            </div>
            <div class="absolute bottom-2 right-3 left-3">
              <p class="text-xs">E1</p>
              <div class="h-[2px] rounded-lg w-full bg-secondary"></div>
            </div>
          </div>
          <p class="pt-2 truncate text-sm font-medium">
            Menjadi Milikmu Sela
          </p>
        </div>
        <div class="p-2 bg-black/[12%] rounded-2xl">
          <div class="relative h-[88px] overflow-hidden rounded-2xl">
            <img src="{{ asset('assets/images/top-choice-2.png') }}" alt="Drama 1" class="object-cover w-full h-full object-center" />
            <div
              class="absolute inset-0 bg-[linear-gradient(0deg,rgba(0,0,0,0.88)_0%,rgba(0,0,0,0.630769)_33.52%,rgba(0,0,0,0)_100%)]">
            </div>
            <div class="absolute bottom-2 right-3 left-3">
              <p class="text-xs">E1</p>
              <div class="h-[2px] rounded-lg w-full bg-secondary"></div>
            </div>
          </div>
          <p class="pt-2 truncate text-sm font-medium">Penebusan Cinta</p>
        </div>
      </div>

      <div class="flex z-20 relative flex-col gap-6 mt-6">
        <div class="flex items-center justify-between">
          <div class="flex gap-1">
            <img src="{{ asset('assets/icons/gift-icon.svg') }}" alt="Gift" />
            <p class="font-medium">Dapatkan Hadiah</p>
          </div>
          <img src="{{ asset('assets/icons/right-arrow.svg') }}" alt="Right Arrow" />
        </div>
        <div class="flex items-center justify-between">
          <div class="flex gap-1">
            <img src="{{ asset('assets/icons/help.svg') }}" alt="Help" />
            <p class="font-medium">Layanan Pelanggan</p>
          </div>
          <img src="{{ asset('assets/icons/right-arrow.svg') }}" alt="Right Arrow" />
        </div>
        <div class="flex items-center justify-between">
          <div class="flex gap-1">
            <img src="{{ asset('assets/icons/setting.svg') }}" alt="Setting" />
            <p class="font-medium">Pengaturan</p>
          </div>
          <img src="{{ asset('assets/icons/right-arrow.svg') }}" alt="Right Arrow" />
        </div>
        <a href="#"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="flex items-center justify-between">
            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">
            @csrf
            </form>

            <div class="flex gap-1">
            <img src="{{ asset('assets/icons/logout-svgrepo-com.svg') }}" alt="Logout" />
            <p class="font-medium">Logout</p>
            </div>

            <img src="{{ asset('assets/icons/right-arrow.svg') }}" alt="Right Arrow" />
        </a>

      </div>
    </main>
</x-app-layout>