<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/svg+xml" href="/vite.svg" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title }}</title>
  <link rel="stylesheet" href="{{ asset('assets/output.css') }}" />
</head>

<body class="flex justify-center min-h-screen text-light font-montserrat">
  <div
    class="max-w-[760px] relative h-screen w-full bg-[linear-gradient(185.15deg,#060613_1.83%,rgba(6,6,19,0.56)_33.42%,rgba(6,6,19,0)_64.15%)] overflow-hidden">
    <div class="relative z-20 h-full overflow-y-auto mx-[19px] flex items-end">
       {{ $slot }}
    </div>

    <!-- Background Section -->
    <div class="absolute inset-0 w-full h-full object-cover object-top z-0 pointer-events-none"
      style="background-image: url({{ asset('assets/images/The_Judge_from_Hell_poster.jpg') }});">
      <div
        class="w-full h-[120px] bg-[linear-gradient(1.4deg,_#17171F_14.44%,_rgba(23,23,31,0.571899)_59.66%,_rgba(24,24,40,0)_94.74%)] absolute -bottom-[5px]">
      </div>
    </div>

    <div
      class="h-[648px] absolute bottom-0 w-full bg-[linear-gradient(179.97deg,_rgba(19,19,30,0)_-2.04%,_rgba(19,19,30,0.56)_45.1%,_#13131E_99.36%)]">
    </div>

    <div class="w-[139px] h-[139px] -bottom-[54px] -left-[51px] rounded-full bg-[#30009B]/50 blur-3xl absolute"></div>
  </div>
  <script type="module" src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>