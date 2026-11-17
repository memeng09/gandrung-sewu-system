<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Penari;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenariController extends Controller {
    private const STATUS = ['aktif' => 'Terverifikasi', 'perlu_ditinjau' => 'Perlu ditinjau', 'dicabut' => 'Dicabut'];

    // Filter dipakai bersama oleh daftar, Excel, dan PDF
    private function query(Request $r) {
        $q = Penari::with('kecamatan', 'kode');
        if ($r->filled('status')) $q->where('status', $r->status);
        if ($r->filled('kecamatan')) $q->where('kecamatan_id', $r->kecamatan);
        if ($t = $r->q) {
            $q->where(fn ($w) => $w->where('nama', 'like', "%$t%")
                ->orWhereHas('kode', fn ($x) => $x->where('kode', 'like', "%$t%")));
        }
        return $q;
    }

    private function urut(Request $r) {
        return $this->query($r)->get()->sortBy([['kecamatan.nama', 'asc'], ['nama', 'asc']])->values();
    }

    public function index(Request $r) {
        return view('admin.penari', [
            'penari'    => $this->query($r)->latest()->paginate(15)->withQueryString(),
            'hitung'    => Penari::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
            'kecamatan' => Kecamatan::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function edit(Penari $penari) {
        return view('admin.penari-edit', [
            'penari'    => $penari->load('kecamatan', 'kode'),
            'kecamatan' => Kecamatan::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $r, Penari $penari) {
        $d = $r->validate([
            'nama' => 'required|string|max:100', 'tanggal_lahir' => 'required|date|before:today',
            'alamat' => 'required|string|max:300', 'no_wa' => 'nullable|string|max:20',
            'sanggar' => 'nullable|string|max:100', 'kecamatan_id' => 'required|exists:kecamatan,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        if ($r->hasFile('foto')) {
            Storage::disk('public')->delete($penari->foto);
            $d['foto'] = $r->file('foto')->store('foto-penari', 'public');
        } else {
            unset($d['foto']);
        }
        $penari->update($d);
        return redirect()->route('admin.penari')->with('ok', "Data {$penari->nama} diperbarui.");
    }

    public function setujui(Penari $penari) {
        $penari->update(['status' => 'aktif', 'catatan' => null]);
        return back()->with('ok', "{$penari->nama} disetujui.");
    }

    public function cabut(Penari $penari) {
        $penari->update(['status' => 'dicabut']);
        return back()->with('ok', "{$penari->nama} dicabut.");
    }

    // ---------- Ekspor Excel (CSV, langsung terbuka di Excel) ----------
    public function excel(Request $r) {
        $rows = $this->urut($r);
        $aman = fn ($v) => (is_string($v) && preg_match('/^[=+\-@]/', $v)) ? "'" . $v : $v; // cegah rumus berbahaya

        return response()->streamDownload(function () use ($rows, $aman) {
            $f = fopen('php://output', 'w');
            fwrite($f, "\xEF\xBB\xBF"); // BOM supaya huruf dan karakter khusus benar di Excel
            fputcsv($f, ['No', 'Nama', 'Kecamatan', 'Kode', 'Tanggal Lahir', 'Alamat', 'No. WhatsApp', 'Sanggar', 'Status', 'Terdaftar'], ';', '"', '');
            foreach ($rows as $i => $p) {
                fputcsv($f, [
                    $i + 1, $aman($p->nama), $p->kecamatan->nama, $p->kode->kode, $p->tanggal_lahir->format('d/m/Y'),
                    $aman($p->alamat), $p->no_wa ? '="' . preg_replace('/\D/', '', $p->no_wa) . '"' : '', // jaga angka 0 di depan
                    $aman($p->sanggar ?? ''), self::STATUS[$p->status], $p->created_at->format('d/m/Y H:i'),
                ], ';', '"', '');
            }
            fclose($f);
        }, 'data-penari-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ---------- Ekspor PDF ----------
    public function pdf(Request $r) {
        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $filter = collect([
            $r->filled('kecamatan') ? 'Kecamatan ' . optional(Kecamatan::find($r->kecamatan))->nama : null,
            $r->filled('status') ? self::STATUS[$r->status] ?? null : null,
            $r->filled('q') ? 'Pencarian "' . $r->q . '"' : null,
        ])->filter()->implode(', ') ?: 'Semua data';

        return Pdf::loadView('admin.export-pdf', ['penari' => $this->urut($r), 'filter' => $filter, 'status' => self::STATUS])
            ->setPaper('a4', 'landscape')
            ->download('data-penari-' . now()->format('Ymd-His') . '.pdf');
    }
}