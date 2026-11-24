<?php
namespace App\Http\Controllers\Penari;

use App\Http\Controllers\Controller;
use App\Models\KodePenari;
use App\Models\Penari;
use App\Services\FotoPenari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormController extends Controller {
    // Kode yang sudah terkunci ke akun ini tetapi data dirinya belum dikirim
    private function kodeAkun(): ?KodePenari {
        return KodePenari::with('kecamatan', 'kelompok')
            ->where('dipakai_oleh', auth()->id())->where('status', 'dipakai')->first();
    }

    // Langkah 1 (masukkan kode) atau langkah 2 (isi data), tergantung akun sudah punya kode atau belum
    public function create() {
        if (auth()->user()->penari) return redirect()->route('penari.dashboard');

        $kode = $this->kodeAkun();
        if (!$kode) return view('penari.kode');

        return view('penari.form', ['kode' => $kode, 'provinsi' => Penari::PROVINSI]);
    }

    // Langkah 1: periksa kode, lalu langsung kunci ke akun ini
    public function cekKode(Request $r) {
        abort_if(auth()->user()->penari, 403);
        if ($this->kodeAkun()) return redirect()->route('penari.form');
        $r->validate(['kode' => 'required|string|max:30']);

        DB::transaction(function () use ($r) {
            // lockForUpdate: dua orang tidak bisa mengambil kode yang sama di detik yang sama
            $kode = KodePenari::where('kode', strtoupper(trim($r->kode)))->lockForUpdate()->first();
            if (!$kode || $kode->status !== 'belum_dipakai') {
                throw ValidationException::withMessages(['kode' => 'Kode tidak valid atau sudah digunakan.']);
            }
            $kode->update(['status' => 'dipakai', 'dipakai_oleh' => auth()->id(), 'dipakai_pada' => now()]);
        });

        return redirect()->route('penari.form');
    }

    // Langkah 2: simpan data diri
    public function store(Request $r) {
        abort_if(auth()->user()->penari, 403);
        $kodeAkun = $this->kodeAkun();
        if (!$kodeAkun) return redirect()->route('penari.form')->withErrors(['kode' => 'Masukkan kode terlebih dahulu.']);

               $d = $r->validate(array_merge([
            'nama' => 'required|string|max:100', 'tanggal_lahir' => 'required|date|before:today',
            'alamat' => 'required|string|max:300', 'no_wa' => 'required|string|min:9|max:20',
            'sanggar' => 'nullable|string|max:100',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:10240',
        ], Penari::aturanJalur($kodeAkun->jalur)), [
            'desa.regex' => 'Nama desa hanya boleh berisi huruf, tanpa angka atau simbol.',
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        DB::transaction(function () use ($d, $r, $kodeAkun) {
            $kode = KodePenari::lockForUpdate()->find($kodeAkun->id);
            if (!$kode || $kode->status !== 'dipakai' || $kode->dipakai_oleh != auth()->id()) {
                throw ValidationException::withMessages(['kode' => 'Kode tidak lagi berlaku untuk akunmu. Hubungi panitia.']);
            }

            // Kode terkunci ke nama tertentu? Nama beda = masuk antrean tinjau admin
            $status = 'aktif'; $catatan = null;
            if ($kode->nama_penerima && $this->norm($kode->nama_penerima) !== $this->norm($d['nama'])) {
                $status = 'perlu_ditinjau';
                $catatan = 'Nama di form beda dengan nama penerima kode (' . $kode->nama_penerima . ')';
            }

            Penari::create(array_merge($d, [
                'user_id' => auth()->id(), 'kode_penari_id' => $kode->id, 'jalur' => $kode->jalur,
                'kecamatan_id' => $kode->kecamatan_id, 'foto' => FotoPenari::simpan($r->file('foto')),
                'status' => $status, 'catatan' => $catatan,
            ]));
        });

        return redirect()->route('penari.dashboard')->with('ok', 'Data terkirim. Kode sah, kamu terdaftar sebagai penari.');
    }

    private function norm(string $s): string {
        return trim(preg_replace('/[^a-z0-9]+/', ' ', mb_strtolower($s)));
    }
}