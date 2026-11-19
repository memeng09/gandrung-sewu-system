@php $tabs = ['sekolah' => 'Sekolah', 'desa' => 'Desa', 'umum' => 'Umum']; @endphp
<div class="flex flex-wrap gap-2">
  @foreach ($tabs as $j => $l)
    <a href="{{ route('admin.kuota', ['jalur' => $j]) }}" class="{{ ($aktif ?? '') === $j ? 'btn' : 'btn-o' }} btn-s">{{ $l }}</a>
  @endforeach
  <a href="{{ route('admin.kuota.luar') }}" class="{{ ($aktif ?? '') === 'luar' ? 'btn' : 'btn-o' }} btn-s">Luar Banyuwangi</a>
</div>