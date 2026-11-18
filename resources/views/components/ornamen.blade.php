<svg {{ $attributes }} viewBox="-100 -100 200 200" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" aria-hidden="true">
  @for ($i = 0; $i < 12; $i++)
    <path transform="rotate({{ $i * 30 }})" d="M0 -22 C 17 -44 17 -76 0 -96 C -17 -76 -17 -44 0 -22 Z" fill="currentColor" fill-opacity=".1"/>
  @endfor
  @for ($i = 0; $i < 12; $i++)
    <path transform="rotate({{ $i * 30 + 15 }})" d="M0 -14 C 10 -30 10 -52 0 -68 C -10 -52 -10 -30 0 -14 Z"/>
  @endfor
  @for ($i = 0; $i < 8; $i++)
    <path transform="rotate({{ $i * 45 }})" d="M0 -6 C 6 -16 6 -30 0 -40 C -6 -30 -6 -16 0 -6 Z" fill="currentColor" fill-opacity=".15"/>
  @endfor
  <circle r="48" stroke-dasharray="2 5"/>
  <circle r="5" fill="currentColor"/>
</svg>