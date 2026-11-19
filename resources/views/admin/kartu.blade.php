<!doctype html><html><head><meta charset="utf-8"><style>
@@page{margin:24pt} body{font-family:DejaVu Sans,sans-serif;color:#2A1A14}
td{border:1px dashed #8B1E2D;padding:12pt 6pt;text-align:center;width:33%}
</style></head><body>
@foreach ($kelompok as $judul => $daftar)
<div @if (!$loop->last) style="page-break-after:always" @endif>
  <div style="font-size:11pt;font-weight:bold;color:#5E1220;margin-bottom:6pt">{{ $judul }} ({{ $daftar->count() }} kartu)</div>
  <table width="100%" cellspacing="8" cellpadding="0">
  @foreach ($daftar->chunk(3) as $baris)
    <tr>
      @foreach ($baris as $k)
      <td><div style="font-size:9pt;color:#8B1E2D">Gandrung Sewu 2026</div>
        <div style="font-size:14pt;font-weight:bold;letter-spacing:1pt;margin:5pt 0">{{ $k->kode }}</div>
        <div style="font-size:8pt">{{ $k->asal }}</div>
        <div style="font-size:8pt">Jalur {{ $k->label_jalur }}</div>
        @if ($k->nama_penerima)<div style="font-size:8pt;margin-top:2pt">a.n. {{ $k->nama_penerima }}</div>@endif
        <div style="font-size:7pt;margin-top:4pt">Masukkan kode ini di website saat mendaftar</div></td>
      @endforeach
      @for ($i = $baris->count(); $i < 3; $i++)<td style="border:0"></td>@endfor
    </tr>
  @endforeach
  </table>
</div>
@endforeach
</body></html>