@props(['title' => '', 'sub' => null])
@php
$u = auth()->user();
$admin = $u->isAdmin();
$grup = $admin ? [
    ['Utama', [['Dashboard', 'admin.dashboard', 'home']]],
    ['Kuota dan kode', [['Kuota & Kode', 'admin.kuota', 'key'], ['Daftar Kode', 'admin.kode', 'list']]],
    ['Data', [['Data Penari', 'admin.penari', 'users'], ['Penari Lolos', 'penari.cards', 'grid']]],
    ['Sertifikat', [['Template Sertifikat', 'admin.template', 'file']]],
] : [
    ['Utama', [['Dashboard', 'penari.dashboard', 'home']]],
    ['Pendaftaran', [['Form Data Diri', 'penari.form', 'edit'], ['Penari Lolos', 'penari.cards', 'grid']]],
];
@endphp
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }} - Gandrung Sewu</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-[#FBF3EA] text-[#2A1A14] antialiased">
<div x-data="{open:false, keluar:false}" class="min-h-screen">
  <div x-show="open" x-cloak @click="open=false" class="fixed inset-0 z-40 bg-black/40 lg:hidden"></div>

  {{-- Konfirmasi keluar --}}
  <div x-show="keluar" x-cloak @keydown.escape.window="keluar=false" @click.self="keluar=false"
       class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
    <div role="dialog" aria-modal="true" aria-labelledby="judul-keluar" class="w-full max-w-sm rounded-2xl bg-[#FFFCF8] p-6 shadow-xl">
      <h2 id="judul-keluar" class="judul text-xl font-bold">Keluar dari akun?</h2>
      <p class="mt-2 text-sm text-[#6B5A50]">Kamu perlu masuk lagi untuk membuka dashboard. Data yang sudah tersimpan tidak hilang.</p>
      <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <button type="button" @click="keluar=false" class="btn-o">Batal</button>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="btn w-full">Ya, keluar</button></form>
      </div>
    </div>
  </div>

  {{-- Sidebar --}}
  <aside class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col overflow-hidden bg-gradient-to-b from-[#6E1427] to-[#560E1E] text-[#F6E7D4] transition-transform duration-200 lg:translate-x-0"
         :class="open ? 'translate-x-0' : '-translate-x-full'">
    <x-ornamen class="pointer-events-none absolute -bottom-20 -left-20 -z-10 h-64 w-64 text-[#E8C468]/20" />
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-6 py-6">
      <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-[#E8C468]"><x-logo class="h-8 w-8"/></span>
      <span class="judul text-lg font-bold leading-tight text-white">Gandrung Sewu<span class="block text-xs font-normal text-[#E5C8CF]">{{ $admin ? 'Panel Admin' : 'Portal Penari' }}</span></span>
    </a>

    <nav class="flex-1 overflow-y-auto px-3 pb-4" aria-label="Menu utama">
      @foreach ($grup as [$namaGrup, $butir])
        <div class="px-3.5 pb-1.5 pt-5 text-xs font-semibold text-[#D8A9B3]">{{ $namaGrup }}</div>
        <div class="space-y-1">
          @foreach ($butir as [$label, $r, $ic])
            @php $aktif = request()->routeIs($r, $r . '.*'); @endphp
            <a href="{{ route($r) }}" @if ($aktif) aria-current="page" @endif
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm transition {{ $aktif ? 'bg-white/15 font-semibold text-white' : 'text-[#EBD5C5] hover:bg-white/5 hover:text-white' }}">
              <x-icon :n="$ic" class="h-5 w-5 {{ $aktif ? 'text-[#E8C468]' : '' }}"/>{{ $label }}
            </a>
          @endforeach
        </div>
      @endforeach
    </nav>

    <div class="border-t border-white/10 p-3">
      <button type="button" @click="keluar=true" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm text-[#EBD5C5] transition hover:bg-white/5 hover:text-white"><x-icon n="out"/>Keluar</button>
    </div>
  </aside>

  {{-- Bunga pojok kanan: di belakang isi, tidak bisa diklik --}}
  <div class="pointer-events-none fixed inset-y-0 left-0 right-0 z-0 overflow-hidden lg:left-72" aria-hidden="true">
    <x-ornamen class="absolute -right-16 top-16 h-64 w-64 text-[#C9962B]/25" />
    <x-ornamen class="absolute -bottom-20 -right-16 h-72 w-72 text-[#C9962B]/25" />
  </div>

  {{-- Isi halaman --}}
  <div class="lg:pl-72">
    <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-[#EFE2D0] bg-[#FFFCF8]/90 px-4 backdrop-blur sm:px-6 lg:px-8">
      <button @click="open=true" class="flex h-11 w-11 items-center justify-center rounded-xl text-[#8B1E2D] hover:bg-[#F0E2D0]/60 lg:hidden" aria-label="Buka menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
      <span class="judul font-bold text-[#8B1E2D] sm:hidden">Gandrung Sewu</span>
      <nav aria-label="Lokasi halaman" class="hidden min-w-0 items-center gap-2 text-sm text-[#6B5A50] sm:flex">
        <span>{{ $admin ? 'Panel Admin' : 'Portal Penari' }}</span><x-icon n="chevron" class="h-4 w-4"/>
        <span class="truncate font-semibold text-[#2A1A14]">{{ $title }}</span>
      </nav>

      <div class="ml-auto flex items-center gap-4">
        <span class="hidden text-sm text-[#6B5A50] md:block">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
        <a href="{{ route('profile.edit') }}" title="Pengaturan akun"
           class="flex items-center gap-3 rounded-xl px-2 py-1 transition hover:bg-[#F0E2D0]/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#8B1E2D] {{ request()->routeIs('profile.*') ? 'bg-[#F0E2D0]/60' : '' }}">
          <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#8B1E2D] font-semibold text-white">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
          <span class="hidden text-sm leading-tight sm:block"><span class="block max-w-[160px] truncate font-semibold">{{ $u->name }}</span><span class="block text-xs text-[#6B5A50]">{{ $admin ? 'Admin' : 'Penari' }}</span></span>
        </a>
      </div>
    </header>

    <main class="relative z-10 mx-auto max-w-7xl space-y-6 p-4 sm:p-6 lg:p-8">
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
          <h1 class="judul text-2xl font-bold sm:text-3xl">{{ $title }}</h1>
          @if ($sub)<p class="mt-1 text-sm text-[#6B5A50]">{{ $sub }}</p>@endif
        </div>
        @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
      </div>
      <x-flash />
      {{ $slot }}
    </main>
  </div>
</div>
</body>
</html>