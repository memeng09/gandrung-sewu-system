<?php
namespace App\Http\Controllers\Penari;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Penari;
use Illuminate\Http\Request;

class CardController extends Controller {
    // Halaman publik: hanya nama, foto, jalur, asal, sanggar. Alamat, tanggal lahir, WA tidak ditampilkan.
    public function index(Request $r) {
        $q = Penari::with('kecamatan:id,nama')->where('status', 'aktif')->orderBy('nama');
        if ($r->filled('q')) $q->where('nama', 'like', '%' . $r->q . '%');
        if (in_array($r->jalur, ['sekolah', 'desa', 'umum', 'luar'])) $q->where('jalur', $r->jalur);
        if ($r->filled('kecamatan')) $q->where('kecamatan_id', $r->kecamatan);

        return view('penari.cards', [
            'penari' => $q->paginate(12)->withQueryString(),
            'kecamatan' => Kecamatan::orderBy('nama')->get(['id', 'nama']),
        ]);
    }
}