<nav class="flex justify-center">
    <ul class="flex px-[10px] py-[9px]  bg-black/70 backdrop-blur-2xl w-fit gap-2 rounded-full mt-8 fixed bottom-[22px]">
        <li>
            @if (Route::is('home'))
            <a href="#"
                class="flex items-center gap-1 px-6 py-2 rounded-full bg-secondary">
                <img class="w-6 h-6 scale-90" src="{{ asset('assets/icons/home-active.svg') }}" alt="Home Icon" />
                <span class="leading-8 text-black font-semibold tracking-[-0.41px]">Home</span>
            </a>
            @else
                <a href="{{ route('home') }}"
                class="flex flex-col items-center bg-light/[.14] rounded-full p-[13px]">
                <img class="w-6 h-6" src="{{ asset('assets/icons/home.svg') }}" alt="Home Icon" />
            </a>
            @endif

        </li>

        <li>
            <a href="#" class="flex flex-col items-center bg-light/[14%] rounded-full p-[13px]">
                <img class="w-6 h-6 scale-125" src="{{ asset('assets/icons/bell.svg') }}" alt="Notification Icon" />
            </a>
        </li>

        <li>
            <a href="/my-list.html" class="flex flex-col items-center bg-light/[14%] rounded-full p-[13px]">
                <img class="w-6 h-6" src="{{ asset('assets/icons/bookmark.svg') }}" alt="Bookmark Icon" />
            </a>
        </li>

        <li>
            @if (Route::is('profile'))
            <a href="#"
                class="flex items-center gap-1 px-6 py-2 rounded-full bg-secondary">
                <img class="w-6 h-6 scale-90" src="{{ asset('assets/icons/profile-active.svg') }}" alt="Profile Icon" />
                <span class="leading-8 text-black font-semibold tracking-[-0.41px]">Profile</span>
            </a>
            @else
            <a href="{{ route('profile') }}"
                class="flex flex-col items-center bg-light/[.14] rounded-full p-[13px]">
                <img class="w-6 h-6" src="{{ asset('assets/icons/user.svg') }}" alt="Profile Icon" />
            </a>
            @endif

        </li>
    </ul>
</nav>