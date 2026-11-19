<form method="get" class="flex flex-col gap-2 lg:flex-row">
  <input class="inp lg:max-w-xs" name="q" value="{{ request('q') }}" placeholder="Cari nama penari">
  <select class="inp lg:max-w-[190px]" name="jalur" onchange="this.form.submit()">
    <option value="">Semua jalur</option>
    @foreach (collect(\App\Models\KodePenari::JALUR) as $j => $v)<option value="{{ $j }}" @selected(request('jalur') === $j)>{{ $v['label'] }}</option>@endforeach
  </select>
  <select class="inp lg:max-w-[210px]" name="kecamatan" onchange="this.form.submit()">
    <option value="">Semua kecamatan</option>
    @foreach ($kecamatan as $k)<option value="{{ $k->id }}" @selected(request('kecamatan') == $k->id)>{{ $k->nama }}</option>@endforeach
  </select>
  <button class="btn-o">Cari</button>
  @if (request()->hasAny(['q', 'jalur', 'kecamatan']))<a href="{{ route('penari.cards') }}" class="inline-flex h-11 items-center px-3 text-sm font-semibold text-[#8B1E2D]">Reset</a>@endif
</form>

<p class="text-sm text-[#6B5A50]">{{ number_format($penari->total()) }} penari ditampilkan</p>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
  @forelse ($penari as $p)
    <article class="overflow-hidden rounded-2xl border border-[#F0E2D0] bg-[#FFFAF4] shadow-sm shadow-[#8B1E2D]/5">
      <img src="{{ asset('storage/' . $p->foto) }}" alt="Foto {{ $p->nama }}" loading="lazy" class="aspect-[4/5] w-full bg-[#F0E2D0] object-cover object-top">
      <div class="space-y-3 p-4">
        <h3 class="font-bold leading-snug">{{ $p->nama }}</h3>
        <dl class="grid grid-cols-[64px_1fr] gap-x-3 gap-y-1.5 text-sm">
          <dt class="text-[#6B5A50]">Jalur</dt><dd class="font-semibold">{{ $p->label_jalur }}</dd>
          <dt class="text-[#6B5A50]">Asal</dt><dd class="font-semibold">{{ $p->asal }}</dd>
          <dt class="text-[#6B5A50]">Sanggar</dt><dd class="font-semibold">{{ $p->sanggar ?: '-' }}</dd>
        </dl>
        <span class="inline-block rounded-lg bg-[#8B1E2D]/10 px-3 py-1.5 text-xs font-semibold text-[#8B1E2D]">Penari Terverifikasi</span>
      </div>
    </article>
  @empty
    <div class="box col-span-full text-center">
      <p class="font-semibold">Belum ada penari yang tampil</p>
      <p class="mt-1 text-sm text-[#6B5A50]">Penari muncul di sini setelah mendaftar dengan kode yang sah.</p>
    </div>
  @endforelse
</div>

{{ $penari->links() }}