<x-panel title="Kuota & Kode" sub="Peserta luar Banyuwangi dipilih juri dari video, jadi tidak ada kuota tetap.">
  <div class="box">
    <div class="text-xs text-[#6B5A50]">Kode dibuat (semua jalur) dari target</div>
    <div class="judul text-2xl font-bold">{{ number_format($totalDibuat) }} / {{ number_format($target) }}</div>
  </div>

  @include('admin._tab-jalur', ['aktif' => 'luar'])

  <form method="post" action="{{ route('admin.kuota.luar.simpan') }}" class="box grid gap-3 sm:grid-cols-[1fr_160px_auto] sm:items-end">@csrf
    <div><label class="lbl" for="nama">Kelompok baru (daerah asal)</label><input id="nama" name="nama" value="{{ old('nama') }}" class="inp" placeholder="Contoh: Sumenep" required></div>
    <div><label class="lbl" for="ks">Singkatan (2-5 huruf)</label><input id="ks" name="kode_singkat" value="{{ old('kode_singkat') }}" class="inp font-mono uppercase" maxlength="5" placeholder="SMN" required></div>
    <button class="btn">Buat kelompok</button>
  </form>

  @forelse ($kelompok as $g)
    @php $unused = $g->kodes->where('status', 'belum_dipakai'); @endphp
    <div class="box space-y-4" x-data="{salin:false}">
      <div class="flex flex-wrap items-center justify-between gap-2">
        <div><h2 class="judul text-lg font-bold">{{ $g->nama }} <span class="font-mono text-xs text-[#6B5A50]">{{ $g->kode_singkat }}-L</span></h2>
          <p class="text-sm text-[#6B5A50]">{{ $g->kodes->count() }} kode dibuat, {{ $g->kodes->where('status', 'dipakai')->count() }} terpakai</p></div>
        @if ($unused->count())
          <div class="flex gap-2">
            <button type="button" class="btn-o btn-s"
              @click="navigator.clipboard.writeText(@js($unused->map(fn ($k) => ($k->nama_penerima ? $k->nama_penerima . ': ' : '') . $k->kode)->implode("\n"))); salin = true"
              x-text="salin ? 'Tersalin' : 'Salin semua kode'"></button>
            <a href="{{ route('admin.kuota.luar.cetak', $g) }}" target="_blank" class="btn-o btn-s">Cetak kartu</a>
          </div>
        @endif
      </div>

      @if ($unused->count())
        <ul class="divide-y divide-[#F0E2D0] text-sm">
          @foreach ($unused as $k)
            <li class="flex flex-wrap items-center justify-between gap-2 py-2" x-data="{ok:false}">
              <span><span class="font-mono font-semibold">{{ $k->kode }}</span>@if ($k->nama_penerima)<span class="ml-2 text-[#6B5A50]">untuk {{ $k->nama_penerima }}</span>@endif</span>
              <button type="button" class="btn-o btn-s" @click="navigator.clipboard.writeText('{{ $k->kode }}'); ok = true" x-text="ok ? 'Tersalin' : 'Salin'"></button>
            </li>
          @endforeach
        </ul>
      @endif

      <form method="post" action="{{ route('admin.kuota.luar.tambah', $g) }}" class="grid gap-3 border-t border-[#F0E2D0] pt-4 sm:grid-cols-[1fr_120px_auto] sm:items-end">@csrf
        <div><label class="lbl">Nama penerima (satu per baris, opsional)</label><textarea name="nama" rows="2" class="inp !h-auto py-2" placeholder="Siti Aminah&#10;Dewi Lestari"></textarea></div>
        <div><label class="lbl">Atau jumlah</label><input type="number" name="jumlah" min="1" max="100" class="inp" placeholder="5"></div>
        <button class="btn">Tambah kode</button>
      </form>
    </div>
  @empty
    <div class="box text-center text-sm text-[#6B5A50]">Belum ada kelompok. Buat satu di atas, misalnya sesuai daerah asal peserta yang lolos video.</div>
  @endforelse

  <p class="text-sm text-[#6B5A50]">Kalau nama penerima diisi, nama di form pendaftaran harus cocok. Yang berbeda masuk antrean "Perlu ditinjau", jadi kode yang dikirim lewat WhatsApp tidak bisa dipakai orang lain.</p>
</x-panel>