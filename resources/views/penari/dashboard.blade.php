<x-panel title="Dashboard">
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#6E1427] to-[#9A2A3E] p-6 text-white sm:p-8">
    <p class="judul text-2xl font-bold sm:text-3xl">Halo, {{ auth()->user()->name }}!</p>
    <p class="mt-1 text-sm text-[#F6E7D4]">Selamat datang di Portal Penari Gandrung Sewu.</p>
    <x-logo class="pointer-events-none absolute -right-8 -top-8 h-44 w-44 text-white/10"/>
  </div>

  <div class="grid gap-4 md:grid-cols-2">
    <div class="box flex items-center gap-4">
      <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full {{ $penari?->status === 'aktif' ? 'bg-[#8B1E2D] text-white' : 'bg-[#F1CF8A] text-[#4A1020]' }}"><x-icon n="check" class="h-7 w-7"/></span>
      <div><div class="text-sm text-[#6B5A50]">Status pendaftaran</div>
        <div class="text-lg font-bold">
          @if (!$penari) Belum mengisi data
          @elseif ($penari->status === 'aktif') Terverifikasi
          @elseif ($penari->status === 'perlu_ditinjau') Perlu ditinjau
          @else Dicabut @endif
        </div></div>
    </div>
    <div class="box flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-4"><span class="text-[#8B1E2D]"><x-icon n="cal" class="h-8 w-8"/></span>
        <div><div class="text-sm text-[#6B5A50]">Event berlangsung</div><div class="font-bold">Gandrung Sewu 2026</div></div></div>
      <a href="{{ route('penari.cards') }}" class="btn-emas !h-10 !px-5">Lihat penari lolos</a>
    </div>
  </div>

  <div class="box space-y-3">
    @if (!$penari)
      <h2 class="font-semibold">Kamu belum mengisi data diri</h2>
      <p class="text-sm text-[#6B5A50]">Siapkan kartu kode dari juri dan pas foto 3x4, lalu lengkapi data dirimu.</p>
      <a href="{{ route('penari.form') }}" class="btn">Isi data diri</a>
    @else
      <div class="flex items-center gap-4">
        <img src="{{ asset('storage/' . $penari->foto) }}" alt="Foto {{ $penari->nama }}" class="h-24 w-[72px] rounded-xl object-cover">
        <div><div class="text-lg font-bold">{{ $penari->nama }}</div><div class="text-sm text-[#6B5A50]">Kecamatan {{ $penari->kecamatan->nama }}</div>
          <div class="mt-2"><x-badge :status="$penari->status" /></div></div>
      </div>
      @if ($penari->status === 'aktif')
        <p class="text-sm">Selamat, kamu terdaftar sebagai penari Gandrung Sewu.</p>
        <a href="{{ route('penari.sertifikat') }}" class="btn">Unduh e-sertifikat (PDF)</a>
      @elseif ($penari->status === 'perlu_ditinjau')
        <p class="text-sm">Datamu sedang ditinjau admin. {{ $penari->catatan }}</p>
      @else
        <p class="text-sm">Pendaftaranmu dicabut. Hubungi panitia.</p>
      @endif
    @endif
  </div>

  <div class="box">
    <h2 class="judul text-lg font-bold">Informasi penting</h2>
    <p class="mt-1 text-sm text-[#6B5A50]">Pastikan data dirimu benar. Nama di e-sertifikat diambil langsung dari data yang kamu isi.</p>
  </div>
</x-panel>