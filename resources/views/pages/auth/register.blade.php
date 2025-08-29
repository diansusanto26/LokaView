<x-auth-layout title="Register">
    <div class="mb-[26px]">
        <h1 class="text-2xl font-bold">Register</h1>
        <p class="mt-2">Buat Akun Sekarang dan Nikmati Series Favoritmu!</p>

        <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data" class="mt-8">
            @csrf
          <div>
            <label for="name">Nama Lengkap</label>
            <input id="name" name="name" type="text" placeholder="Masukkan nama lengkap"
              class="bg-input/[.09] w-full p-[18px] focus:outline-none backdrop-blur-xl rounded-full mt-2" />
               @error('name')
                  <p class="mt-1 text-sm">{{ $message }}</p>
              @enderror
          </div>
          <div class="mt-5">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="Masukkan email"
              class="bg-input/[.09] w-full p-[18px] focus:outline-none backdrop-blur-xl rounded-full mt-2" />
              @error('name')
                  <p class="mt-1 text-sm">{{ $message }}</p>
              @enderror
          </div>
          <div class="mt-5">
            <label for="password">Password</label>
            <div class="relative mt-2">
              <input id="password" name="password" type="password" placeholder="Buat kata sandi"
                class="bg-input/[.09] w-full p-[18px] focus:outline-none backdrop-blur-xl rounded-full" />
              <img src="{{ asset('assets/icons/eye-slash.svg') }}" alt="eye-slash-icon"
                class="absolute -translate-y-1/2 cursor-pointer top-1/2 right-5" />
          </div>
            @error('name')
                  <p class="mt-1 text-sm">{{ $message }}</p>
              @enderror
            </div>
            <div class="mt-5">
                <label for="profile_picture">Foto Profil</label>
                <div class="relative mt-2">
                <input id="profile_picture" name="profile_picture" type="file"
                    class="bg-input/[.09] w-full p-[18px] focus:outline-none backdrop-blur-xl rounded-full" />
             @error('profile_picture')
                <p class="mt-1 text-sm">{{ $message }}</p>
            @enderror
  </div>
</div>

          <button type="submit"
            class="font-bold text-[#1F0E0B] bg-secondary w-full py-[14px] rounded-full mt-6 cursor-pointer">
            Daftar
          </button>
        </form>

        <div class="mt-6 flex flex-col gap-4 max-w-[188px] mx-auto">
          <div class="flex items-center gap-2">
            <hr class="grow" />
            <p class="text-xs">Atau daftar dengan</p>
            <hr class="grow" />
          </div>
          <div class="flex justify-center gap-4">
            <button>
              <img src="{{ asset('assets/icons/google.svg') }}" alt="Google icon" />
            </button>
            <button>
              <img src="{{asset('assets/icons/apple.svg')}}" alt="Apple icon" />
            </button>
            <button>
              <img src="{{ asset('assets/icons/facebook.svg') }}" alt="Facebook icon" />
            </button>
          </div>
        </div>

        <p class="mt-8 text-xs text-center">
          Sudah punya akun?
          <a href="{{ route('login') }}" class="underline text-secondary">Masuk di sini.</a>
        </p>
      </div>
</x-auth-layout>