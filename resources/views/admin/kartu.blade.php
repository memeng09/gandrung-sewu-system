<!doctype html><html><head><meta charset="utf-8"><style>
@page{margin:24pt} body{font-family:DejaVu Sans,sans-serif;color:#2A1A14}
td{border:1px dashed #8B1E2D;padding:14pt 6pt;text-align:center;width:33%}
</style></head><body>
<table width="100%" cellspacing="8" cellpadding="0">
@foreach ($kodes->chunk(3) as $baris)
<tr>
  @foreach ($baris as $k)
  <td><div style="font-size:9pt;color:#8B1E2D">Gandrung Sewu 2026</div>
    <div style="font-size:15pt;font-weight:bold;letter-spacing:1pt;margin:6pt 0">{{ $k->kode }}</div>
    <div style="font-size:8pt">Kecamatan {{ $kecamatan->nama }}</div>
    <div style="font-size:7pt;margin-top:4pt">Masukkan kode ini saat mengisi data diri di website</div></td>
  @endforeach
  @for ($i = $baris->count(); $i < 3; $i++)<td style="border:0"></td>@endfor
</tr>
@endforeach
</table></body></html>