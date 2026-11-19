<x-panel title="Kuota & Kode" sub="Isi jumlah kode tiap kecamatan, lalu buat dan cetak kartunya sekaligus.">
  @php
    $huruf = ['sekolah' => 'S', 'desa' => 'D', 'umum' => 'U'][$jalur];
    $penjelasan = [
      'sekolah' => 'Peserta tampil atas nama sekolah. Asal sekolah sama dengan kecamatan tempat seleksi.',
      'desa' => 'Peserta tampil atas nama desa. Tes tetap di kecamatannya, desa dipilih penari saat mengisi data.',
      'umum' => 'Peserta umum (ibu-ibu dan dewasa) yang tes di kecamatannya.',
    ][$jalur];
  @endphp

  <div class="box">
    <div class="text-xs text-[#6B5A50]">Kode dibuat (semua jalur) dari target</div>
    <div class="judul text-2xl font-bold">{{ number_format($totalDibuat) }} / {{ number_format($target) }}</div>
    <div class="text-xs text-[#6B5A50]">{{ $totalDibuat <= $target ? 'Sisa ' . number_format($target - $totalDibuat) . ' kode untuk dibagi setelah seleksi' : 'Melebihi target ' . number_format($totalDibuat - $target) }}</div>
  </div>

  @include('admin._tab-jalur', ['aktif' => $jalur])
  <p class="text-sm text-[#6B5A50]">{{ $penjelasan }}</p>

  <form method="post" action="{{ route('admin.kuota.simpan') }}" class="box space-y-4"
        x-data="{segarkan:false}" @visibilitychange.document="if (segarkan && !document.hidden) location.reload()">@csrf
    <input type="hidden" name="jalur" value="{{ $jalur }}">
    <div class="overflow-x-auto"><table class="w-full min-w-[820px]">
      <tr><th class="th w-8"></th><th class="th">Kecamatan</th><th class="th">Prefix kode</th><th class="th w-40">Jumlah kode</th><th class="th">Dibuat</th><th class="th">Terpakai</th><th class="th">Aksi</th></tr>
      @foreach ($kec as $k)
        @php $lebih = $k->kuota !== null ? $k->dibuat - $k->kuota : 0; @endphp
        <tr>
          <td class="td"><input type="checkbox" name="kecamatan[]" value="{{ $k->id }}" class="pilih-kec rounded border-[#CDBFA6] text-[#8B1E2D]" aria-label="Pilih {{ $k->nama }} untuk dicetak ulang"></td>
          <td class="td">{{ $k->nama }}</td>
          <td class="td font-mono text-xs">{{ $k->kode_singkat }}-{{ $huruf }}</td>
          <td class="td"><input class="inp !h-9" type="number" min="0" name="kuota[{{ $k->id }}]" value="{{ old('kuota.' . $k->id, $k->kuota) }}" placeholder="belum pasti"></td>
          <td class="td">{{ $k->dibuat }}</td>
          <td class="td">{{ $k->dipakai }}</td>
          <td class="td"><div class="flex flex-wrap gap-2">
            @if ($lebih > 0)<button type="submit" form="cab-{{ $k->id }}" class="btn-o btn-s">Cabut {{ $lebih }} lebih</button>@endif
            @if ($k->dibuat > $k->dipakai)<a href="{{ route('admin.kuota.cetak', ['kecamatan' => [$k->id], 'jalur' => $jalur]) }}" target="_blank" class="btn-o btn-s">Cetak ulang</a>@endif
          </div></td>
        </tr>
      @endforeach
    </table></div>

    <div class="flex flex-wrap items-center justify-between gap-3">
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" class="rounded border-[#CDBFA6] text-[#8B1E2D]"
               @change="$el.closest('form').querySelectorAll('.pilih-kec').forEach(c => c.checked = $el.checked)"> Pilih semua kecamatan
      </label>
      <div class="flex flex-wrap gap-2">
        <button class="btn-o">Simpan saja</button>
        <button class="btn" formaction="{{ route('admin.kuota.buat-cetak') }}" formtarget="_blank" @click="segarkan = true">Buat kode &amp; cetak kartu</button>
      </div>
    </div>
  </form>

  @foreach ($kec as $k)
    <form id="cab-{{ $k->id }}" method="post" action="{{ route('admin.kuota.cabut', ['kecamatan' => $k->id, 'jalur' => $jalur]) }}">@csrf</form>
  @endforeach

  <div class="box text-sm text-[#6B5A50]">
    <p><b>Buat kode &amp; cetak kartu</b> menyimpan jumlah, membuat kode yang kurang, lalu membuka PDF di tab baru berisi kartu yang baru dibuat ditambah kecamatan yang dicentang. Saat kamu kembali ke halaman ini, angkanya otomatis diperbarui.</p>
    <p class="mt-2">Mau menambah kode? Naikkan angka di kolom Jumlah kode, lalu klik tombol yang sama. Jumlah yang belum pasti boleh dikosongkan dan diisi setelah seleksi.</p>
  </div>
</x-panel>