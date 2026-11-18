@php
    // ====== GANTI DATA KONTAK DI SINI SAJA (sekarang masih data contoh) ======
    $wa      = ['tampil' => '+62 812-3456-7890', 'nomor' => '6281234567890'];
    $email   = 'panitia@contoh.id';
    $sosmed  = [
        ['nama' => 'Instagram', 'kode' => 'IG', 'warna' => '#E1306C', 'akun' => '@akun_contoh',  'url' => 'https://instagram.com/akun_contoh'],
        ['nama' => 'TikTok',    'kode' => 'TT', 'warna' => '#111111', 'akun' => '@akun_contoh',  'url' => 'https://tiktok.com/@akun_contoh'],
        ['nama' => 'Facebook',  'kode' => 'FB', 'warna' => '#1877F2', 'akun' => 'Akun Contoh',  'url' => 'https://facebook.com/akun.contoh'],
        ['nama' => 'YouTube',   'kode' => 'YT', 'warna' => '#FF0000', 'akun' => '@AkunContoh',  'url' => 'https://youtube.com/@AkunContoh'],
    ];
    $lokasi  = 'Pantai Boom Marina, Banyuwangi, Jawa Timur';

    // ====== DAFTAR FAQ: tambah / ubah / hapus sesuai aturan panitia ======
    $faq = [
        ['tanya' => 'Kartu kode saya hilang, bagaimana?',
         'jawab' => 'Hubungi panitia lewat WhatsApp atau email di halaman ini dengan menyebutkan nama lengkap dan kecamatan asal.'],
        ['tanya' => 'Kode saya ditolak saat daftar, kenapa?',
         'jawab' => 'Pastikan kode diketik persis seperti yang tertera di kartu, termasuk huruf besar/kecil dan tanda hubung. Jika masih gagal, hubungi panitia.'],
        ['tanya' => 'Saya salah mengisi data diri, bisakah diubah?',
         'jawab' => 'Foto, kecamatan, dan asal sekolah bisa diedit lewat dashboard penari. Nama dikunci sesuai kode, jadi perubahan nama harus lewat panitia.'],
        ['tanya' => 'Siapa saja yang boleh mendaftar akun penari?',
         'jawab' => 'Hanya penari yang sudah lolos seleksi dan menerima kartu kode dari juri.'],
        ['tanya' => 'Di mana data saya akan ditampilkan?',
         'jawab' => 'Data yang kamu isi akan tampil di halaman Penari Lolos setelah kamu menyetujui penayangannya di form.'],
    ];
    // =========================================================================
@endphp

<x-publik title="Kontak">
<style>
    .badge-kontak {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 9999px;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .baris-kontak {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px 0;
        color: #35251F;
        transition: color .2s;
    }
    .baris-kontak:hover { color: #8B1E2D; }

    .faq-item {
        background: #fff;
        border: 1px solid #E5D5C5;
        border-radius: 12px;
        margin-bottom: 10px;
        overflow: hidden;
    }
    .faq-tombol {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 20px;
        text-align: left;
        font-weight: 600;
        color: #35251F;
        cursor: pointer;
    }
    .faq-tombol:hover { color: #8B1E2D; }
    .faq-panah {
        flex-shrink: 0;
        font-size: 20px;
        line-height: 1;
        color: #C9962B;
        transition: transform .2s;
    }
    .faq-jawab {
        padding: 0 20px 18px;
        font-size: 14px;
        line-height: 1.7;
        color: #6B5A50;
    }
</style>

<section class="mx-auto max-w-6xl px-4 py-14">

  {{-- JUDUL --}}
  <div class="text-center" style="margin-bottom:48px;">
    <p class="text-sm tracking-[0.25em] text-[#C9962B] font-semibold">HUBUNGI KAMI</p>
    <h1 class="judul text-3xl font-bold text-[#35251F] sm:text-4xl" style="margin-top:10px;">Kontak Panitia</h1>
    <p class="mx-auto text-[#6B5A50]" style="margin-top:16px; max-width:560px;">
      Cek pertanyaan yang sering diajukan di bawah. Kalau belum terjawab, hubungi panitia lewat kontak yang tersedia.
    </p>
  </div>

  {{-- FAQ --}}
  <div class="mx-auto" style="max-width:768px; margin-bottom:56px;" x-data="{ buka: null }">
    <h2 class="judul text-2xl font-bold text-[#35251F] text-center" style="margin-bottom:20px;">Pertanyaan yang Sering Diajukan</h2>
    @foreach ($faq as $i => $f)
      <div class="faq-item">
        <button type="button" class="faq-tombol" @click="buka = (buka === {{ $i }}) ? null : {{ $i }}" :aria-expanded="buka === {{ $i }}">
          <span>{{ $f['tanya'] }}</span>
          <span class="faq-panah" :style="buka === {{ $i }} ? 'transform: rotate(45deg)' : ''">+</span>
        </button>
        <div x-show="buka === {{ $i }}" x-cloak class="faq-jawab">{{ $f['jawab'] }}</div>
      </div>
    @endforeach
  </div>

  {{-- KARTU KONTAK --}}
  <div class="grid gap-4 md:grid-cols-3">

    {{-- WhatsApp + Email --}}
    <div class="box">
      <h3 class="judul text-xl font-bold text-[#8B1E2D]">WhatsApp</h3>
      <a href="https://wa.me/{{ $wa['nomor'] }}?text={{ urlencode('Halo panitia Gandrung Sewu, saya ingin bertanya...') }}"
         target="_blank" rel="noopener" class="baris-kontak" style="margin-top:8px;">
        <span class="badge-kontak" style="background:#25D366;">WA</span>
        <span>{{ $wa['tampil'] }}</span>
      </a>

      <h3 class="judul text-xl font-bold text-[#8B1E2D]" style="margin-top:20px;">Email</h3>
      <a href="mailto:{{ $email }}" class="baris-kontak" style="margin-top:8px;">
        <span class="badge-kontak" style="background:#C9962B;">@</span>
        <span>{{ $email }}</span>
      </a>
    </div>

    {{-- Social Media --}}
    <div class="box">
      <h3 class="judul text-xl font-bold text-[#8B1E2D]">Social Media</h3>
      <div style="margin-top:8px;">
        @foreach ($sosmed as $s)
          <a href="{{ $s['url'] }}" target="_blank" rel="noopener" class="baris-kontak">
            <span class="badge-kontak" style="background:{{ $s['warna'] }};">{{ $s['kode'] }}</span>
            <span>{{ $s['akun'] }}</span>
          </a>
        @endforeach
      </div>
    </div>

    {{-- Lokasi --}}
    <div class="box">
      <h3 class="judul text-xl font-bold text-[#8B1E2D]">Lokasi</h3>
      <p class="text-[#35251F]" style="margin-top:12px; line-height:1.7;">{{ $lokasi }}</p>
    </div>

  </div>

  {{-- PETA --}}
  <div class="box" style="margin-top:32px; padding:0; overflow:hidden;">
    <iframe
      title="Peta Pantai Boom Marina Banyuwangi"
      src="https://www.google.com/maps?q=Pantai+Boom+Marina+Banyuwangi&output=embed"
      style="border:0; width:100%; height:380px; display:block;"
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      allowfullscreen></iframe>
  </div>

</section>
</x-publik>