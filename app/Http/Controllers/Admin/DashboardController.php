<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Penari;

class DashboardController extends Controller {
    public function index() {
        $kec = Kecamatan::withCount([
            'kodes as kode_dibuat' => fn ($q) => $q->where('status', '!=', 'dicabut'),
            'kodes as kode_dipakai' => fn ($q) => $q->where('status', 'dipakai'),
        ])->orderBy('nama')->get();

        $stat = [
            'target'   => Kecamatan::TARGET,
            'kuota'    => $kec->sum('kuota'),
            'dibuat'   => $kec->sum('kode_dibuat'),
            'terpakai' => $kec->sum('kode_dipakai'),
            'aktif'    => Penari::where('status', 'aktif')->count(),
            'ditinjau' => Penari::where('status', 'perlu_ditinjau')->count(),
        ];

        // pendaftaran per hari, 7 hari terakhir
        $harian = collect(range(6, 0))->map(function ($i) {
            $d = now()->subDays($i);
            return ['tgl' => $d->format('d/m'), 'n' => Penari::whereDate('created_at', $d->toDateString())->count()];
        });

        $terbaru = Penari::with('kecamatan')->latest()->take(5)->get();

        return view('admin.dashboard', compact('kec', 'stat', 'harian', 'terbaru'));
    }
}