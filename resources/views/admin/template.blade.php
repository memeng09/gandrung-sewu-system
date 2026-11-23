<x-panel title="Template Sertifikat" sub="Unggah desain latar tanpa teks, lalu susun teksnya. Geser kotak teks langsung di pratinjau.">
@php
  $fontCustom = \App\Models\SertifikatTemplate::fontCustom();
  $data = [
    'orientasi' => old('orientasi', $tpl->orientasi ?? 'landscape'),
    'elemen'    => $tpl->elemenAktif(),
    'src'       => $tpl->gambar ? asset('storage/' . $tpl->gambar) : '',
    'fonts'     => \App\Models\SertifikatTemplate::daftarFont(),
    'qrX'       => (float) old('qr_x', $tpl->qr_x ?? 78),
    'qrY'       => (float) old('qr_y', $tpl->qr_y ?? 86),
    'qrSize'    => (float) old('qr_size', $tpl->qr_size ?? 12),
  ];
@endphp
<style>
@foreach ($fontCustom as $f)
@@font-face{font-family:'{{ $f }}';src:url('{{ asset('fonts/sertifikat/' . $f . '.ttf') }}') format('truetype');}
@endforeach
</style>
<script>
function editorSertifikat(d) {
  d = d || {};
  return {
    orientasi: d.orientasi || 'landscape',
    elemen: Array.isArray(d.elemen) ? d.elemen : [],
    src: d.src || '',
    fonts: d.fonts || ['Times', 'Helvetica', 'DejaVu Serif', 'DejaVu Sans'],
    pilih: 0,
    drag: null,
    qrX: d.qrX ?? 78,
    qrY: d.qrY ?? 86,
    qrSize: d.qrSize ?? 12,
    chips: ['{NAMA}', '{nama}', '{SEKOLAH}', '{ASAL}', '{DESA}', '{SANGGAR}', '{KECAMATAN}', '{nomor}', '{tanggal_lahir}', '{tanggal}'],

    get W() { return this.orientasi === 'landscape' ? 841.89 : 595.28; },
    get el() { return this.elemen[this.pilih] || null; },
    klem(v, a, b) { return Math.round(Math.min(b, Math.max(a, v)) * 10) / 10; },

    tambah() {
      this.elemen.push({ teks: 'Teks baru', x: 10, y: 50, w: 80, ukuran: 16, font: 'Times', tebal: false, miring: false, rata: 'center', warna: '#2A1A14' });
      this.pilih = this.elemen.length - 1;
    },
    duplikat() {
      if (!this.el) return;
      const c = JSON.parse(JSON.stringify(this.el));
      c.y = this.klem(c.y + 5, 0, 98);
      this.elemen.push(c);
      this.pilih = this.elemen.length - 1;
    },
    hapus() {
      if (this.elemen.length < 2) return;
      this.elemen.splice(this.pilih, 1);
      this.pilih = Math.max(0, this.pilih - 1);
    },
    sisip(t) { if (this.el) this.el.teks = (this.el.teks || '') + t; },

    mulai(e, i) {
      this.pilih = i;
      const r = this.$refs.kanvas.getBoundingClientRect(), el = this.elemen[i];
      this.drag = { i, dx: (e.clientX - r.left) / r.width * 100 - el.x, dy: (e.clientY - r.top) / r.height * 100 - el.y };
      e.currentTarget.setPointerCapture(e.pointerId);
    },
    geser(e) {
      if (!this.drag) return;
      const r = this.$refs.kanvas.getBoundingClientRect(), el = this.elemen[this.drag.i];
      el.x = this.klem((e.clientX - r.left) / r.width * 100 - this.drag.dx, 0, 100 - el.w);
      el.y = this.klem((e.clientY - r.top) / r.height * 100 - this.drag.dy, 0, 98);
    },
    lepas() { this.drag = null; },

    fontCss(f) {
      return ({ 'Times': '"Times New Roman",Times,serif', 'Helvetica': 'Helvetica,Arial,sans-serif',
        'DejaVu Serif': '"DejaVu Serif",Georgia,serif', 'DejaVu Sans': '"DejaVu Sans",Verdana,sans-serif' })[f] || ('"' + f + '",cursive');
    },
    contoh(t) {
      const peta = {
        nama: 'Nur Laily Syafitri', kecamatan: 'Cluring', sanggar: 'Sanggar Tari Gandrung Arum',
        sekolah: 'SMPN 2 Cluring', asal: 'SMPN 2 Cluring', desa: 'Rejosari',
      };
      let s = String(t == null ? '' : t);
      for (const [k, v] of Object.entries(peta)) {
        s = s.replaceAll('{' + k + '}', v).replaceAll('{' + k.toUpperCase() + '}', v.toUpperCase());
      }
      return s
        .replaceAll('{tanggal_lahir}', '14 Maret 2008')
        .replaceAll('{nomor}', 'GS26/0001/2026')
        .replaceAll('{tanggal}', new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }));
    },
  };
}
</script>

