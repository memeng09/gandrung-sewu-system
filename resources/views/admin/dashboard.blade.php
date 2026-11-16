<x-panel title="Dashboard Admin" sub="Selamat datang! Berikut ringkasan data terbaru.">
  @php $pct = fn ($a, $b) => $b ? min(100, round($a / $b * 100)) : 0; @endphp

  <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    <div class="rounded-2xl bg-[#8B1E2D] p-4 text-white sm:p-5">
      <div class="flex items-center gap-2 text-sm"><x-icon n="users"/>Kuota terisi</div>
      <div class="judul mt-2 text-3xl font-bold">{{ number_format($stat['kuota']) }}</div>
      <div class="text-xs text-[#F6E7D4]">dari target {{ number_format($stat['target']) }}</div>
    </div>
    <div class="box"><div class="flex items-center gap-2 text-sm text-[#6B5A50]"><x-icon n="key"/>Kode dibuat</div><div class="judul mt-2 text-3xl font-bold">{{ number_format($stat['dibuat']) }}</div></div>
    <div class="box"><div class="flex items-center gap-2 text-sm text-[#6B5A50]"><x-icon n="edit"/>Kode terpakai</div><div class="judul mt-2 text-3xl font-bold">{{ number_format($stat['terpakai']) }}</div></div>
    <div class="box"><div class="flex items-center gap-2 text-sm text-[#6B5A50]"><x-icon n="check"/>Terverifikasi</div><div class="judul mt-2 text-3xl font-bold">{{ number_format($stat['aktif']) }}</div></div>
  </div>

  @if ($stat['ditinjau'] > 0)
    <a href="{{ route('admin.penari', ['status' => 'perlu_ditinjau']) }}" class="flex items-center justify-between rounded-2xl bg-amber-100 px-5 py-4 text-sm text-amber-900">
      <span><b>{{ $stat['ditinjau'] }}</b> data penari perlu ditinjau.</span><span class="font-semibold">Tinjau sekarang</span></a>
  @endif

  <div class="grid gap-4 lg:grid-cols-[2fr_1fr]">
    <div class="box">
      <h2 class="judul text-lg font-bold">Statistik pendaftaran</h2><p class="text-xs text-[#6B5A50]">7 hari terakhir</p>
      @php
        $max = max(1, $harian->max('n')); $titik = [];
        foreach ($harian as $i => $h) $titik[] = [round(40 + $i * 90), round(150 - $h['n'] / $max * 120)];
        $garis = collect($titik)->map(fn ($t) => $t[0] . ',' . $t[1])->implode(' ');
      @endphp
      <svg viewBox="0 0 600 200" class="mt-3 w-full" role="img" aria-label="Grafik jumlah pendaftaran per hari selama 7 hari terakhir">
        <polygon points="40,150 {{ $garis }} 580,150" fill="#8B1E2D" fill-opacity=".1"/>
        <polyline points="{{ $garis }}" fill="none" stroke="#8B1E2D" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>
        @foreach ($harian as $i => $h)
          <circle cx="{{ $titik[$i][0] }}" cy="{{ $titik[$i][1] }}" r="4.5" fill="#8B1E2D"/>
          <text x="{{ $titik[$i][0] }}" y="180" text-anchor="middle" font-size="12" fill="#6B5A50">{{ $h['tgl'] }}</text>
        @endforeach
        <text x="4" y="34" font-size="12" fill="#6B5A50">{{ $max }}</text><text x="4" y="154" font-size="12" fill="#6B5A50">0</text>
      </svg>
    </div>
    <div class="box">
      <h2 class="judul text-lg font-bold">Aktivitas terbaru</h2>
      <ul class="mt-3 space-y-3">
        @forelse ($terbaru as $p)
          <li class="flex items-start gap-3 text-sm"><img src="{{ asset('storage/' . $p->foto) }}" alt="" class="h-11 w-9 rounded object-cover">
            <div><div class="font-semibold">{{ $p->nama }} mendaftar</div><div class="text-xs text-[#6B5A50]">{{ $p->kecamatan->nama }} &middot; {{ $p->created_at->format('d M Y, H:i') }}</div></div></li>
        @empty <li class="text-sm text-[#6B5A50]">Belum ada pendaftaran.</li>
        @endforelse
      </ul>
    </div>
  </div>

  <form method="post" action="{{ route('admin.kode.generate-semua') }}" class="box flex flex-wrap items-center justify-between gap-3">@csrf
    <div class="text-sm">Buat kode untuk semua kecamatan yang kuotanya belum terpenuhi.</div><button class="btn">Generate semua kode</button>
  </form>

  <div class="box">
    <h2 class="judul mb-3 text-lg font-bold">Rekap per kecamatan</h2>
    <div class="overflow-x-auto"><table class="w-full min-w-[560px]">
      <tr><th class="th">Kecamatan</th><th class="th">Kuota</th><th class="th">Kode dibuat</th><th class="th w-1/3">Terpakai</th></tr>
      @foreach ($kec as $k)
      <tr><td class="td">{{ $k->nama }} <span class="text-xs text-[#6B5A50]">{{ $k->kode_singkat }}</span></td><td class="td">{{ $k->kuota }}</td><td class="td">{{ $k->kode_dibuat }}</td>
        <td class="td"><div class="flex items-center gap-2"><div class="h-2 flex-1 rounded bg-[#F0E2D0]"><div class="h-2 rounded bg-[#C9962B]" style="width:{{ $pct($k->kode_dipakai, $k->kuota) }}%"></div></div><span class="text-xs">{{ $k->kode_dipakai }}/{{ $k->kuota }}</span></div></td></tr>
      @endforeach
    </table></div>
  </div>
</x-panel>