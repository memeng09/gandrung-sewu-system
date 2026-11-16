<x-publik title="Beranda">
<section style="background-color:#6E1427;background-image:linear-gradient(90deg,rgba(94,18,32,.95) 20%,rgba(94,18,32,.4)),url('/images/hero.jpg');background-size:cover;background-position:center right">
  <div class="mx-auto max-w-6xl px-4 pb-28 pt-16 sm:pb-36 sm:pt-24">
    <p class="text-sm tracking-[0.25em] text-[#E8C468]">Festival Tari Tradisional</p>
    <h1 class="judul mt-3 text-5xl font-bold leading-[1.05] text-white sm:text-7xl">Gandrung<br>Sewu</h1>
    <p class="judul mt-3 text-xl italic text-[#F6E7D4] sm:text-2xl">Menari Bersama, Menjaga Budaya</p>
    <p class="mt-4 max-w-md text-[#F6E7D4]">Satu tarian, ribuan penari, untuk Banyuwangi yang lebih berbudaya.</p>
    <a href="{{ route('register') }}" class="btn-emas mt-7">Daftar Penari</a>
    <p class="mt-3 max-w-md text-xs text-[#F6E7D4]/80">Khusus penari yang sudah lolos seleksi dan menerima kartu kode dari juri.</p>
  </div>
</section>

<div class="mx-auto -mt-14 max-w-6xl px-4">
  <div class="grid gap-3 sm:grid-cols-3">
    <div class="box flex items-center gap-4"><span class="text-[#8B1E2D]"><x-icon n="users" class="h-9 w-9"/></span><div><div class="font-bold">± {{ number_format(\App\Models\Kecamatan::TARGET, 0, ',', '.') }}</div><div class="text-sm text-[#6B5A50]">Penari</div></div></div>
    <div class="box flex items-center gap-4"><span class="text-[#8B1E2D]"><x-icon n="cal" class="h-9 w-9"/></span><div><div class="text-sm text-[#6B5A50]">Pelaksanaan</div><div class="font-bold">Segera diumumkan</div></div></div>{{-- ubah tanggal di sini --}}
    <div class="box flex items-center gap-4"><span class="text-[#8B1E2D]"><x-icon n="pin" class="h-9 w-9"/></span><div><div class="text-sm text-[#6B5A50]">Lokasi</div><div class="font-bold">Banyuwangi</div></div></div>
  </div>
</div>

<section id="tentang" class="mx-auto max-w-6xl scroll-mt-20 px-4 py-14">
  <h2 class="judul text-2xl font-bold sm:text-3xl">Cara mendapatkan akun penari</h2>
  <div class="mt-6 grid gap-4 md:grid-cols-3">
    @foreach ([['Ikuti seleksi', 'Seleksi dilakukan tatap muka oleh juri dan terbuka untuk siapa saja yang bisa menari.'], ['Terima kartu kode', 'Penari yang layak menerima kartu berisi kode unik langsung dari juri.'], ['Daftar dan isi data diri', 'Buat akun, masukkan kode, lalu lengkapi data diri dan pas foto. E-sertifikat terisi otomatis.']] as $i => [$j, $t])
      <div class="box"><span class="judul text-3xl font-bold text-[#C9962B]">{{ $i + 1 }}</span><h3 class="mt-2 font-semibold">{{ $j }}</h3><p class="mt-1 text-sm text-[#6B5A50]">{{ $t }}</p></div>
    @endforeach
  </div>
</section>

<section id="kontak" class="mx-auto max-w-6xl scroll-mt-20 px-4 pb-6">
  <div class="box"><h2 class="judul text-xl font-bold">Kontak panitia</h2>
    <p class="mt-2 text-sm text-[#6B5A50]">Kartu hilang atau ada kendala saat mengisi data? Hubungi panitia di email panitia@contoh.id atau WhatsApp 08xx xxxx xxxx.</p></div>{{-- ganti kontak asli --}}
</section>
</x-publik>