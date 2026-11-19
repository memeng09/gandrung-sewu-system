<!doctype html><html><head><meta charset="utf-8"><style>
@@page{margin:28pt 24pt}
body{font-family:DejaVu Sans,sans-serif;font-size:8pt;color:#2A1A14}
h1{font-size:14pt;margin:0;color:#5E1220}
.ket{margin:2pt 0 10pt;color:#6B5A50;font-size:8pt}
table{width:100%;border-collapse:collapse}
thead{display:table-header-group}
th{background:#8B1E2D;color:#fff;text-align:left;padding:4pt;font-size:7.5pt}
td{border-bottom:.5pt solid #D9C3A3;padding:3.5pt 4pt;vertical-align:top}
tr{page-break-inside:avoid}
</style></head><body>
<h1>Data Penari Gandrung Sewu</h1>
<div class="ket">{{ $filter }} &middot; {{ number_format($penari->count()) }} penari &middot; dicetak {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</div>
<table>
<thead><tr><th>No</th><th>Nama</th><th>Jalur</th><th>Asal</th><th>Sekolah / instansi</th><th>Sanggar</th><th>Kode</th><th>Tgl lahir</th><th>WhatsApp</th><th>Status</th></tr></thead>
<tbody>
@foreach ($penari as $i => $p)
<tr><td>{{ $i + 1 }}</td><td>{{ $p->nama }}</td><td>{{ $p->label_jalur }}</td>
<td>{{ $p->jalur === 'desa' ? 'Desa ' . $p->desa . ', ' : '' }}{{ $p->wilayah }}</td>
<td>{{ $p->sekolah ?: $p->instansi }}{{ $p->kelas ? ' (kelas ' . $p->kelas . ')' : '' }}</td><td>{{ $p->sanggar }}</td>
<td>{{ $p->kode->kode }}</td><td>{{ $p->tanggal_lahir->format('d/m/Y') }}</td><td>{{ $p->no_wa }}</td><td>{{ $status[$p->status] }}</td></tr>
@endforeach
</tbody></table>
</body></html>