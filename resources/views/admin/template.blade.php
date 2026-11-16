<x-panel title="Template sertifikat">
  <p class="text-sm text-[#6B5A50]">Unggah desain sertifikat kosong (landscape). Nama, kecamatan, dan nomor terisi otomatis dari data penari.</p>
  <form method="post" action="{{ route('admin.template.simpan') }}" enctype="multipart/form-data"
    x-data="{nama:{{ old('nama_y', $tpl->nama_y) }}, size:{{ old('nama_size', $tpl->nama_size) }}, info:{{ old('info_y', $tpl->info_y) }}, nomor:{{ old('nomor_y', $tpl->nomor_y) }}, warna:'{{ old('warna', $tpl->warna) }}', src:'{{ $tpl->gambar ? asset('storage/' . $tpl->gambar) : '' }}'}"
    class="grid gap-5 lg:grid-cols-[1fr_320px]">@csrf
    <div class="box">
      <div class="relative w-full overflow-hidden rounded border border-[#EADFCB] bg-[#F5EFE2]" style="aspect-ratio:297/210;container-type:inline-size">
        <img x-show="src" :src="src" alt="" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-x-0 text-center font-serif" :style="`top:${nama}%;font-size:${size/8.41}cqw;color:${warna}`">Nama Penari</div>
        <div class="absolute inset-x-0 text-center" :style="`top:${info}%;font-size:1.67cqw`">Kecamatan Kabat</div>
        <div class="absolute inset-x-0 text-center" :style="`top:${nomor}%;font-size:1.3cqw`">No. GS26/0001/2026</div>
      </div>
      <p class="mt-2 text-xs text-[#6B5A50]">Pratinjau posisi teks. Sertifikat PDF memakai posisi yang sama.</p>
    </div>
    <div class="box space-y-3">
      <div><label class="lbl" for="gambar">Desain sertifikat (JPG/PNG)</label>
        <input id="gambar" type="file" name="gambar" accept="image/png,image/jpeg" class="text-sm"
          @change="if($event.target.files[0]) src = URL.createObjectURL($event.target.files[0])"></div>
      <div><label class="lbl">Posisi nama (% dari atas): <span x-text="nama"></span></label><input type="range" min="0" max="100" step="0.5" name="nama_y" x-model="nama" class="w-full"></div>
      <div><label class="lbl">Ukuran font nama (pt)</label><input class="inp" type="number" min="12" max="120" name="nama_size" x-model="size"></div>
      <div><label class="lbl">Posisi kecamatan (%): <span x-text="info"></span></label><input type="range" min="0" max="100" step="0.5" name="info_y" x-model="info" class="w-full"></div>
      <div><label class="lbl">Posisi nomor (%): <span x-text="nomor"></span></label><input type="range" min="0" max="100" step="0.5" name="nomor_y" x-model="nomor" class="w-full"></div>
      <div><label class="lbl" for="warna">Warna nama</label><input id="warna" type="color" name="warna" x-model="warna" class="h-11 w-full rounded-lg border border-[#CDBFA6]"></div>
      <button class="btn w-full">Simpan template</button>
    </div>
  </form>
</x-panel>