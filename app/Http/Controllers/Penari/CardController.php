<?php
namespace App\Http\Controllers\Penari;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Penari;
use Illuminate\Http\Request;

class CardController extends Controller {
    // Halaman publik: hanya nama, foto, kecamatan (data pribadi tidak ditampilkan)
    public function index(Request $r) {
        $q = Penari::with('kecamatan:id,nama')->where('status', 'aktif')->orderBy('nama');
        if ($r->filled('q')) $q->where('nama', 'like', '%' . $r->q . '%');
        if ($r->filled('kecamatan')) $q->where('kecamatan_id', $r->kecamatan);
        return view('penari.cards', [
            'penari' => $q->paginate(12, ['id', 'nama', 'foto', 'kecamatan_id'])->withQueryString(),
            'kecamatan' => Kecamatan::orderBy('nama')->get(['id', 'nama']),
        ]);
    }
}