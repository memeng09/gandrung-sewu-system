<x-publik title="Beranda">
<section style="background-color:#6E1427;" class="relative overflow-hidden">
  
  {{-- Background Merah & Gradasi Tetap Konstan di Bawah --}}
  <div class="absolute inset-0 bg-gradient-to-r from-[rgba(94,18,32,0.98)] via-[rgba(94,18,32,0.75)] to-[rgba(94,18,32,0.5)] z-0"></div>

  {{-- Gambar Slider 1 (Utama) --}}
  <img id="heroSliderImg" src="{{ asset('images/hero.jpg') }}" alt="Gandrung Sewu" class="absolute inset-0 w-full h-full object-cover object-right opacity-40 transition-opacity duration-1000 z-1 pointer-events-none">

  <div class="mx-auto max-w-6xl px-4 pb-28 pt-16 sm:pb-36 sm:pt-24 relative z-10">
    <p class="text-sm tracking-[0.25em] text-[#E8C468]">Festival Tari Tradisional</p>
    <h1 class="judul mt-3 text-5xl font-bold leading-[1.05] text-white sm:text-7xl">Gandrung<br>Sewu</h1>
    <p class="judul mt-3 text-xl italic text-[#F6E7D4] sm:text-2xl">Menari Bersama, Menjaga Budaya</p>
    <p class="mt-4 max-w-md text-[#F6E7D4]">Satu tarian, ribuan penari, untuk Banyuwangi yang lebih berbudaya.</p>
    <a href="{{ route('register') }}" class="btn-emas mt-7">Daftar Penari</a>
    <p class="mt-3 max-w-md text-xs text-[#F6E7D4]/80">Khusus penari yang sudah lolos seleksi dan menerima kartu kode dari juri.</p>
  </div>
</section>

<div class="mx-auto -mt-14 max-w-6xl px-4 relative z-20">
  <div class="grid gap-3 sm:grid-cols-3">
    <div class="box flex items-center gap-4" style="background-color: #F0CD8A;"><span class="text-[#8B1E2D]"><x-icon n="users" class="h-9 w-9"/></span><div><div class="font-bold">± {{ number_format(\App\Models\Kecamatan::TARGET, 0, ',', '.') }}</div><div class="text-sm text-[#6B5A50]">Penari</div></div></div>
    <div class="box flex items-center gap-4" style="background-color: #F0CD8A;"><span class="text-[#8B1E2D]"><x-icon n="cal" class="h-9 w-9"/></span><div><div class="text-sm text-[#6B5A50]">Pelaksanaan</div><div class="font-bold">Segera diumumkan</div></div></div>
    <div class="box flex items-center gap-4" style="background-color: #F0CD8A;"><span class="text-[#8B1E2D]"><x-icon n="pin" class="h-9 w-9"/></span><div><div class="text-sm text-[#6B5A50]">Lokasi</div><div class="font-bold">Pantai Boom Marina, Banyuwangi</div></div></div>
  </div>
</div>


{{-- RINGKASAN FESTIVAL --}}
<section class="mx-auto max-w-6xl px-4 py-14 text-center">
  <p class="text-sm tracking-[0.25em] text-[#C9962B] font-semibold">TENTANG FESTIVAL</p>
  <h2 class="judul mt-2 text-2xl font-bold sm:text-3xl">Apa itu Gandrung Sewu?</h2>
  <p class="mx-auto mt-4 max-w-2xl text-[#6B5A50]">
    Gandrung Sewu adalah festival tari tradisional yang menampilkan ribuan penari membawakan Tari Gandrung secara bersama-sama di Pantai Boom Marina, Banyuwangi. Festival ini digelar untuk melestarikan budaya Banyuwangi dan memperkenalkannya kepada generasi muda.
  </p>
  <a href="{{ route('tentang') }}" class="btn-o mt-6" style="display:inline-flex; align-items:center; justify-content:center;">Selengkapnya</a>
</section>


{{-- CARA MENDAPATKAN AKUN --}}
<section id="pendaftaran" class="mx-auto max-w-6xl scroll-mt-20 px-4 pb-14">
  <h2 class="judul text-2xl font-bold sm:text-3xl">Cara mendapatkan akun penari</h2>
  <div class="mt-6 grid gap-4 md:grid-cols-3">
    @foreach ([['Ikuti seleksi', 'Seleksi dilakukan tatap muka oleh juri dan terbuka untuk siapa saja yang bisa menari.'], ['Terima kartu kode', 'Penari yang layak menerima kartu berisi kode unik langsung dari juri.'], ['Daftar dan isi data diri', 'Buat akun, masukkan kode, lalu lengkapi data diri dan pas foto. E-sertifikat terisi otomatis.']] as $i => [$j, $t])
      <div class="box"><span class="judul text-3xl font-bold text-[#C9962B]">{{ $i + 1 }}</span><h3 class="mt-2 font-semibold">{{ $j }}</h3><p class="mt-1 text-sm text-[#6B5A50]">{{ $t }}</p></div>
    @endforeach
  </div>
</section>


<script>
    const heroSlides = [
        "{{ asset('images/hero.png') }}",
        "{{ asset('images/hero-2.png') }}",
        "{{ asset('images/hero-3.png') }}"
    ];

    let currentSlide = 0;
    setInterval(() => {
        currentSlide = (currentSlide + 1) % heroSlides.length;
        const imgEl = document.getElementById('heroSliderImg');
        if (imgEl) {
            // Turunkan opacity perlahan, ganti gambar, lalu naikkan lagi
            imgEl.style.opacity = '0';
            setTimeout(() => {
                imgEl.src = heroSlides[currentSlide];
                imgEl.style.opacity = '0.4';
            }, 500);
        }
    }, 5000);
</script>
</x-publik>