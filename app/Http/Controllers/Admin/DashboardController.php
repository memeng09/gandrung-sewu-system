<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\KodePenari;
use App\Models\KuotaJalur;
use App\Models\Penari;

class DashboardController extends Controller {
    public function index() {
        $kuota = KuotaJalur::all()->keyBy(fn ($k) => $k->kecamatan_id . '|' . $k->jalur);
        $kode = KodePenari::where('status', '!=', 'dicabut')->whereNotNull('kecamatan_id')
            ->selectRaw("kecamatan_id, jalur, COUNT(*) AS dibuat, SUM(CASE WHEN status = 'dipakai' THEN 1 ELSE 0 END) AS dipakai")
            ->groupBy('kecamatan_id', 'jalur')->get()->keyBy(fn ($k) => $k->kecamatan_id . '|' . $k->jalur);
        $luar = KodePenari::where('jalur', 'luar')->where('status', '!=', 'dicabut')
            ->selectRaw("COUNT(*) AS dibuat, SUM(CASE WHEN status = 'dipakai' THEN 1 ELSE 0 END) AS dipakai")->first();

        $stat = [
            'target'   => Kecamatan::TARGET,
            'dibuat'   => KodePenari::where('status', '!=', 'dicabut')->count(),
            'terpakai' => KodePenari::where('status', 'dipakai')->count(),
            'aktif'    => Penari::where('status', 'aktif')->count(),
            'ditinjau' => Penari::where('status', 'perlu_ditinjau')->count(),
        ];

        $harian = collect(range(6, 0))->map(function ($i) {
            $d = now()->subDays($i);
            return ['tgl' => $d->format('d/m'), 'n' => Penari::whereDate('created_at', $d->toDateString())->count()];
        });

        return view('admin.dashboard', [
            'kec' => Kecamatan::orderBy('nama')->get(['id', 'nama', 'kode_singkat']),
            'kuota' => $kuota, 'kode' => $kode, 'luar' => $luar, 'stat' => $stat, 'harian' => $harian,
            'terbaru' => Penari::with('kecamatan')->latest()->take(5)->get(),
        ]);
    }
}