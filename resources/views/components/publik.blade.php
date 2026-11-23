@props(['title' => ''])
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }} - Gandrung Sewu</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
[x-cloak]{display:none!important}
.nav-link{transition:color .2s}
.nav-link:hover{color:#8B1E2D}
.nav-mobile{transition:background-color .2s,color .2s}
.nav-mobile:hover{background-color:#F6E7D4;color:#8B1E2D}
.nav-mobile-aktif{background-color:#F6E7D4;color:#8B1E2D;font-weight:600}
</style>
</head>
<body class="bg-[#FBF3EA] text-[#2A1A14] antialiased" style="min-height:100vh; display:flex; flex-direction:column;">
  <x-ornamen class="pointer-events-none fixed -bottom-24 -right-20 -z-10 h-80 w-80 text-[#C9962B]/25" />
<x-ornamen class="pointer-events-none fixed -left-20 top-40 -z-10 hidden h-64 w-64 text-[#C9962B]/20 lg:block" />
<header x-data="{open:false}" class="sticky top-0 z-30 border-b border-[#F0E2D0] bg-[#FFFAF4]/95 backdrop-blur">
  <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4">
    <a href="{{ route('beranda') }}" class="flex items-center gap-2 text-[#8B1E2D]">
      <x-logo class="h-9 w-9"/><span class="judul text-sm font-bold leading-tight">GANDRUNG<br>SEWU</span>
    </a>
    <nav class="hidden items-center gap-7 text-sm lg:flex">
      <a href="{{ route('beranda') }}" class="nav-link py-5 {{ request()->routeIs('beranda') ? 'border-b-2 border-[#8B1E2D] font-semibold text-[#8B1E2D]' : '' }}">Beranda</a>
      <a href="{{ route('tentang') }}" class="nav-link py-5 {{ request()->routeIs('tentang') ? 'border-b-2 border-[#8B1E2D] font-semibold text-[#8B1E2D]' : '' }}">Tentang</a>
      <a href="{{ route('penari.cards') }}" class="nav-link py-5 {{ request()->routeIs('penari.cards') ? 'border-b-2 border-[#8B1E2D] font-semibold text-[#8B1E2D]' : '' }}">Penari Lolos</a>
      <a href="{{ route('kontak') }}" class="nav-link py-5 {{ request()->routeIs('kontak') ? 'border-b-2 border-[#8B1E2D] font-semibold text-[#8B1E2D]' : '' }}">Kontak</a>
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
    <a href="{{ route('beranda') }}" class="nav-mobile rounded-lg px-3 py-3 {{ request()->routeIs('beranda') ? 'nav-mobile-aktif' : '' }}">Beranda</a>
    <a href="{{ route('tentang') }}" class="nav-mobile rounded-lg px-3 py-3 {{ request()->routeIs('tentang') ? 'nav-mobile-aktif' : '' }}">Tentang</a>
    <a href="{{ route('penari.cards') }}" class="nav-mobile rounded-lg px-3 py-3 {{ request()->routeIs('penari.cards') ? 'nav-mobile-aktif' : '' }}">Penari Lolos</a>
    <a href="{{ route('kontak') }}" class="nav-mobile rounded-lg px-3 py-3 {{ request()->routeIs('kontak') ? 'nav-mobile-aktif' : '' }}">Kontak</a>
    <div class="mt-2 flex gap-2">
      @auth <a href="{{ route('dashboard') }}" class="btn flex-1">Dashboard</a>
      @else <a href="{{ route('login') }}" class="btn-o flex-1">Masuk</a><a href="{{ route('register') }}" class="btn flex-1">Daftar Penari</a>
      @endauth
    </div>
  </nav>
</header>
<main style="flex:1 0 auto;">{{ $slot }}</main>
<footer style="margin-top:40px; background-color:#6E1427; color:#F6E7D4;">
  <style>
    .footer-baris { display:flex; align-items:center; justify-content:space-between; gap:16px 24px; flex-wrap:wrap; }
    .footer-menu { display:flex; gap:20px; flex-wrap:wrap; }
    .footer-link { font-size:13px; color:#F6E7D4; opacity:.85; transition:opacity .2s, color .2s; }
    .footer-link:hover { opacity:1; color:#E8C468; }
    @media (max-width:768px) { .footer-baris { flex-direction:column; text-align:center; } .footer-menu { justify-content:center; } }
  </style>

  <div class="mx-auto max-w-6xl px-4" style="padding-top:20px; padding-bottom:16px;">
    <div class="footer-baris">

      {{-- Identitas --}}
      <div style="display:flex; align-items:center; gap:8px; color:#E8C468;">
        <x-logo class="h-7 w-7"/>
        <span class="judul text-sm font-bold">GANDRUNG SEWU</span>
      </div>

      {{-- Menu --}}
      <nav class="footer-menu">
        <a href="{{ route('beranda') }}" class="footer-link">Beranda</a>
        <a href="{{ route('tentang') }}" class="footer-link">Tentang</a>
        <a href="{{ route('penari.cards') }}" class="footer-link">Penari Lolos</a>
        <a href="{{ route('kontak') }}" class="footer-link">Kontak</a>
      </nav>

      {{-- Lokasi --}}
      <p style="font-size:13px; opacity:.85;">Pantai Boom Marina, Banyuwangi</p>

    </div>

    <div style="margin-top:12px; padding-top:10px; border-top:1px solid rgba(201,150,43,.3); text-align:center; font-size:12px; opacity:.7;">
      &copy; {{ date('Y') }} Festival Gandrung Sewu, Banyuwangi
    </div>
  </div>
</footer>
</body>
</html>