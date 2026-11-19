<x-panel title="Edit Data Penari" sub="Koreksi data yang salah ketik. Sertifikat memakai data terbaru.">
  <a href="{{ route('admin.penari') }}" class="text-sm font-semibold text-[#8B1E2D]">&larr; Kembali ke daftar</a>

  @if (session('sandi_baru'))
    <div class="rounded-2xl bg-amber-100 p-4 text-sm text-amber-900" x-data="{salin:false}">
      <p>Password sementara untuk <b>{{ $penari->nama }}</b> (hanya tampil sekali, catat sekarang):</p>
      <div class="mt-2 flex flex-wrap items-center gap-3">
        <code class="rounded-lg bg-white px-4 py-2 text-lg font-bold tracking-widest">{{ session('sandi_baru') }}</code>
        <button type="button" class="btn-o btn-s" @click="navigator.clipboard.writeText('{{ session('sandi_baru') }}'); salin = true" x-text="salin ? 'Tersalin' : 'Salin'"></button>
      </div>
      <p class="mt-2 text-xs">Kirim ke penari lewat WhatsApp dan minta dia menggantinya di Pengaturan Akun.</p>
    </div>
  @endif

  <div class="grid gap-5 lg:grid-cols-[260px_1fr]">
    <div class="box space-y-3">
      <img src="{{ asset('storage/' . $penari->foto) }}" alt="Foto {{ $penari->nama }}" class="aspect-[3/4] w-full rounded-xl object-cover">
      <div><x-badge :status="$penari->status" /></div>
      @if ($penari->catatan)<p class="text-xs text-[#6B5A50]">{{ $penari->catatan }}</p>@endif
      <dl class="space-y-2 text-sm">
        <div><dt class="text-xs text-[#6B5A50]">Jalur (dari kode)</dt><dd class="font-semibold">{{ $penari->label_jalur }}</dd></div>
        <div><dt class="text-xs text-[#6B5A50]">Kecamatan / asal (dari kode)</dt><dd class="font-semibold">{{ $penari->wilayah }}</dd></div>
        <div><dt class="text-xs text-[#6B5A50]">Kode yang dipakai</dt><dd class="font-mono text-xs font-semibold">{{ $penari->kode->kode }}</dd></div>
        <div><dt class="text-xs text-[#6B5A50]">Akun</dt><dd class="break-all">{{ $penari->user->email }}</dd></div>
        <div><dt class="text-xs text-[#6B5A50]">Mendaftar</dt><dd>{{ $penari->created_at->format('d M Y, H:i') }}</dd></div>
      </dl>
      <div class="flex flex-wrap gap-2">
        @if ($penari->status !== 'aktif')<form method="post" action="{{ route('admin.penari.setujui', $penari) }}">@csrf<button class="btn btn-s">Setujui</button></form>@endif
        @if ($penari->status !== 'dicabut')<form method="post" action="{{ route('admin.penari.cabut', $penari) }}" onsubmit="return confirm('Cabut penari ini? Kodenya ikut dicabut.')">@csrf<button class="btn-o btn-s">Cabut</button></form>@endif
        <form method="post" action="{{ route('admin.penari.reset-password', $penari) }}" onsubmit="return confirm('Reset password {{ $penari->nama }}? Password lama tidak berlaku lagi.')">@csrf<button class="btn-o btn-s">Reset password</button></form>
      </div>
    </div>

    <form method="post" action="{{ route('admin.penari.update', $penari) }}" enctype="multipart/form-data" x-data="{foto:''}" class="box grid gap-4 sm:grid-cols-2">@csrf @method('PUT')
      <div><label class="lbl" for="nama">Nama lengkap</label><input id="nama" name="nama" value="{{ old('nama', $penari->nama) }}" class="inp" required></div>
      <div><label class="lbl" for="tgl">Tanggal lahir</label><input id="tgl" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $penari->tanggal_lahir->format('Y-m-d')) }}" class="inp" required></div>
      <div><label class="lbl" for="wa">No. WhatsApp</label><input id="wa" name="no_wa" value="{{ old('no_wa', $penari->no_wa) }}" class="inp" inputmode="tel"></div>
      <div><label class="lbl" for="sanggar">Sanggar (opsional)</label><input id="sanggar" name="sanggar" value="{{ old('sanggar', $penari->sanggar) }}" class="inp"></div>
      <div class="sm:col-span-2"><label class="lbl" for="alamat">Alamat</label><input id="alamat" name="alamat" value="{{ old('alamat', $penari->alamat) }}" class="inp" required></div>

      @if ($penari->jalur === 'sekolah')
        <div><label class="lbl" for="sekolah">Sekolah</label><input id="sekolah" name="sekolah" value="{{ old('sekolah', $penari->sekolah) }}" class="inp" required></div>
        <div><label class="lbl" for="kelas">Kelas</label><input id="kelas" name="kelas" value="{{ old('kelas', $penari->kelas) }}" class="inp" required></div>
      @elseif ($penari->jalur === 'desa')
               <div class="sm:col-span-2"><label class="lbl" for="desa">Desa / kelurahan</label>
          <input id="desa" name="desa" value="{{ old('desa', $penari->desa) }}" class="inp" required maxlength="100">
          <p class="mt-1 text-xs text-[#6B5A50]">Penulisan dirapikan otomatis saat disimpan.</p></div>
      @elseif ($penari->jalur === 'umum')
        <div class="sm:col-span-2"><label class="lbl" for="instansi">Instansi / komunitas (opsional)</label><input id="instansi" name="instansi" value="{{ old('instansi', $penari->instansi) }}" class="inp"></div>
      @else
        <div><label class="lbl" for="provinsi">Provinsi</label><select id="provinsi" name="provinsi" class="inp" required>
          @foreach ($provinsi as $p)<option value="{{ $p }}" @selected(old('provinsi', $penari->provinsi) === $p)>{{ $p }}</option>@endforeach</select></div>
        <div><label class="lbl" for="kota">Kota / kabupaten</label><input id="kota" name="kota" value="{{ old('kota', $penari->kota) }}" class="inp" required></div>
        <div class="sm:col-span-2"><label class="lbl" for="instansi">Sekolah / instansi (opsional)</label><input id="instansi" name="instansi" value="{{ old('instansi', $penari->instansi) }}" class="inp"></div>
      @endif

      <div class="sm:col-span-2"><label class="lbl" for="foto">Ganti pas foto (kosongkan jika tidak diganti, maks. 10 MB)</label>
        <div class="flex items-center gap-4"><img x-show="foto" :src="foto" alt="Pratinjau" class="h-24 w-[72px] rounded-lg object-cover">
          <input id="foto" type="file" name="foto" accept="image/png,image/jpeg" class="text-sm" @change="foto = URL.createObjectURL($event.target.files[0])"></div></div>
      <div class="flex justify-end gap-2 sm:col-span-2"><a href="{{ route('admin.penari') }}" class="btn-o">Batal</a><button class="btn">Simpan perubahan</button></div>
    </form>
  </div>
</x-panel>