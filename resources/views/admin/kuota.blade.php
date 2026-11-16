<x-panel title="Kuota & generate kode">
  <p class="text-sm text-[#6B5A50]">Isi kuota tiap kecamatan, simpan, lalu klik Generate. Kode dibuat otomatis oleh sistem sebanyak sisa kuota.</p>
  <div class="box">
    <div class="flex flex-wrap items-end justify-between gap-2"><div><div class="text-xs text-[#6B5A50]">Total kuota</div>
      <div class="text-2xl font-bold">{{ number_format($kec->sum('kuota')) }} / {{ number_format($target) }}</div></div>
      <div class="text-sm {{ $kec->sum('kuota') == $target ? 'text-green-800' : 'text-amber-800' }}">{{ $kec->sum('kuota') == $target ? 'Pas dengan target' : 'Selisih ' . abs($target - $kec->sum('kuota')) }}</div></div>
  </div>
  <form method="post" action="{{ route('admin.kuota.simpan') }}" class="box space-y-4">@csrf
    <div class="overflow-x-auto"><table class="w-full min-w-[720px]">
      <tr><th class="th">Kecamatan</th><th class="th">Prefix</th><th class="th w-28">Kuota</th><th class="th">Kode dibuat</th><th class="th">Terpakai</th><th class="th">Aksi</th></tr>
      @foreach ($kec as $k)
      @php $sisa = $k->kuota - $k->kode_dibuat; @endphp
      <tr><td class="td">{{ $k->nama }}</td><td class="td">{{ $k->kode_singkat }}</td>
        <td class="td"><input class="inp !h-9" type="number" min="0" name="kuota[{{ $k->id }}]" value="{{ old('kuota.' . $k->id, $k->kuota) }}"></td>
        <td class="td">{{ $k->kode_dibuat }} / {{ $k->kuota }}</td><td class="td">{{ $k->kode_dipakai }}</td>
        <td class="td"><div class="flex flex-wrap gap-2">
          @if ($sisa > 0)<button type="submit" form="gen-{{ $k->id }}" class="btn btn-s">Generate {{ $sisa }} kode</button>
          @elseif ($sisa < 0)<button type="submit" form="cab-{{ $k->id }}" class="btn-o btn-s">Cabut {{ -$sisa }} kelebihan</button>@endif
          @if ($k->kode_dibuat > $k->kode_dipakai)<a href="{{ route('admin.kode.cetak', $k) }}" target="_blank" class="btn-o btn-s">Cetak kartu</a>@endif
        </div></td></tr>
      @endforeach
    </table></div>
    <div class="flex justify-end"><button class="btn">Simpan kuota</button></div>
  </form>
  @foreach ($kec as $k)
    <form id="gen-{{ $k->id }}" method="post" action="{{ route('admin.kode.generate', $k) }}">@csrf</form>
    <form id="cab-{{ $k->id }}" method="post" action="{{ route('admin.kode.cabut', $k) }}">@csrf</form>
  @endforeach
  <p class="text-sm text-[#6B5A50]">Kuota diturunkan? Tombol cabut hanya mencabut kode yang belum dipakai. Kode yang sudah dipakai penari tidak pernah disentuh.</p>
</x-panel>