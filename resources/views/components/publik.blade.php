@props(['title' => ''])
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }} - Gandrung Sewu</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-[#FBF3EA] text-[#2A1A14] antialiased">
<header x-data="{open:false}" class="sticky top-0 z-30 border-b border-[#F0E2D0] bg-[#FFFAF4]/95 backdrop-blur">
  <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4">
    <a href="{{ route('beranda') }}" class="flex items-center gap-2 text-[#8B1E2D]">
      <x-logo class="h-9 w-9"/><span class="judul text-sm font-bold leading-tight">GANDRUNG<br>SEWU</span>
    </a>
    <nav class="hidden items-center gap-7 text-sm lg:flex">
      <a href="{{ route('beranda') }}" class="py-5 {{ request()->routeIs('beranda') ? 'border-b-2 border-[#8B1E2D] font-semibold text-[#8B1E2D]' : '' }}">Beranda</a>
      <a href="{{ route('beranda') }}#tentang" class="py-5">Tentang</a>
      <a href="{{ route('penari.cards') }}" class="py-5 {{ request()->routeIs('penari.cards') ? 'border-b-2 border-[#8B1E2D] font-semibold text-[#8B1E2D]' : '' }}">Penari Lolos</a>
      <a href="{{ route('beranda') }}#kontak" class="py-5">Kontak</a>
    </nav>
    <div class="hidden items-center gap-2 lg:flex">
      @auth <a href="{{ route('dashboard') }}" class="btn btn-s">Dashboard</a>
      @else <a href="{{ route('login') }}" class="btn-o btn-s">Masuk</a><a href="{{ route('register') }}" class="btn btn-s">Daftar Penari</a>
      @endauth
    </div>
    <button @click="open=!open" class="flex h-11 w-11 items-center justify-center rounded-lg text-[#8B1E2D] lg:hidden" aria-label="Buka menu" :aria-expanded="open">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
  </div>
  <nav x-show="open" x-cloak class="flex flex-col gap-1 border-t border-[#F0E2D0] px-4 py-3 text-sm lg:hidden">
    <a href="{{ route('beranda') }}" class="rounded-lg px-3 py-3">Beranda</a>
    <a href="{{ route('beranda') }}#tentang" class="rounded-lg px-3 py-3">Tentang</a>
    <a href="{{ route('penari.cards') }}" class="rounded-lg px-3 py-3">Penari Lolos</a>
    <a href="{{ route('beranda') }}#kontak" class="rounded-lg px-3 py-3">Kontak</a>
    <div class="mt-2 flex gap-2">
      @auth <a href="{{ route('dashboard') }}" class="btn flex-1">Dashboard</a>
      @else <a href="{{ route('login') }}" class="btn-o flex-1">Masuk</a><a href="{{ route('register') }}" class="btn flex-1">Daftar Penari</a>
      @endauth
    </div>
  </nav>
</header>
{{ $slot }}
<footer class="mt-10 border-t border-[#F0E2D0] py-6 text-center text-sm text-[#6B5A50]">Festival Gandrung Sewu, Banyuwangi</footer>
</body>
</html>