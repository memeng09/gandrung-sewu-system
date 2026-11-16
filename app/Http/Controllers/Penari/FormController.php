<?php
namespace App\Http\Controllers\Penari;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\KodePenari;
use App\Models\Penari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormController extends Controller {
    public function create() {
        if (auth()->user()->penari) return redirect()->route('penari.dashboard');
        return view('penari.form', ['kecamatan' => Kecamatan::orderBy('nama')->get()]);
    }

    public function store(Request $r) {
        abort_if(auth()->user()->penari, 403);
        $d = $r->validate([
            'kode' => 'required|string|max:30', 'kecamatan_id' => 'required|exists:kecamatan,id',
            'nama' => 'required|string|max:100', 'tanggal_lahir' => 'required|date|before:today',
            'alamat' => 'required|string|max:300', 'no_wa' => 'nullable|string|max:20',
            'sanggar' => 'nullable|string|max:100', 'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($d, $r) {
            // lockForUpdate: dua orang tidak bisa memakai kode yang sama di detik yang sama
            $kode = KodePenari::with('kecamatan')->where('kode', strtoupper(trim($d['kode'])))->lockForUpdate()->first();
            if (!$kode || $kode->status !== 'belum_dipakai') {
                throw ValidationException::withMessages(['kode' => 'Kode tidak valid atau sudah digunakan.']);
            }

            $status = 'aktif'; $catatan = null;
            if ($kode->kecamatan_id != $d['kecamatan_id']) {
                $status = 'perlu_ditinjau';
                $catatan = "Kecamatan di form beda dengan kode ({$kode->kecamatan->nama})";
            }

            Penari::create([
                'user_id' => auth()->id(), 'kode_penari_id' => $kode->id, 'kecamatan_id' => $d['kecamatan_id'],
                'nama' => $d['nama'], 'tanggal_lahir' => $d['tanggal_lahir'], 'alamat' => $d['alamat'],
                'no_wa' => $d['no_wa'] ?? null, 'sanggar' => $d['sanggar'] ?? null,
                'foto' => $r->file('foto')->store('foto-penari', 'public'),
                'status' => $status, 'catatan' => $catatan,
            ]);
            $kode->update(['status' => 'dipakai', 'dipakai_oleh' => auth()->id(), 'dipakai_pada' => now()]);
        });

        return redirect()->route('penari.dashboard')->with('ok', 'Data terkirim. Kode sah, kamu terdaftar sebagai penari.');
    }
}