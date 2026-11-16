<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penari;
use Illuminate\Http\Request;

class PenariController extends Controller {
    public function index(Request $r) {
        $q = Penari::with('kecamatan', 'kode')->latest();
        if ($r->filled('status')) $q->where('status', $r->status);
        if ($t = $r->q) {
            $q->where(fn ($w) => $w->where('nama', 'like', "%$t%")
                ->orWhereHas('kode', fn ($x) => $x->where('kode', 'like', "%$t%")));
        }
        return view('admin.penari', [
            'penari' => $q->paginate(15)->withQueryString(),
            'hitung' => Penari::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
        ]);
    }

    public function setujui(Penari $penari) {
        $penari->update(['status' => 'aktif', 'catatan' => null]);
        return back()->with('ok', "{$penari->nama} disetujui.");
    }

    public function cabut(Penari $penari) {
        $penari->update(['status' => 'dicabut']);
        return back()->with('ok', "{$penari->nama} dicabut.");
    }
}