<form method="post" action="{{ route('admin.template.simpan') }}" enctype="multipart/form-data"
      x-data="editorSertifikat(@js($data))" @submit="$refs.json.value = JSON.stringify(elemen)"
      class="grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">@csrf
  <input type="hidden" name="elemen" x-ref="json">

  {{-- Pratinjau --}}
  <div class="box min-w-0 lg:sticky lg:top-24">
    <div :class="orientasi === 'portrait' ? 'mx-auto max-w-md' : ''">
      <div x-ref="kanvas" class="relative w-full select-none overflow-hidden rounded border border-[#F0E2D0] bg-white"
           :style="{ aspectRatio: orientasi === 'landscape' ? '297/210' : '210/297', containerType: 'inline-size' }">
        <img x-show="src" :src="src" alt="" class="pointer-events-none absolute inset-0 h-full w-full object-cover">
        <template x-for="(e, i) in elemen" :key="i">
          <div class="absolute cursor-move touch-none"
               :class="pilih === i ? 'ring-2 ring-[#8B1E2D]' : 'hover:ring-1 hover:ring-[#C9962B]'"
               :style="{ left: e.x + '%', top: e.y + '%', width: e.w + '%', fontSize: (e.ukuran / W * 100) + 'cqw',
                         fontFamily: fontCss(e.font), fontWeight: e.tebal ? 700 : 400, fontStyle: e.miring ? 'italic' : 'normal',
                         textAlign: e.rata, color: e.warna, lineHeight: 1.25, whiteSpace: 'pre-line' }"
               @pointerdown="mulai($event, i)" @pointermove="geser($event)" @pointerup="lepas()" x-text="contoh(e.teks)"></div>
        </template>
        <div class="pointer-events-none absolute flex items-center justify-center rounded border border-dashed border-[#8B1E2D] bg-[#8B1E2D]/5 text-[9px] font-bold text-[#8B1E2D]"
             :style="{ left: qrX + '%', top: qrY + '%', width: qrSize + '%', aspectRatio: '1/1' }">QR</div>
      </div>
    </div>
    <p class="mt-2 text-xs text-[#6B5A50]">Pratinjau memakai data contoh. Klik kotak teks untuk memilih, geser untuk memindahkan.</p>
  </div>

  {{-- Pengaturan --}}
  <div class="min-w-0 space-y-4">
    <div class="box space-y-3">
      <div><label class="lbl" for="gambar">Desain latar (JPG/PNG, maks. 4 MB, tanpa teks)</label>
        <input id="gambar" type="file" name="gambar" accept="image/png,image/jpeg" class="block w-full text-sm"
               @change="if ($event.target.files[0]) src = URL.createObjectURL($event.target.files[0])"></div>
      <div><label class="lbl" for="orientasi">Orientasi kertas A4</label>
        <select id="orientasi" name="orientasi" x-model="orientasi" class="inp">
          <option value="landscape">Landscape (melebar)</option><option value="portrait">Portrait (tegak)</option></select></div>
      <div class="grid grid-cols-3 gap-3">
        <div><label class="lbl" for="qr_x">QR X (%)</label>
          <input id="qr_x" type="number" name="qr_x" min="0" max="100" step="0.5" x-model.number="qrX" class="inp" required></div>
        <div><label class="lbl" for="qr_y">QR Y (%)</label>
          <input id="qr_y" type="number" name="qr_y" min="0" max="100" step="0.5" x-model.number="qrY" class="inp" required></div>
        <div><label class="lbl" for="qr_size">Ukuran QR (%)</label>
          <input id="qr_size" type="number" name="qr_size" min="1" max="100" step="0.5" x-model.number="qrSize" class="inp" required></div>
      </div>
    </div>

    <div class="box space-y-3">
      <div class="flex items-center justify-between gap-2"><h2 class="font-semibold">Kotak teks</h2>
        <button type="button" @click="tambah()" class="btn btn-s">Tambah teks</button></div>
      <div class="flex flex-wrap gap-2">
        <template x-for="(e, i) in elemen" :key="i">
          <button type="button" @click="pilih = i" class="max-w-[10rem] truncate rounded-lg border px-3 py-1.5 text-xs"
                  :class="pilih === i ? 'border-[#8B1E2D] bg-[#8B1E2D] text-white' : 'border-[#E5D5C0] bg-white'"
                  x-text="(e.teks || '(kosong)').split('\n')[0]"></button>
        </template>
      </div>
      <p x-show="!elemen.length" class="text-sm text-[#6B5A50]">Belum ada kotak teks. Klik "Tambah teks" untuk memulai.</p>
    </div>

    <template x-if="el">
      <div class="box space-y-3">
        <div><label class="lbl">Isi teks</label><textarea x-model="el.teks" rows="3" class="inp !h-auto py-2"></textarea>
          <div class="mt-2 flex flex-wrap gap-1.5">
            <template x-for="c in chips" :key="c">
              <button type="button" @click="sisip(c)" class="rounded-md bg-[#8B1E2D]/10 px-2 py-1 text-xs font-semibold text-[#8B1E2D]" x-text="c"></button>
            </template></div>
          <p class="mt-1 text-xs text-[#6B5A50]">Klik penanda untuk menyisipkannya. Isinya diganti otomatis dengan data penari. Huruf besar, misalnya {NAMA}, menghasilkan teks KAPITAL.</p></div>

        <div class="grid grid-cols-2 gap-3">
          <div class="col-span-2"><label class="lbl">Font</label>
            <select x-model="el.font" class="inp"><template x-for="f in fonts" :key="f"><option :value="f" x-text="f"></option></template></select></div>
          <div><label class="lbl">Ukuran (pt)</label><input type="number" min="6" max="150" step="1" x-model.number="el.ukuran" class="inp"></div>
          <div><label class="lbl">Lebar kotak (%)</label><input type="number" min="1" max="100" step="1" x-model.number="el.w" class="inp"></div>
          <div><label class="lbl">Rata teks</label>
            <select x-model="el.rata" class="inp"><option value="left">Kiri</option><option value="center">Tengah</option><option value="right">Kanan</option></select></div>
          <div><label class="lbl">Warna</label><input type="color" x-model="el.warna" class="h-11 w-full rounded-xl border border-[#E5D5C0]"></div>
          <div><label class="lbl">Posisi X (%)</label><input type="number" min="0" max="100" step="0.5" x-model.number="el.x" class="inp"></div>
          <div><label class="lbl">Posisi Y (%)</label><input type="number" min="0" max="100" step="0.5" x-model.number="el.y" class="inp"></div>
        </div>
        <div class="flex gap-5 text-sm">
          <label class="flex items-center gap-2"><input type="checkbox" x-model="el.tebal" class="rounded border-[#CDBFA6] text-[#8B1E2D]"> Tebal</label>
          <label class="flex items-center gap-2"><input type="checkbox" x-model="el.miring" class="rounded border-[#CDBFA6] text-[#8B1E2D]"> Miring</label>
        </div>
        <div class="flex flex-wrap gap-2">
          <button type="button" @click="duplikat()" class="btn-o btn-s">Duplikat</button>
          <button type="button" @click="hapus()" class="btn-o btn-s" :disabled="elemen.length < 2">Hapus kotak ini</button>
        </div>
      </div>
    </template>

    <button class="btn w-full">Simpan template</button>
  </div>
</form>
</x-panel>