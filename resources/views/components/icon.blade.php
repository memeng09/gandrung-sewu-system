@props(['n'])
<svg {{ $attributes->merge(['class' => 'h-5 w-5 shrink-0']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($n)
  @case('home')<path d="M3 11l9-8 9 8M5 10v10h5v-6h4v6h5V10"/>@break
  @case('users')<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.5 3-5.5 6.5-5.5s6.5 2 6.5 5.5M16 4.5a3.5 3.5 0 010 7M18 14.8c2 .6 3.5 2.2 3.5 5.2"/>@break
  @case('key')<circle cx="8" cy="15" r="4"/><path d="M11 12l9-9M16 7l3 3"/>@break
  @case('file')<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h3"/>@break
  @case('edit')<path d="M4 20h4L19 9l-4-4L4 16v4z"/>@break
  @case('grid')<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>@break
  @case('user')<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>@break
  @case('out')<path d="M9 21H5V3h4M16 17l5-5-5-5M21 12H9"/>@break
  @case('check')<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>@break
  @case('cal')<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>@break
  @case('pin')<path d="M12 21s7-6 7-11a7 7 0 10-14 0c0 5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>@break
@endswitch
</svg>