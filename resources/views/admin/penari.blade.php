<x-panel title="Manajemen Penari" sub="Penari dengan kode sah otomatis terverifikasi. Admin menangani pengecualian dan koreksi data.">
  @php $jalurLabel = collect(\App\Models\KodePenari::JALUR)->map(fn ($j) => $j['label']); @endphp
  <form method="get" class="flex flex-col gap-2 lg:flex-row">
    <input class="inp lg:max-w-xs" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, WhatsApp, desa, atau kode">
    <select class="inp lg:max-w-[180px]" name="jalur" onchange="this.form.submit()"><option value="">Semua jalur</option>
      @foreach ($jalurLabel as $j => $l)<option value="{{ $j }}" @selected(request('jalur') === $j)>{{ $l }}</option>@endforeach</select>
    <select class="inp lg:max-w-[190px]" name="kecamatan" onchange="this.form.submit()"><option value="">Semua kecamatan</option>
      @foreach ($kecamatan as $k)<option value="{{ $k->id }}" @selected(request('kecamatan') == $k->id)>{{ $k->nama }}</option>@endforeach</select>
    <select class="inp lg:max-w-[210px]" name="status" onchange="this.form.submit()"><option value="">Semua status ({{ $hitung->sum() }})</option>
      @foreach (['aktif' => 'Terverifikasi', 'perlu_ditinjau' => 'Perlu ditinjau', 'dicabut' => 'Dicabut'] as $s => $l)
        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $l }} ({{ $hitung[$s] ?? 0 }})</option>@endforeach</select>
    <button class="btn-o">Cari</button>
    @if (request()->hasAny(['q', 'jalur', 'kecamatan', 'status']))<a href="{{ route('admin.penari') }}" class="inline-flex h-11 items-center px-3 text-sm font-semibold text-[#8B1E2D]">Reset</a>@endif
  </form>

  <div class="box flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-[#6B5A50]">Ekspor mengikuti filter di atas ({{ number_format($penari->total()) }} data).</p>
    <div class="flex gap-2">
      <a href="{{ route('admin.penari.excel', request()->query()) }}" class="btn">Unduh Excel</a>
      <a href="{{ route('admin.penari.pdf', request()->query()) }}" class="btn-o">Unduh PDF</a>
    </div>
  </div>

  <div class="box"><div class="overflow-x-auto"><table class="w-full min-w-[900px]">
    <tr><th class="th">No</th><th class="th">Foto</th><th class="th">Nama penari</th><th class="th">Jalur</th><th class="th">Asal</th><th class="th">Kode</th><th class="th">Status</th><th class="th">Aksi</th></tr>
    @forelse ($penari as $p)
    <tr><td class="td">{{ $penari->firstItem() + $loop->index }}</td>
      <td class="td"><img src="{{ asset('storage/' . $p->foto) }}" alt="" class="h-12 w-9 rounded object-cover"></td>
      <td class="td font-semibold">{{ $p->nama }}@if ($p->sanggar)<div class="text-xs font-normal text-[#6B5A50]">Sanggar {{ $p->sanggar }}</div>@endif</td>
      <td class="td">{{ $p->label_jalur }}</td>
      <td class="td">{{ $p->jalur === 'desa' ? 'Desa ' . $p->desa . ', ' : '' }}{{ $p->wilayah }}</td>
      <td class="td font-mono text-xs">{{ $p->kode->kode }}</td>
      <td class="td"><x-badge :status="$p->status" />@if ($p->catatan)<div class="mt-1 max-w-[200px] text-xs text-[#6B5A50]">{{ $p->catatan }}</div>@endif</td>
      <td class="td"><div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.penari.edit', $p) }}" class="btn-o btn-s">Edit</a>
        @if ($p->status !== 'aktif')<form method="post" action="{{ route('admin.penari.setujui', $p) }}">@csrf<button class="btn btn-s">Setujui</button></form>@endif
        @if ($p->status !== 'dicabut')<form method="post" action="{{ route('admin.penari.cabut', $p) }}" onsubmit="return confirm('Cabut penari ini? Kodenya ikut dicabut.')">@csrf<button class="btn-o btn-s">Cabut</button></form>@endif
      </div></td></tr>
    @empty <tr><td class="td text-[#6B5A50]" colspan="8">Tidak ada data yang cocok.</td></tr>
    @endforelse
  </table></div>
  <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-sm text-[#6B5A50]">
    <span>Menampilkan {{ $penari->firstItem() ?? 0 }}-{{ $penari->lastItem() ?? 0 }} dari {{ $penari->total() }} data</span>{{ $penari->links() }}</div></div>
</x-panel>