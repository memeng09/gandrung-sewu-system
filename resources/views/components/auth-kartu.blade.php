@props(['aktif' => 'masuk', 'judul' => '', 'ket' => ''])
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $judul }} - Gandrung Sewu</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-[#FBF3EA] text-[#2A1A14] antialiased">
<div class="grid min-h-screen lg:grid-cols-[1.05fr_1fr]">

  {{-- Panel merek (hanya layar lebar) --}}
  <aside class="relative hidden flex-col justify-between overflow-hidden p-12 text-[#F6E7D4] lg:flex"
         style="background-color:#5E1220;background-image:linear-gradient(160deg,rgba(94,18,32,.78),rgba(94,18,32,.96)),url('/images/login.jpg');background-size:cover;background-position:center">
    <x-ornamen class="pointer-events-none absolute -bottom-28 -right-28 h-[26rem] w-[26rem] text-[#E8C468]/20" />
    <a href="{{ route('beranda') }}" class="relative flex items-center gap-3 text-[#E8C468]">
      <x-logo class="h-10 w-10"/><span class="judul text-lg font-bold leading-tight text-white">GANDRUNG<br>SEWU</span>
    </a>

    <div class="relative max-w-md">
      <h2 class="judul text-4xl font-bold leading-tight text-white">Satu tarian, ribuan penari.</h2>
      <p class="mt-4 text-[#F0DCD0]">Portal resmi penari Gandrung Sewu. Masuk untuk melihat status pendaftaran dan mengunduh e-sertifikatmu.</p>
      <ol class="mt-8 space-y-4 text-sm">
        @foreach (['Ikuti seleksi di kecamatanmu', 'Terima kartu kode dari juri', 'Daftar akun, masukkan kode, lengkapi data diri'] as $i => $langkah)
          <li class="flex items-center gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/15 font-semibold text-[#E8C468]">{{ $i + 1 }}</span>{{ $langkah }}</li>
        @endforeach
      </ol>
    </div>

    <p class="relative text-xs text-[#E5C8CF]">Festival Gandrung Sewu, Banyuwangi</p>
  </aside>

  {{-- Formulir --}}
  <div class="flex min-h-screen flex-col">
    <header class="flex items-center justify-between px-5 py-4 sm:px-10">
      <a href="{{ route('beranda') }}" class="flex items-center gap-2 text-[#8B1E2D] lg:hidden"><x-logo class="h-8 w-8"/><span class="judul font-bold">Gandrung Sewu</span></a>
      <a href="{{ route('beranda') }}" class="ml-auto inline-flex items-center gap-1 text-sm font-semibold text-[#6B5A50] hover:text-[#8B1E2D]"><span aria-hidden="true">&larr;</span> Kembali ke beranda</a>
    </header>

    <main class="flex flex-1 items-center justify-center px-5 py-6 sm:px-10">
      <div class="w-full max-w-md">
        <div class="mb-6 grid grid-cols-2 rounded-xl bg-[#F0E2D0] p-1 text-sm font-semibold" role="tablist">
          <a href="{{ route('login') }}" class="rounded-lg py-2.5 text-center transition {{ $aktif === 'masuk' ? 'bg-white text-[#8B1E2D] shadow-sm' : 'text-[#6B5A50] hover:text-[#8B1E2D]' }}">Masuk</a>
          <a href="{{ route('register') }}" class="rounded-lg py-2.5 text-center transition {{ $aktif === 'daftar' ? 'bg-white text-[#8B1E2D] shadow-sm' : 'text-[#6B5A50] hover:text-[#8B1E2D]' }}">Daftar Penari</a>
        </div>

        <div class="box !p-6 sm:!p-8">
          <h1 class="judul text-2xl font-bold">{{ $judul }}</h1>
          <p class="mb-6 mt-1 text-sm text-[#6B5A50]">{{ $ket }}</p>
          {{ $slot }}
        </div>
      </div>
    </main>

    <footer class="px-5 py-4 text-center text-xs text-[#6B5A50] sm:px-10">&copy; {{ date('Y') }} Gandrung Sewu, Banyuwangi</footer>
  </div>
</div>
</body>
</html>