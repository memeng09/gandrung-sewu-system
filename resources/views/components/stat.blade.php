@props(['label', 'nilai', 'ikon' => 'check', 'info' => null, 'warna' => 'maroon', 'href' => null])
@php
  $tema = [
    'maroon' => 'bg-[#8B1E2D]/10 text-[#8B1E2D]',
    'emas'   => 'bg-[#C9962B]/15 text-[#7A5410]',
    'hijau'  => 'bg-green-100 text-green-800',
    'amber'  => 'bg-amber-100 text-amber-800',
  ][$warna] ?? 'bg-[#8B1E2D]/10 text-[#8B1E2D]';
  $tag = $href ? 'a' : 'div';
@endphp
<{{ $tag }} @if ($href) href="{{ $href }}" @endif class="box flex flex-col gap-3 {{ $href ? 'transition hover:border-[#C9962B]' : '' }}">
  <div class="flex items-center justify-between gap-3">
    <span class="text-sm font-medium text-[#6B5A50]">{{ $label }}</span>
    <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $tema }}"><x-icon :n="$ikon" class="h-5 w-5"/></span>
  </div>
  <div class="judul text-3xl font-bold tracking-tight">{{ $nilai }}</div>
  @if ($info)<div class="text-xs text-[#6B5A50]">{{ $info }}</div>@endif
  {{ $slot }}
</{{ $tag }}>