<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/svg+xml" href="/vite.svg" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title> {{ $title ?? 'LokaView' }} </title>
  <link rel="stylesheet" href="{{ asset('assets/output.css') }}" />
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>

<body class="flex justify-center min-h-screen overflow-hidden text-light font-montserrat">
  <div class="max-w-[760px] pb-24 h-screen w-full bg-primary overflow-y-auto relative">
    {{ $slot }}

    @if ($showNavbar ?? true)
      <x-navbar/>
    @endif

    
  </div>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script type="module" src="{{ asset('assets/js/main.js') }}?v={{ time() }}"></script>

@stack('scripts')

  
</body>

</html>