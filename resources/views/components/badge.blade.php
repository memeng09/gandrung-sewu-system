@props(['status'])
@php
$map = ['aktif' => ['Terverifikasi', 'bg-green-100 text-green-900'], 'perlu_ditinjau' => ['Perlu ditinjau', 'bg-amber-100 text-amber-900'], 'dicabut' => ['Dicabut', 'bg-red-100 text-red-900']];
[$label, $cls] = $map[$status] ?? [$status, 'bg-gray-100 text-gray-900'];
@endphp
<span class="inline-block whitespace-nowrap rounded-md px-3 py-1 text-xs font-semibold {{ $cls }}">{{ $label }}</span>