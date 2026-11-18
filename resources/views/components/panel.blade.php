@props(['title' => '', 'sub' => null])
@php
$u = auth()->user();
$admin = $u->isAdmin();
$menu = $admin ? [
    ['Dashboard', 'admin.dashboard', 'home'],
    ['Data Penari', 'admin.penari', 'users'],
    ['Kuota & Kode', 'admin.kuota', 'key'],
    ['Daftar Kode', 'admin.kode', 'key'],
    ['Template Sertifikat', 'admin.template', 'file'],
    ['Penari Lolos', 'penari.cards', 'grid'],
] : [
    ['Dashboard', 'penari.dashboard', 'home'],
    ['Form Data Diri', 'penari.form', 'edit'],
    ['Penari Lolos', 'penari.cards', 'grid'],
];
@endphp
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
<div x-data="{open:false, keluar:false}" class="min-h-screen">
  <div x-show="open" x-cloak @click="open=false" class="fixed inset-0 z-40 bg-black/40 lg:hidden"></div>

  {{-- Konfirmasi keluar --}}
  <div x-show="keluar" x-cloak @keydown.escape.window="keluar=false" @click.self="keluar=false"
       class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
    <div role="dialog" aria-modal="true" aria-labelledby="judul-keluar" class="w-full max-w-sm rounded-2xl bg-[#FFFAF4] p-6 shadow-xl">
      <h2 id="judul-keluar" class="judul text-xl font-bold">Keluar dari akun?</h2>
      <p class="mt-2 text-sm text-[#6B5A50]">Kamu perlu masuk lagi untuk membuka dashboard. Data yang sudah tersimpan tidak hilang.</p>
      <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <button type="button" @click="keluar=false" class="btn-o">Batal</button>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="btn w-full">Ya, keluar</button></form>
      </div>
    </div>
  </div>

  {{-- Sidebar kiri --}}
  <aside class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col overflow-hidden bg-[#6E1427] text-[#F6E7D4] transition-transform duration-200 lg:translate-x-0"
         :class="open ? 'translate-x-0' : '-translate-x-full'">
    <x-ornamen class="pointer-events-none absolute -bottom-20 -left-20 -z-10 h-64 w-64 text-[#E8C468]/25" />
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-2 px-6 py-7 text-[#E8C468]">
      <x-logo class="h-12 w-12"/>
      <span class="judul text-center text-lg font-bold leading-tight text-white">GANDRUNG<br>SEWU</span>
    </a>
    <nav class="flex-1 space-y-1 overflow-y-auto px-3">
      @foreach ($menu as [$label, $r, $ic])
        <a href="{{ route($r) }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm {{ request()->routeIs($r, $r . '.*') ? 'bg-[#9A2A3E] font-semibold text-white' : 'hover:bg-[#82203A]' }}">
          <x-icon :n="$ic"/>{{ $label }}
        </a>
      @endforeach
    </nav>
    <div class="border-t border-[#82203A] p-3">
      <button type="button" @click="keluar=true" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm hover:bg-[#82203A]"><x-icon n="out"/>Keluar</button>
    </div>
  </aside>

  {{-- Bunga pojok kanan: menempel di layar, di belakang isi, tidak bisa diklik --}}
  <div class="pointer-events-none fixed inset-y-0 left-0 right-0 z-0 overflow-hidden lg:left-64" aria-hidden="true">
    <x-ornamen class="absolute -right-16 top-16 h-64 w-64 text-[#C9962B]/30" />
    <x-ornamen class="absolute -bottom-20 -right-16 h-72 w-72 text-[#C9962B]/30" />
  </div>

  {{-- Isi halaman --}}
  <div class="lg:pl-64">
    <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-[#F0E2D0] bg-[#FFFAF4]/95 px-4 backdrop-blur sm:px-6">
      <button @click="open=true" class="flex h-11 w-11 items-center justify-center rounded-lg text-[#8B1E2D] lg:hidden" aria-label="Buka menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
      <a href="{{ route('profile.edit') }}" title="Pengaturan akun"
         class="ml-auto flex items-center gap-3 rounded-xl px-2 py-1 hover:bg-[#F0E2D0]/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#8B1E2D] {{ request()->routeIs('profile.*') ? 'bg-[#F0E2D0]/60' : '' }}">
        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#8B1E2D] font-semibold text-white">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
        <div class="hidden text-sm leading-tight sm:block"><div class="font-semibold">{{ $u->name }}</div><div class="text-xs text-[#6B5A50]">{{ $admin ? 'Admin' : 'Penari' }}</div></div>
      </a>
    </header>

    <main class="relative z-10 space-y-5 p-4 sm:p-6 lg:p-8">
      <div><h1 class="judul text-2xl font-bold sm:text-3xl">{{ $title }}</h1>@if ($sub)<p class="mt-1 text-sm text-[#6B5A50]">{{ $sub }}</p>@endif</div>
      <x-flash />
      {{ $slot }}
    </main>
  </div>
</div>
</body>
</html>