<!doctype html><html><head><meta charset="utf-8"><style>
@page{margin:0} body{margin:0;font-family:DejaVu Serif,serif}
.t{position:absolute;left:0;width:841pt;text-align:center}
</style></head><body>
<img src="{{ $bg }}" style="position:absolute;left:0;top:0;width:841pt;height:595pt">
<div class="t" style="top:{{ $tpl->nama_y * 5.95 }}pt;font-size:{{ $tpl->nama_size }}pt;color:{{ $tpl->warna }}">{{ $penari->nama }}</div>
<div class="t" style="top:{{ $tpl->info_y * 5.95 }}pt;font-size:14pt">Kecamatan {{ $penari->kecamatan->nama }}</div>
<div class="t" style="top:{{ $tpl->nomor_y * 5.95 }}pt;font-size:11pt">No. {{ $penari->nomor_sertifikat }}</div>
</body></html>