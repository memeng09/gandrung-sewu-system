<x-publik title="Penari Lolos">
<div class="mx-auto max-w-6xl space-y-5 px-4 py-8">
  <div><h1 class="judul text-2xl font-bold sm:text-3xl">Penari yang Lolos</h1>
    <p class="mt-1 text-sm text-[#6B5A50]">Daftar penari yang terpilih dalam seleksi Gandrung Sewu.</p></div>
  <form method="get" class="flex flex-col gap-2 sm:flex-row">
    <input class="inp sm:max-w-xs" name="q" value="{{ request('q') }}" placeholder="Cari nama penari">
    <select class="inp sm:max-w-[220px]" name="kecamatan" onchange="this.form.submit()"><option value="">Semua kecamatan</option>
      @foreach ($kecamatan as $k)<option value="{{ $k->id }}" @selected(request('kecamatan') == $k->id)>{{ $k->nama }}</option>@endforeach</select>
    <button class="btn-o">Cari</button>
  </form>
  <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
    @forelse ($penari as $p)
      <div class="overflow-hidden rounded-2xl border border-[#F0E2D0] bg-[#FFFAF4]">
        <img src="{{ asset('storage/' . $p->foto) }}" alt="Foto {{ $p->nama }}" loading="lazy" class="aspect-[3/4] w-full object-cover">
        <div class="p-3"><div class="font-semibold leading-tight">{{ $p->nama }}</div><div class="text-sm text-[#6B5A50]">{{ $p->kecamatan->nama }}</div></div>
      </div>
    @empty <p class="col-span-full text-[#6B5A50]">Belum ada penari yang tampil.</p>
    @endforelse
  </div>
  {{ $penari->links() }}
</div>
</x-publik>