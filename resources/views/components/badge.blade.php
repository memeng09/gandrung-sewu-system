@props(['status'])
@php
$map = [
  'aktif'          => ['Terverifikasi', 'bg-green-50 text-green-800 ring-green-200', 'bg-green-500'],
  'perlu_ditinjau' => ['Perlu ditinjau', 'bg-amber-50 text-amber-800 ring-amber-200', 'bg-amber-500'],
  'dicabut'        => ['Dicabut', 'bg-red-50 text-red-800 ring-red-200', 'bg-red-500'],
];
[$label, $cls, $dot] = $map[$status] ?? [$status, 'bg-gray-50 text-gray-800 ring-gray-200', 'bg-gray-400'];
@endphp
<span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $cls }}"><span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>{{ $label }}</span>