<svg {{ $attributes->merge(['class' => 'h-10 w-10']) }} viewBox="-24 -24 48 48" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
  @for ($i = 0; $i < 8; $i++)<ellipse cx="0" cy="-11" rx="4.5" ry="10" transform="rotate({{ $i * 45 }})"/>@endfor
  <circle r="3.5" fill="currentColor"/>
</svg>