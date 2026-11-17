@auth
  <x-panel title="Penari yang Lolos" sub="Daftar penari yang terpilih dalam seleksi Gandrung Sewu.">
    @include('penari._daftar')
  </x-panel>
@else
  <x-publik title="Penari Lolos">
    <div class="mx-auto max-w-6xl space-y-5 px-4 py-8">
      <div>
        <h1 class="judul text-2xl font-bold sm:text-3xl">Penari yang Lolos</h1>
        <p class="mt-1 text-sm text-[#6B5A50]">Daftar penari yang terpilih dalam seleksi Gandrung Sewu.</p>
      </div>
      @include('penari._daftar')
    </div>
  </x-publik>
@endauth