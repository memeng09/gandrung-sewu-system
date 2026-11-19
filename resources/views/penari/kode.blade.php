<x-panel title="Pendaftaran Penari" sub="Langkah 1 dari 2: masukkan kode dari kartu yang diberikan juri.">
  <form method="post" action="{{ route('penari.kode') }}" class="box max-w-xl space-y-4">@csrf
    <div>
      <label class="lbl" for="kode">Kode unik</label>
      <input id="kode" name="kode" value="{{ old('kode') }}" class="inp font-mono uppercase" placeholder="CLR-D-XXXX-XXXX" autocomplete="off" autofocus required>
      <p class="mt-2 text-sm text-[#6B5A50]">Jalur dan asalmu dikenali otomatis dari kode. Setelah diperiksa, kode langsung terkunci ke akunmu, jadi tidak perlu dimasukkan lagi walaupun kamu keluar dulu.</p>
    </div>
    <button class="btn">Periksa kode</button>
  </form>
</x-panel>