<x-panel title="Daftar Kode" sub="Semua kode unik, status pemakaian, dan siapa pemakainya.">
  @php
    $label = ['belum_dipakai' => ['Tersedia', 'bg-sky-100 text-sky-900'], 'dipakai' => ['Terpakai', 'bg-green-100 text-green-900'], 'dicabut' => ['Dicabut', 'bg-red-100 text-red-900']];
  @endphp

  <div class="grid grid-cols-3 gap-3">
    @foreach ($label as $s => [$l, $cls])
      <a href="{{ route('admin.kode', ['status' => $s]) }}" class="box block hover:bg-white">
        <div class="text-xs text-[#6B5A50]">{{ $l }}</div>
        <div class="judul text-2xl font-bold sm:text-3xl">{{ number_format($hitung[$s] ?? 0) }}</div>
      </a>
    @endforeach
  </div>

  <form method="get" class="flex flex-col gap-2 sm:flex-row">
    <input class="inp sm:max-w-xs" name="q" value="{{ request('q') }}" placeholder="Cari kode, misalnya KBT-7K4M">
    <select class="inp sm:max-w-[200px]" name="kecamatan" onchange="this.form.submit()">
      <option value="">Semua kecamatan</option>
      @foreach ($kecamatan as $k)<option value="{{ $k->id }}" @selected(request('kecamatan') == $k->id)>{{ $k->nama }}</option>@endforeach
    </select>
    <select class="inp sm:max-w-[200px]" name="status" onchange="this.form.submit()">
      <option value="">Semua status</option>
      @foreach ($label as $s => [$l])<option value="{{ $s }}" @selected(request('status') === $s)>{{ $l }}</option>@endforeach
    </select>
    <button class="btn-o">Cari</button>
    @if (request()->hasAny(['q', 'kecamatan', 'status']))<a href="{{ route('admin.kode') }}" class="inline-flex h-11 items-center px-3 text-sm font-semibold text-[#8B1E2D]">Reset</a>@endif
  </form>

  <div class="box">
    <div class="overflow-x-auto"><table class="w-full min-w-[720px]">
      <tr><th class="th">Kode</th><th class="th">Kecamatan</th><th class="th">Status</th><th class="th">Dipakai oleh</th><th class="th">Waktu pakai</th><th class="th">Aksi</th></tr>
      @forelse ($kodes as $k)
        @php [$l, $cls] = $label[$k->status]; @endphp
        <tr>
          <td class="td font-mono text-xs font-semibold">{{ $k->kode }}</td>
          <td class="td">{{ $k->kecamatan->nama }}</td>
          <td class="td"><span class="inline-block rounded-md px-3 py-1 text-xs font-semibold {{ $cls }}">{{ $l }}</span></td>
          <td class="td">{{ $k->pemakai?->penari?->nama ?? ($k->pemakai?->name ?? '-') }}</td>
          <td class="td text-xs text-[#6B5A50]">{{ $k->dipakai_pada?->format('d M Y, H:i') ?? '-' }}</td>
          <td class="td">
            @if ($k->status === 'belum_dipakai')
              <form method="post" action="{{ route('admin.kode.cabut-satu', $k) }}" onsubmit="return confirm('Cabut kode {{ $k->kode }}? Kartu dengan kode ini tidak bisa dipakai lagi.')">@csrf<button class="btn-o btn-s">Cabut</button></form>
            @elseif ($k->status === 'dicabut' && !$k->dipakai_oleh)
              <form method="post" action="{{ route('admin.kode.aktifkan', $k) }}">@csrf<button class="btn-o btn-s">Aktifkan lagi</button></form>
            @else <span class="text-xs text-[#6B5A50]">-</span>@endif
          </td>
        </tr>
      @empty
        <tr><td class="td text-[#6B5A50]" colspan="6">Belum ada kode. Buat dulu di menu Kuota &amp; Kode.</td></tr>
      @endforelse
    </table></div>
    <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-sm text-[#6B5A50]">
      <span>Menampilkan {{ $kodes->firstItem() ?? 0 }}-{{ $kodes->lastItem() ?? 0 }} dari {{ number_format($kodes->total()) }} kode</span>{{ $kodes->links() }}</div>
  </div>
</x-panel>