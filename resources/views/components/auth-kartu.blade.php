@props(['aktif' => 'masuk', 'judul' => '', 'ket' => ''])
<x-publik :title="$judul">
  <div class="mx-auto my-6 grid max-w-4xl overflow-hidden rounded-3xl border border-[#F0E2D0] bg-[#FFFAF4] shadow-lg shadow-[#8B1E2D]/10 sm:my-10 lg:grid-cols-[1fr_1.1fr]">
    <div class="relative hidden min-h-[500px] lg:block"
         style="background-color:#6E1427;background-image:linear-gradient(160deg,rgba(94,18,32,.5),rgba(94,18,32,.92)),url('/images/login.jpg');background-size:cover;background-position:center">
      <div class="absolute inset-x-0 bottom-0 p-8 text-white">
        <x-logo class="h-12 w-12 text-[#E8C468]"/>
        <p class="judul mt-3 text-2xl italic">Menari Bersama, Menjaga Budaya</p>
      </div>
    </div>
    <div class="p-6 sm:p-10">
      <div class="mb-6 grid grid-cols-2 border-b border-[#E5D5C0] text-center text-sm font-semibold">
        <a href="{{ route('login') }}" class="pb-3 {{ $aktif === 'masuk' ? 'border-b-2 border-[#8B1E2D] text-[#8B1E2D]' : 'text-[#6B5A50]' }}">Masuk</a>
        <a href="{{ route('register') }}" class="pb-3 {{ $aktif === 'daftar' ? 'border-b-2 border-[#8B1E2D] text-[#8B1E2D]' : 'text-[#6B5A50]' }}">Daftar Penari</a>
      </div>
      <h1 class="judul text-2xl font-bold">{{ $judul }}</h1>
      <p class="mb-5 mt-1 text-sm text-[#6B5A50]">{{ $ket }}</p>
      {{ $slot }}
    </div>
  </div>
</x-publik>