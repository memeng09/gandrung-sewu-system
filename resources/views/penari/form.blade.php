<x-panel title="Data Diri Penari" sub="Langkah 2 dari 2: lengkapi data dengan benar dan terbaru.">
  <div class="box flex flex-wrap items-center justify-between gap-3">
    <div>
      <div class="flex items-center gap-2 font-semibold text-green-800"><x-icon n="check" class="h-5 w-5"/>Kode sah</div>
      <div class="mt-1 font-mono text-sm">{{ $kode->kode }}</div>
      <div class="mt-2 flex flex-wrap gap-2 text-xs">
        <span class="rounded-md bg-[#8B1E2D]/10 px-3 py-1 font-semibold text-[#8B1E2D]">Jalur {{ $kode->label_jalur }}</span>
        <span class="rounded-md bg-[#C9962B]/15 px-3 py-1 font-semibold text-[#6B4A10]">{{ $kode->asal }}</span>
      </div>
    </div>
  </div>

  <form method="post" action="{{ route('penari.simpan') }}" enctype="multipart/form-data" x-data="{foto:''}" class="box grid gap-4 sm:grid-cols-2">@csrf
    <div><label class="lbl" for="nama">Nama lengkap</label><input id="nama" name="nama" value="{{ old('nama') }}" class="inp" required autocomplete="name"></div>
    <div><label class="lbl" for="tgl">Tanggal lahir</label><input id="tgl" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="inp" required></div>
    <div><label class="lbl" for="wa">No. WhatsApp</label><input id="wa" name="no_wa" value="{{ old('no_wa') }}" class="inp" inputmode="tel" placeholder="08xx xxxx xxxx"></div>
    <div><label class="lbl" for="sanggar">Sanggar tari (opsional)</label><input id="sanggar" name="sanggar" value="{{ old('sanggar') }}" class="inp"></div>

    @if ($kode->jalur === 'sekolah')
      <div><label class="lbl" for="sekolah">Sekolah</label><input id="sekolah" name="sekolah" value="{{ old('sekolah') }}" class="inp" required></div>
      <div><label class="lbl" for="kelas">Kelas</label><input id="kelas" name="kelas" value="{{ old('kelas') }}" class="inp" required></div>
       @elseif ($kode->jalur === 'desa')
      <div class="sm:col-span-2">
        <label class="lbl" for="desa">Desa / kelurahan (Kecamatan {{ $kode->kecamatan->nama }})</label>
        <input id="desa" name="desa" value="{{ old('desa') }}" class="inp" required maxlength="100" autocomplete="off" autocapitalize="words"
               placeholder="Contoh: Rejosari"
               @blur="$el.value = $el.value.trim().toLowerCase().replace(/(^|\s)(\S)/g, (m, a, b) => a + b.toUpperCase())">
        <p class="mt-1 text-xs text-[#6B5A50]">Tulis nama desa tanpa kata "Desa". Huruf besar di awal tiap kata dirapikan otomatis.</p>
      </div>
      <div><label class="lbl" for="sekolah">Sekolah (isi jika pelajar)</label><input id="sekolah" name="sekolah" value="{{ old('sekolah') }}" class="inp"></div>
      <div><label class="lbl" for="kelas">Kelas</label><input id="kelas" name="kelas" value="{{ old('kelas') }}" class="inp"></div>
    @elseif ($kode->jalur === 'umum')
      <div class="sm:col-span-2"><label class="lbl" for="instansi">Instansi / komunitas (opsional)</label><input id="instansi" name="instansi" value="{{ old('instansi') }}" class="inp"></div>
    @else
      <div><label class="lbl" for="provinsi">Provinsi</label><select id="provinsi" name="provinsi" class="inp" required><option value="">Pilih provinsi</option>
        @foreach ($provinsi as $p)<option value="{{ $p }}" @selected(old('provinsi') === $p)>{{ $p }}</option>@endforeach</select></div>
      <div><label class="lbl" for="kota">Kota / kabupaten</label><input id="kota" name="kota" value="{{ old('kota') }}" class="inp" required></div>
      <div class="sm:col-span-2"><label class="lbl" for="instansi">Sekolah / instansi (opsional)</label><input id="instansi" name="instansi" value="{{ old('instansi') }}" class="inp"></div>
    @endif

    <div class="sm:col-span-2"><label class="lbl" for="alamat">Alamat</label><input id="alamat" name="alamat" value="{{ old('alamat') }}" class="inp" required autocomplete="street-address"></div>

    <div class="sm:col-span-2"><label class="lbl" for="foto">Pas foto 3x4 (JPG/PNG, maks. 10 MB, ukuran dikecilkan otomatis)</label>
      <div class="flex items-center gap-4"><img x-show="foto" :src="foto" alt="Pratinjau foto" class="h-28 w-[84px] rounded-lg object-cover">
        <input id="foto" type="file" name="foto" accept="image/png,image/jpeg" class="text-sm" required @change="foto = URL.createObjectURL($event.target.files[0])"></div></div>

    <div class="flex flex-col-reverse gap-3 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
      <p class="text-sm text-[#6B5A50]">Jalur dan asal sudah terkunci dari kode. Setelah dikirim, kamu langsung terdaftar.</p>
      <button class="btn">Kirim data</button></div>
  </form>
</x-panel>