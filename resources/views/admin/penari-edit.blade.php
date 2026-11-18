<x-panel title="Edit Data Penari" sub="Koreksi data yang salah ketik. Sertifikat memakai data terbaru.">
  <a href="{{ route('admin.penari') }}" class="text-sm font-semibold text-[#8B1E2D]">&larr; Kembali ke daftar</a>

  <div class="grid gap-5 lg:grid-cols-[260px_1fr]">
    <div class="space-y-4">
      <div class="box space-y-3">
        <img src="{{ asset('storage/' . $penari->foto) }}" alt="Foto {{ $penari->nama }}" class="aspect-[3/4] w-full rounded-xl object-cover">
        <div><x-badge :status="$penari->status" /></div>
        @if ($penari->catatan)<p class="text-xs text-[#6B5A50]">{{ $penari->catatan }}</p>@endif
        <dl class="space-y-1 text-sm">
          <div><dt class="text-xs text-[#6B5A50]">Kode yang dipakai</dt><dd class="font-mono text-xs font-semibold">{{ $penari->kode->kode }}</dd></div>
          <div><dt class="text-xs text-[#6B5A50]">Akun</dt><dd>{{ $penari->user->email }}</dd></div>
          <div><dt class="text-xs text-[#6B5A50]">Mendaftar</dt><dd>{{ $penari->created_at->format('d M Y, H:i') }}</dd></div>
        </dl>
        <div class="flex gap-2">
          @if ($penari->status !== 'aktif')<form method="post" action="{{ route('admin.penari.setujui', $penari) }}">@csrf<button class="btn btn-s">Setujui</button></form>@endif
          @if ($penari->status !== 'dicabut')<form method="post" action="{{ route('admin.penari.cabut', $penari) }}" onsubmit="return confirm('Cabut penari ini?')">@csrf<button class="btn-o btn-s">Cabut</button></form>@endif
        </div>
      </div>
    </div>

    <form method="post" action="{{ route('admin.penari.update', $penari) }}" enctype="multipart/form-data" x-data="{foto:''}" class="box grid gap-4 sm:grid-cols-2">@csrf @method('PUT')
      <div><label class="lbl" for="nama">Nama lengkap</label><input id="nama" name="nama" value="{{ old('nama', $penari->nama) }}" class="inp" required></div>
      <div><label class="lbl" for="tgl">Tanggal lahir</label><input id="tgl" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $penari->tanggal_lahir->format('Y-m-d')) }}" class="inp" required></div>
      <div><label class="lbl" for="kec">Kecamatan</label><select id="kec" name="kecamatan_id" class="inp" required>
        @foreach ($kecamatan as $k)<option value="{{ $k->id }}" @selected(old('kecamatan_id', $penari->kecamatan_id) == $k->id)>{{ $k->nama }}</option>@endforeach</select></div>
      <div><label class="lbl" for="wa">No. WhatsApp</label><input id="wa" name="no_wa" value="{{ old('no_wa', $penari->no_wa) }}" class="inp" inputmode="tel"></div>
      <div class="sm:col-span-2"><label class="lbl" for="alamat">Alamat</label><input id="alamat" name="alamat" value="{{ old('alamat', $penari->alamat) }}" class="inp" required></div>
      <div class="sm:col-span-2"><label class="lbl" for="sanggar">Sanggar / sekolah</label><input id="sanggar" name="sanggar" value="{{ old('sanggar', $penari->sanggar) }}" class="inp"></div>
      <div class="sm:col-span-2"><label class="lbl" for="foto">Ganti pas foto (kosongkan jika tidak diganti)</label>
        <div class="flex items-center gap-4"><img x-show="foto" :src="foto" alt="Pratinjau" class="h-24 w-[72px] rounded-lg object-cover">
          <input id="foto" type="file" name="foto" accept="image/png,image/jpeg" class="text-sm" @change="foto = URL.createObjectURL($event.target.files[0])"></div></div>
      <div class="flex justify-end gap-2 sm:col-span-2"><a href="{{ route('admin.penari') }}" class="btn-o">Batal</a><button class="btn">Simpan perubahan</button></div>
    </form>
  </div>
</x-panel>