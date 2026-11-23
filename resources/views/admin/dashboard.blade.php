<x-panel title="Dashboard" sub="Ringkasan seleksi dan pendaftaran penari Gandrung Sewu 2026.">
  <x-slot:actions>
    <a href="{{ route('admin.penari') }}" class="btn-o">Data penari</a>
    <a href="{{ route('admin.kuota') }}" class="btn">Atur kuota &amp; kode</a>
  </x-slot:actions>

  @php
    $pct = fn ($a, $b) => $b ? min(100, round($a / $b * 100)) : 0;
    $jl = ['sekolah' => 'Sekolah', 'desa' => 'Desa', 'umum' => 'Umum'];
    $perJalur = collect($jl)->map(fn ($l, $j) => [
        'label' => $l, 'dibuat' => (int) $kode->where('jalur', $j)->sum('dibuat'), 'dipakai' => (int) $kode->where('jalur', $j)->sum('dipakai'),
    ]);
    $perJalur->put('luar', ['label' => 'Luar Banyuwangi', 'dibuat' => (int) ($luar->dibuat ?? 0), 'dipakai' => (int) ($luar->dipakai ?? 0)]);
  @endphp

  {{-- Ringkasan --}}
  <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-stat label="Kode dibuat" :nilai="number_format($stat['dibuat'])" ikon="key" :info="'dari target ' . number_format($stat['target']) . ' kode'">
      <div class="h-2 rounded-full bg-[#F0E2D0]"><div class="h-2 rounded-full bg-[#8B1E2D]" style="width:{{ $pct($stat['dibuat'], $stat['target']) }}%"></div></div>
      <div class="text-xs text-[#6B5A50]">{{ $stat['dibuat'] <= $stat['target'] ? 'Sisa ' . number_format($stat['target'] - $stat['dibuat']) . ' untuk dibagi setelah seleksi' : 'Melebihi target ' . number_format($stat['dibuat'] - $stat['target']) }}</div>
    </x-stat>
    <x-stat label="Kode terpakai" :nilai="number_format($stat['terpakai'])" ikon="edit" warna="emas"
            :info="$stat['dibuat'] ? $pct($stat['terpakai'], $stat['dibuat']) . '% dari kode yang dibuat' : 'Belum ada kode dibuat'" />
    <x-stat label="Penari terverifikasi" :nilai="number_format($stat['aktif'])" ikon="check" warna="hijau" info="Tampil di halaman Penari Lolos" />
    <x-stat label="Perlu ditinjau" :nilai="number_format($stat['ditinjau'])" ikon="clock" warna="amber"
            :href="route('admin.penari', ['status' => 'perlu_ditinjau'])" :info="$stat['ditinjau'] ? 'Klik untuk meninjau datanya' : 'Tidak ada antrean'" />
  </div>

  {{-- Grafik dan aktivitas --}}
  <div class="grid gap-4 lg:grid-cols-3">
    <div class="box lg:col-span-2">
      @php
        $total7 = $harian->sum('n');
        $max = max(4, $harian->max('n'));
        $titik = [];
        foreach ($harian as $i => $h) $titik[] = [round(56 + $i * 86), round(170 - $h['n'] / $max * 140)];
        $garis = collect($titik)->map(fn ($t) => $t[0] . ',' . $t[1])->implode(' ');
      @endphp
      <div class="flex flex-wrap items-start justify-between gap-2">
        <div><h2 class="judul text-lg font-bold">Pendaftaran 7 hari terakhir</h2><p class="text-xs text-[#6B5A50]">Jumlah penari yang mengirim data diri per hari</p></div>
        <span class="chip bg-[#8B1E2D]/10 text-[#8B1E2D]">{{ number_format($total7) }} pendaftar</span>
      </div>
      <svg viewBox="0 0 620 215" class="mt-4 w-full" role="img" aria-label="Grafik jumlah pendaftaran per hari selama 7 hari terakhir. Total {{ $total7 }} pendaftar.">
        <defs><linearGradient id="gradArea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8B1E2D" stop-opacity=".22"/><stop offset="1" stop-color="#8B1E2D" stop-opacity="0"/></linearGradient></defs>
        @foreach ([0, .25, .5, .75, 1] as $f)
          @php $y = round(170 - $f * 140); @endphp
          <line x1="48" x2="600" y1="{{ $y }}" y2="{{ $y }}" stroke="#EFE2D0" stroke-width="1"/>
          <text x="38" y="{{ $y + 4 }}" text-anchor="end" font-size="11" fill="#6B5A50">{{ round($max * $f) }}</text>
        @endforeach
        <polygon points="{{ $titik[0][0] }},170 {{ $garis }} {{ $titik[6][0] }},170" fill="url(#gradArea)"/>
        <polyline points="{{ $garis }}" fill="none" stroke="#8B1E2D" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>
        @foreach ($harian as $i => $h)
          <circle cx="{{ $titik[$i][0] }}" cy="{{ $titik[$i][1] }}" r="4.5" fill="#FFFCF8" stroke="#8B1E2D" stroke-width="2.5"><title>{{ $h['tgl'] }}: {{ $h['n'] }} pendaftar</title></circle>
          <text x="{{ $titik[$i][0] }}" y="198" text-anchor="middle" font-size="11" fill="#6B5A50">{{ $h['tgl'] }}</text>
        @endforeach
      </svg>
      @if ($total7 === 0)<p class="text-center text-sm text-[#6B5A50]">Belum ada pendaftaran dalam 7 hari terakhir.</p>@endif
    </div>

    <div class="box">
      <div class="flex items-center justify-between"><h2 class="judul text-lg font-bold">Aktivitas terbaru</h2><a href="{{ route('admin.penari') }}" class="text-xs font-semibold text-[#8B1E2D] hover:underline">Lihat semua</a></div>
      <ul class="mt-4 space-y-4">
        @forelse ($terbaru as $p)
          <li class="flex items-center gap-3">
            <img src="{{ asset('storage/' . $p->foto) }}" alt="" class="h-12 w-10 shrink-0 rounded-lg object-cover">
            <div class="min-w-0 flex-1 text-sm"><div class="truncate font-semibold">{{ $p->nama }}</div>
              <div class="truncate text-xs text-[#6B5A50]">{{ $p->label_jalur }} &middot; {{ $p->wilayah }}</div></div>
            <span class="shrink-0 text-xs text-[#6B5A50]">{{ $p->created_at->locale('id')->diffForHumans(short: true) }}</span>
          </li>
        @empty
          <li class="text-sm text-[#6B5A50]">Belum ada pendaftaran.</li>
        @endforelse
      </ul>
    </div>
  </div>

  {{-- Per jalur dan aksi cepat --}}
  <div class="grid gap-4 lg:grid-cols-3">
    <div class="box lg:col-span-2">
      <h2 class="judul text-lg font-bold">Ringkasan per jalur</h2>
      <p class="text-xs text-[#6B5A50]">Kode terpakai dibanding kode yang sudah dibuat</p>
      <div class="mt-5 space-y-5">
        @foreach ($perJalur as $j)
          <div>
            <div class="mb-1.5 flex items-center justify-between text-sm"><span class="font-semibold">{{ $j['label'] }}</span><span class="text-[#6B5A50]">{{ $j['dipakai'] }} / {{ $j['dibuat'] }} kode</span></div>
            <div class="h-2.5 rounded-full bg-[#F0E2D0]"><div class="h-2.5 rounded-full bg-[#C9962B]" style="width:{{ $pct($j['dipakai'], $j['dibuat']) }}%"></div></div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="box">
      <h2 class="judul text-lg font-bold">Aksi cepat</h2>
      <ul class="mt-3 divide-y divide-[#F0E2D0]">
        @foreach ([['Buat kode & cetak kartu', 'admin.kuota', 'key'], ['Tinjau data penari', 'admin.penari', 'users'], ['Daftar semua kode', 'admin.kode', 'list'], ['Atur template sertifikat', 'admin.template', 'file']] as [$l, $r, $ic])
          <li><a href="{{ route($r) }}" class="-mx-2 flex items-center gap-3 rounded-lg px-2 py-3 text-sm font-medium transition hover:bg-[#F0E2D0]/50">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#8B1E2D]/10 text-[#8B1E2D]"><x-icon :n="$ic" class="h-5 w-5"/></span>
            <span class="flex-1">{{ $l }}</span><x-icon n="chevron" class="h-4 w-4 text-[#9B8878]"/></a></li>
        @endforeach
      </ul>
    </div>
  </div>

  {{-- Rekap per kecamatan --}}
  <div class="box">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
      <div><h2 class="judul text-lg font-bold">Rekap per kecamatan dan jalur</h2><p class="text-xs text-[#6B5A50]">Angka tebal: terpakai / dibuat. Di bawahnya: kuota yang ditetapkan.</p></div>
    </div>
    <div class="overflow-x-auto"><table class="w-full min-w-[640px]">
      <tr><th class="th">Kecamatan</th>@foreach ($jl as $l)<th class="th">{{ $l }}</th>@endforeach</tr>
      @foreach ($kec as $k)
        <tr><td class="td font-medium">{{ $k->nama }} <span class="ml-1 text-xs font-normal text-[#6B5A50]">{{ $k->kode_singkat }}</span></td>
          @foreach ($jl as $j => $l)
            @php $key = $k->id . '|' . $j; $kd = $kode[$key] ?? null; $kq = $kuota[$key]->kuota ?? null; @endphp
            <td class="td"><div class="font-semibold">{{ (int) ($kd->dipakai ?? 0) }} / {{ (int) ($kd->dibuat ?? 0) }}</div>
              <div class="text-xs text-[#6B5A50]">{{ $kq !== null ? 'kuota ' . $kq : 'kuota belum pasti' }}</div></td>
          @endforeach
        </tr>
      @endforeach
    </table></div>
  </div>
</x-panel>