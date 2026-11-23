<x-panel title="Dashboard" sub="Pantau pendaftaran dan unduh e-sertifikatmu.">
  @php
    $dicabut = $penari?->status === 'dicabut';
    // 1 = masukkan kode, 2 = isi data diri, 3 = verifikasi, 4 = sertifikat
    $langkah = !$penari ? ($punyaKode ? 2 : 1) : ($penari->status === 'aktif' ? 4 : 3);
    $steps = [['Masukkan kode', 'Kode dari kartu juri'], ['Isi data diri', 'Data dan pas foto'], ['Verifikasi', 'Data diperiksa'], ['Sertifikat', 'Unduh e-sertifikat']];

    [$pesan, $tombol, $tautan] = match (true) {
        $dicabut                   => ['Pendaftaranmu dicabut. Hubungi panitia untuk informasi lebih lanjut.', null, null],
        $langkah === 1             => ['Masukkan kode dari kartu juri untuk memulai pendaftaran.', 'Mulai pendaftaran', route('penari.form')],
        $langkah === 2             => ['Kodemu sudah terkunci ke akun ini. Lengkapi data diri dan pas foto.', 'Lanjutkan pengisian', route('penari.form')],
        $langkah === 3             => ['Datamu sedang ditinjau admin.' . ($penari->catatan ? ' ' . $penari->catatan . '.' : ''), null, null],
        default                    => ['Selamat, kamu terdaftar sebagai penari Gandrung Sewu 2026.', 'Unduh e-sertifikat', route('penari.sertifikat')],
    };
  @endphp

  {{-- Sambutan --}}
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#6E1427] to-[#9A2A3E] p-6 text-white sm:p-8">
    <x-logo class="pointer-events-none absolute -right-10 -top-10 h-52 w-52 text-white/10"/>
    <div class="relative max-w-2xl">
      <p class="text-sm text-[#F0DCD0]">Selamat datang kembali</p>
      <h2 class="judul mt-1 text-2xl font-bold sm:text-3xl">Halo, {{ auth()->user()->name }}!</h2>
      <p class="mt-2 text-[#F6E7D4]">{{ $pesan }}</p>
      @if ($tombol)<a href="{{ $tautan }}" class="btn-emas mt-5">{{ $tombol }}</a>@endif
    </div>
  </div>

  {{-- Langkah pendaftaran --}}
  @unless ($dicabut)
    <div class="box">
      <h2 class="judul text-lg font-bold">Langkah pendaftaran</h2>
      <ol class="mt-4 grid gap-4 sm:grid-cols-4">
        @foreach ($steps as $i => [$judul, $ket])
          @php $no = $i + 1; $selesai = $no < $langkah; $sekarang = $no === $langkah; @endphp
          <li @if ($sekarang) aria-current="step" @endif>
            <div class="h-1.5 rounded-full {{ $selesai || $sekarang ? 'bg-[#8B1E2D]' : 'bg-[#F0E2D0]' }}"></div>
            <div class="mt-3 flex items-start gap-3">
              <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold {{ $selesai ? 'bg-[#8B1E2D] text-white' : ($sekarang ? 'bg-[#F1CF8A] text-[#4A1020]' : 'bg-[#F0E2D0] text-[#6B5A50]') }}">
                @if ($selesai)<x-icon n="check" class="h-5 w-5"/>@else{{ $no }}@endif
              </span>
              <div class="text-sm"><div class="font-semibold">{{ $judul }}</div><div class="text-xs text-[#6B5A50]">{{ $selesai ? 'Selesai' : ($sekarang ? 'Langkah saat ini' : $ket) }}</div></div>
            </div>
          </li>
        @endforeach
      </ol>
    </div>
  @endunless

  {{-- Data penari --}}
  @if ($penari)
    <div class="box">
      <div class="flex flex-wrap items-start gap-5">
        <img src="{{ asset('storage/' . $penari->foto) }}" alt="Foto {{ $penari->nama }}" class="h-36 w-28 rounded-xl object-cover">
        <div class="min-w-0 flex-1">
          <div class="flex flex-wrap items-center gap-3"><h2 class="judul text-xl font-bold">{{ $penari->nama }}</h2><x-badge :status="$penari->status" /></div>
          <dl class="mt-4 grid gap-x-8 gap-y-3 text-sm sm:grid-cols-2">
            <div><dt class="text-xs text-[#6B5A50]">Jalur</dt><dd class="font-semibold">{{ $penari->label_jalur }}</dd></div>
            <div><dt class="text-xs text-[#6B5A50]">Asal</dt><dd class="font-semibold">{{ $penari->asal }}</dd></div>
            <div><dt class="text-xs text-[#6B5A50]">Sekolah / instansi</dt><dd class="font-semibold">{{ $penari->sekolah ?: ($penari->instansi ?: '-') }}</dd></div>
            <div><dt class="text-xs text-[#6B5A50]">Sanggar</dt><dd class="font-semibold">{{ $penari->sanggar ?: '-' }}</dd></div>
            <div><dt class="text-xs text-[#6B5A50]">Kode yang dipakai</dt><dd class="font-mono text-xs font-semibold">{{ $penari->kode->kode }}</dd></div>
            <div><dt class="text-xs text-[#6B5A50]">Terdaftar</dt><dd class="font-semibold">{{ $penari->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</dd></div>
          </dl>
          @if ($penari->status === 'aktif')
            <a href="{{ route('penari.sertifikat') }}" class="btn mt-5"><x-icon n="download" class="h-5 w-5"/>Unduh e-sertifikat (PDF)</a>
          @endif
        </div>
      </div>
    </div>
  @endif

  <div class="grid gap-4 md:grid-cols-2">
    <div class="box">
      <h2 class="judul text-lg font-bold">Informasi penting</h2>
      <p class="mt-1 text-sm text-[#6B5A50]">Nama di e-sertifikat diambil dari data yang kamu isi. Pastikan penulisannya benar. Kalau ada yang salah, hubungi panitia.</p>
    </div>
    <a href="{{ route('penari.cards') }}" class="box flex items-center justify-between gap-3 transition hover:border-[#C9962B]">
      <div><h2 class="judul text-lg font-bold">Penari yang lolos</h2><p class="mt-1 text-sm text-[#6B5A50]">Lihat daftar penari Gandrung Sewu.</p></div>
      <x-icon n="chevron" class="h-5 w-5 text-[#8B1E2D]"/>
    </a>
  </div>
</x-panel>