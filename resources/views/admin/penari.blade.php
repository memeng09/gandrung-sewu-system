<x-panel title="Manajemen Penari" sub="Penari dengan kode sah otomatis terverifikasi. Admin hanya menangani pengecualian.">
  <form method="get" class="flex flex-col gap-2 sm:flex-row">
    <input class="inp sm:max-w-xs" name="q" value="{{ request('q') }}" placeholder="Cari nama atau kode">
    <select class="inp sm:max-w-[220px]" name="status" onchange="this.form.submit()">
      <option value="">Semua status ({{ $hitung->sum() }})</option>
      @foreach (['aktif' => 'Terverifikasi', 'perlu_ditinjau' => 'Perlu ditinjau', 'dicabut' => 'Dicabut'] as $s => $l)
        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $l }} ({{ $hitung[$s] ?? 0 }})</option>
      @endforeach
    </select>
    <button class="btn-o">Cari</button>
  </form>
  <div class="box"><div class="overflow-x-auto"><table class="w-full min-w-[760px]">
    <tr><th class="th">No</th><th class="th">Foto</th><th class="th">Nama penari</th><th class="th">Kecamatan</th><th class="th">Kode</th><th class="th">Status</th><th class="th">Aksi</th></tr>
    @forelse ($penari as $p)
    <tr><td class="td">{{ $penari->firstItem() + $loop->index }}</td>
      <td class="td"><img src="{{ asset('storage/' . $p->foto) }}" alt="" class="h-12 w-9 rounded object-cover"></td>
      <td class="td font-semibold">{{ $p->nama }}</td><td class="td">{{ $p->kecamatan->nama }}</td>
      <td class="td font-mono text-xs">{{ $p->kode->kode }}</td>
      <td class="td"><x-badge :status="$p->status" />@if ($p->catatan)<div class="mt-1 max-w-[200px] text-xs text-[#6B5A50]">{{ $p->catatan }}</div>@endif</td>
      <td class="td"><div class="flex gap-2">
        @if ($p->status !== 'aktif')<form method="post" action="{{ route('admin.penari.setujui', $p) }}">@csrf<button class="btn btn-s">Setujui</button></form>@endif
        @if ($p->status !== 'dicabut')<form method="post" action="{{ route('admin.penari.cabut', $p) }}" onsubmit="return confirm('Cabut penari ini?')">@csrf<button class="btn-o btn-s">Cabut</button></form>@endif
      </div></td></tr>
    @empty <tr><td class="td text-[#6B5A50]" colspan="7">Belum ada penari yang mendaftar.</td></tr>
    @endforelse
  </table></div>
  <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-sm text-[#6B5A50]">
    <span>Menampilkan {{ $penari->firstItem() ?? 0 }}-{{ $penari->lastItem() ?? 0 }} dari {{ $penari->total() }} data</span>{{ $penari->links() }}</div></div>
</x-panel>