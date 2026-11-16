<x-panel title="Form Data Diri Penari" sub="Lengkapi data diri dengan benar dan terbaru.">
  <a href="{{ route('penari.dashboard') }}" class="text-sm font-semibold text-[#8B1E2D]">&larr; Kembali ke Dashboard</a>
  <form method="post" action="{{ route('penari.simpan') }}" enctype="multipart/form-data" x-data="{foto:''}" class="box grid gap-4 md:grid-cols-[1fr_1fr_220px]">@csrf
    <div><label class="lbl" for="kode">Kode unik</label><input id="kode" name="kode" value="{{ old('kode') }}" class="inp font-mono uppercase" placeholder="KBT-XXXX-XXXX" autocomplete="off" required></div>
    <div><label class="lbl" for="kec">Kecamatan</label><select id="kec" name="kecamatan_id" class="inp" required>
      <option value="">Pilih kecamatan</option>@foreach ($kecamatan as $k)<option value="{{ $k->id }}" @selected(old('kecamatan_id') == $k->id)>{{ $k->nama }}</option>@endforeach</select></div>

    <div class="md:row-span-4">
      <label class="lbl" for="foto">Pas foto 3x4</label>
      <label for="foto" class="flex min-h-[220px] cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-[#D9C3A3] bg-white p-3 text-center text-sm text-[#6B5A50] hover:border-[#8B1E2D]">
        <img x-show="foto" :src="foto" alt="Pratinjau foto" class="h-40 w-[120px] rounded-lg object-cover">
        <span x-show="!foto">Unggah JPG/PNG<br>maks. 2 MB</span>
      </label>
      <input id="foto" type="file" name="foto" accept="image/png,image/jpeg" class="sr-only" required @change="foto = URL.createObjectURL($event.target.files[0])">
    </div>

    <div><label class="lbl" for="nama">Nama lengkap</label><input id="nama" name="nama" value="{{ old('nama') }}" class="inp" required></div>
    <div><label class="lbl" for="tgl">Tanggal lahir</label><input id="tgl" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="inp" required></div>
    <div class="md:col-span-2"><label class="lbl" for="alamat">Alamat</label><input id="alamat" name="alamat" value="{{ old('alamat') }}" class="inp" required></div>
    <div><label class="lbl" for="wa">No. WhatsApp</label><input id="wa" name="no_wa" value="{{ old('no_wa') }}" class="inp" inputmode="tel"></div>
    <div><label class="lbl" for="sanggar">Sanggar / sekolah</label><input id="sanggar" name="sanggar" value="{{ old('sanggar') }}" class="inp"></div>

    <div class="flex flex-col-reverse gap-3 md:col-span-3 md:flex-row md:items-center md:justify-between">
      <p class="text-sm text-[#6B5A50]">Kode diperiksa otomatis. Jika sah, kamu langsung terdaftar.</p>
      <button class="btn">Kirim data</button>
    </div>
  </form>
</x-panel>