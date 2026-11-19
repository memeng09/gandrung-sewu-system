<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\KodePenari;
use App\Models\Penari;
use App\Services\FotoPenari;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PenariController extends Controller {
    private const STATUS = ['aktif' => 'Terverifikasi', 'perlu_ditinjau' => 'Perlu ditinjau', 'dicabut' => 'Dicabut'];

    // Filter dipakai bersama oleh daftar, Excel, dan PDF
    private function query(Request $r) {
        $q = Penari::with('kecamatan', 'kode');
        if ($r->filled('status')) $q->where('status', $r->status);
        if ($r->filled('jalur')) $q->where('jalur', $r->jalur);
        if ($r->filled('kecamatan')) $q->where('kecamatan_id', $r->kecamatan);
        if ($t = $r->q) {
            $q->where(fn ($w) => $w->where('nama', 'like', "%$t%")->orWhere('desa', 'like', "%$t%")->orWhere('sekolah', 'like', "%$t%")
                ->orWhereHas('kode', fn ($x) => $x->where('kode', 'like', "%$t%")));
        }
        return $q;
    }

    private function urut(Request $r) {
        return $this->query($r)->get()->sortBy([['jalur', 'asc'], ['wilayah', 'asc'], ['nama', 'asc']])->values();
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
            'penari'   => $penari->load('kecamatan', 'kode'),
            'provinsi' => Penari::PROVINSI,
        ]);
    }

    // Jalur dan kecamatan tidak bisa diubah: keduanya ditentukan oleh kode
    public function update(Request $r, Penari $penari) {
        $d = $r->validate(array_merge([
            'nama' => 'required|string|max:100', 'tanggal_lahir' => 'required|date|before:today',
            'alamat' => 'required|string|max:300', 'no_wa' => 'nullable|string|max:20', 'sanggar' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ], Penari::aturanJalur($penari->jalur)), [
            'desa.regex' => 'Nama desa hanya boleh berisi huruf, tanpa angka atau simbol.',
        ]);

        if ($r->hasFile('foto')) {
            Storage::disk('public')->delete($penari->foto);
            $d['foto'] = FotoPenari::simpan($r->file('foto'));
        } else {
            unset($d['foto']);
        }
        $penari->update($d);
        return redirect()->route('admin.penari')->with('ok', "Data {$penari->nama} diperbarui.");
    }

    // Menyetujui penari yang dicabut = memulihkan kodenya juga
    public function setujui(Penari $penari) {
        DB::transaction(function () use ($penari) {
            if ($penari->status === 'dicabut') $penari->kode->update(['status' => 'dipakai']);
            $penari->update(['status' => 'aktif', 'catatan' => null]);
        });
        return back()->with('ok', "{$penari->nama} disetujui.");
    }

    // Mencabut penari = kodenya ikut dicabut, sehingga slot kuota terbuka untuk kode pengganti
    public function cabut(Penari $penari) {
        DB::transaction(function () use ($penari) {
            $penari->update(['status' => 'dicabut']);
            $penari->kode->update(['status' => 'dicabut']);
        });
        return back()->with('ok', "{$penari->nama} dicabut. Kodenya ikut dicabut; buat kode pengganti bila perlu.");
    }

    // Password sementara 8 karakter, tampil sekali di layar
    public function resetPassword(Penari $penari) {
        $huruf = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $sandi = '';
        for ($i = 0; $i < 8; $i++) $sandi .= $huruf[random_int(0, strlen($huruf) - 1)];

        $user = $penari->user;
        $user->password = $sandi;
        $user->setRememberToken(Str::random(60));
        $user->save();

        return back()->with('sandi_baru', $sandi);
    }

    // ---------- Ekspor Excel (CSV, langsung terbuka di Excel) ----------
    public function excel(Request $r) {
        $rows = $this->urut($r);
        $aman = fn ($v) => (is_string($v) && preg_match('/^[=+\-@]/', $v)) ? "'" . $v : $v;

        return response()->streamDownload(function () use ($rows, $aman) {
            $f = fopen('php://output', 'w');
            fwrite($f, "\xEF\xBB\xBF");
            fputcsv($f, ['No', 'Nama', 'Jalur', 'Kecamatan / Kota', 'Desa', 'Sekolah / Instansi', 'Kelas', 'Sanggar', 'Kode',
                'Tanggal Lahir', 'Alamat', 'No. WhatsApp', 'Status', 'Terdaftar'], ';', '"', '');
            foreach ($rows as $i => $p) {
                fputcsv($f, [
                    $i + 1, $aman($p->nama), $p->label_jalur, $p->wilayah, $aman($p->desa ?? ''), $aman($p->sekolah ?: ($p->instansi ?? '')),
                    $p->kelas ?? '', $aman($p->sanggar ?? ''), $p->kode->kode, $p->tanggal_lahir->format('d/m/Y'), $aman($p->alamat),
                    $p->no_wa ? '="' . preg_replace('/\D/', '', $p->no_wa) . '"' : '', self::STATUS[$p->status], $p->created_at->format('d/m/Y H:i'),
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
            $r->filled('jalur') ? 'Jalur ' . (KodePenari::JALUR[$r->jalur]['label'] ?? $r->jalur) : null,
            $r->filled('kecamatan') ? 'Kecamatan ' . optional(Kecamatan::find($r->kecamatan))->nama : null,
            $r->filled('status') ? (self::STATUS[$r->status] ?? null) : null,
            $r->filled('q') ? 'Pencarian "' . $r->q . '"' : null,
        ])->filter()->implode(', ') ?: 'Semua data';

        return Pdf::loadView('admin.export-pdf', ['penari' => $this->urut($r), 'filter' => $filter, 'status' => self::STATUS])
            ->setPaper('a4', 'landscape')->download('data-penari-' . now()->format('Ymd-His') . '.pdf');
    }
}