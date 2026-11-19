<x-panel title="Dashboard Admin" sub="Selamat datang! Berikut ringkasan data terbaru.">
  @php
    $pct = fn ($a, $b) => $b ? min(100, round($a / $b * 100)) : 0;
    $jl = ['sekolah' => 'Sekolah', 'desa' => 'Desa', 'umum' => 'Umum'];
  @endphp

  <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    <div class="col-span-2 rounded-2xl bg-[#8B1E2D] p-4 text-white sm:p-5">
      <div class="flex items-center gap-2 text-sm"><x-icon n="key"/>Kode dibuat dari target</div>
      <div class="judul mt-2 text-3xl font-bold">{{ number_format($stat['dibuat']) }} <span class="text-lg font-normal">/ {{ number_format($stat['target']) }}</span></div>
      <div class="mt-2 h-2 rounded bg-white/25"><div class="h-2 rounded bg-[#F1CF8A]" style="width:{{ $pct($stat['dibuat'], $stat['target']) }}%"></div></div>
      <div class="mt-1 text-xs text-[#F6E7D4]">{{ $stat['dibuat'] <= $stat['target'] ? 'Sisa ' . number_format($stat['target'] - $stat['dibuat']) . ' kode untuk dibagi setelah seleksi' : 'Melebihi target ' . number_format($stat['dibuat'] - $stat['target']) }}</div>
    </div>
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
            <div><div class="font-semibold">{{ $p->nama }} mendaftar</div><div class="text-xs text-[#6B5A50]">{{ $p->label_jalur }} &middot; {{ $p->wilayah }} &middot; {{ $p->created_at->format('d M, H:i') }}</div></div></li>
        @empty <li class="text-sm text-[#6B5A50]">Belum ada pendaftaran.</li>
        @endforelse
      </ul>
    </div>
  </div>

  <div class="box">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
      <h2 class="judul text-lg font-bold">Rekap per kecamatan dan jalur</h2>
      <a href="{{ route('admin.kuota') }}" class="btn btn-s">Atur kuota &amp; kode</a>
    </div>
    <p class="mb-3 text-xs text-[#6B5A50]">Angka besar: terpakai / dibuat. Di bawahnya: kuota yang ditetapkan.</p>
    <div class="overflow-x-auto"><table class="w-full min-w-[620px]">
      <tr><th class="th">Kecamatan</th>@foreach ($jl as $l)<th class="th">{{ $l }}</th>@endforeach</tr>
      @foreach ($kec as $k)
        <tr><td class="td">{{ $k->nama }} <span class="text-xs text-[#6B5A50]">{{ $k->kode_singkat }}</span></td>
          @foreach ($jl as $j => $l)
            @php $key = $k->id . '|' . $j; $kd = $kode[$key] ?? null; $kq = $kuota[$key]->kuota ?? null; @endphp
            <td class="td"><div class="font-semibold">{{ (int) ($kd->dipakai ?? 0) }} / {{ (int) ($kd->dibuat ?? 0) }}</div>
              <div class="text-xs text-[#6B5A50]">{{ $kq !== null ? 'kuota ' . $kq : 'kuota belum pasti' }}</div></td>
          @endforeach
        </tr>
      @endforeach
      <tr class="bg-[#F0E2D0]/40"><td class="td font-semibold">Luar Banyuwangi</td>
        <td class="td" colspan="3"><span class="font-semibold">{{ (int) ($luar->dipakai ?? 0) }} / {{ (int) ($luar->dibuat ?? 0) }}</span> <span class="text-xs text-[#6B5A50]">(tanpa kuota tetap, mengikuti pilihan juri)</span></td></tr>
    </table></div>
  </div>
</x-panel>