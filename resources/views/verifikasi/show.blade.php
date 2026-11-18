<x-publik title="Verifikasi Sertifikat">
<main class="mx-auto flex min-h-[70vh] w-full max-w-xl items-center px-4 py-10">
  <section class="box w-full p-6 text-center sm:p-8">
    @if ($penari)
      <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 ring-8 ring-emerald-50">
        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 13 4 4L19 7"/></svg>
      </div>
      <p class="mt-5 text-xs font-bold uppercase tracking-[0.2em] text-emerald-700">Terverifikasi</p>
      <h1 class="judul mt-1 text-2xl font-bold text-emerald-800 sm:text-3xl">Sertifikat Asli</h1>
      <p class="mx-auto mt-2 max-w-sm text-sm text-[#6B5A50]">Sertifikat ini terdaftar dalam data resmi Festival Gandrung Sewu {{ $tahun }}.</p>

      <dl class="mt-6 grid gap-3 text-left sm:grid-cols-2">
        <div class="rounded-xl border border-[#F0E2D0] bg-[#FFFAF4] p-4">
          <dt class="text-xs font-semibold uppercase tracking-wide text-[#6B5A50]">Kode Sertifikat</dt>
          <dd class="mt-1 break-all font-mono text-sm font-bold text-[#8B1E2D]">{{ $penari->kode_verifikasi }}</dd>
        </div>
        <div class="rounded-xl border border-[#F0E2D0] bg-[#FFFAF4] p-4">
          <dt class="text-xs font-semibold uppercase tracking-wide text-[#6B5A50]">Nama Penari</dt>
          <dd class="mt-1 text-sm font-bold text-[#2A1A14]">{{ $penari->nama }}</dd>
        </div>
        <div class="rounded-xl border border-[#F0E2D0] bg-[#FFFAF4] p-4">
          <dt class="text-xs font-semibold uppercase tracking-wide text-[#6B5A50]">Tahun</dt>
          <dd class="mt-1 text-sm font-bold text-[#2A1A14]">{{ $penari->created_at?->year ?? $tahun }}</dd>
        </div>
        <div class="rounded-xl border border-[#F0E2D0] bg-[#FFFAF4] p-4">
          <dt class="text-xs font-semibold uppercase tracking-wide text-[#6B5A50]">Kecamatan</dt>
          <dd class="mt-1 text-sm font-bold text-[#2A1A14]">{{ $penari->kecamatan?->nama ?? '-' }}</dd>
        </div>
        <div class="rounded-xl border border-[#F0E2D0] bg-[#FFFAF4] p-4 sm:col-span-2">
          <dt class="text-xs font-semibold uppercase tracking-wide text-[#6B5A50]">Keterangan Event</dt>
          <dd class="mt-1 text-sm font-bold text-[#2A1A14]">Festival Gandrung Sewu {{ $tahun }} — Banyuwangi</dd>
        </div>
      </dl>
    @else
      <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-700 ring-8 ring-red-50">
        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </div>
      <p class="mt-5 text-xs font-bold uppercase tracking-[0.2em] text-red-700">Tidak Valid</p>
      <h1 class="judul mt-1 text-2xl font-bold text-red-800 sm:text-3xl">Sertifikat Tidak Ditemukan</h1>
      <p class="mx-auto mt-2 max-w-sm text-sm text-[#6B5A50]">Kode sertifikat tidak terdaftar. Pastikan kode yang Anda pindai sudah benar atau hubungi panitia.</p>
      <p class="mt-4 break-all font-mono text-xs text-[#8B1E2D]">Kode yang diperiksa: {{ $kode }}</p>
    @endif

    <a href="{{ route('beranda') }}" class="btn-o mt-7 inline-flex">Kembali ke Beranda</a>
  </section>
</main>
</x-publik>
