<!doctype html><html><head><meta charset="utf-8"><style>
@@page{margin:0}
@foreach ($fontCustom as $f)
@@font-face{font-family:'{{ $f }}';font-style:normal;font-weight:normal;src:url('{{ str_replace('\\', '/', public_path('fonts/sertifikat/' . $f . '.ttf')) }}') format('truetype');}
@endforeach
body{margin:0}
.e{position:absolute;line-height:1.25}
</style></head><body>
<img src="{{ $bg }}" style="position:absolute;left:0;top:0;width:{{ $W }}pt;height:{{ $H }}pt">
<img src="{{ $qrDataUri }}" style="position:absolute;left:{{ $tpl->qr_x * $W / 100 }}pt;top:{{ $tpl->qr_y * $H / 100 }}pt;width:{{ $tpl->qr_size * $W / 100 }}pt;height:{{ $tpl->qr_size * $W / 100 }}pt">
@foreach ($tpl->elemenAktif() as $e)
<div class="e" style="left:{{ $e['x'] * $W / 100 }}pt;top:{{ $e['y'] * $H / 100 }}pt;width:{{ $e['w'] * $W / 100 }}pt;text-align:{{ $e['rata'] }};font-family:'{{ $e['font'] }}';font-size:{{ $e['ukuran'] }}pt;color:{{ $e['warna'] }};font-weight:{{ !empty($e['tebal']) ? 'bold' : 'normal' }};font-style:{{ !empty($e['miring']) ? 'italic' : 'normal' }}">{!! nl2br(e($penari->isiTeks($e['teks'] ?? ''))) !!}</div>
@endforeach
</body></html>