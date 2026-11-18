<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SertifikatTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TemplateController extends Controller
{
    public function edit()
    {
        return view('admin.template', ['tpl' => SertifikatTemplate::first() ?? new SertifikatTemplate]);
    }

    public function simpan(Request $r)
    {
        $r->validate([
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'orientasi' => 'required|in:landscape,portrait',
            'elemen' => 'required|json',
            'qr_x' => 'required|numeric|between:0,100',
            'qr_y' => 'required|numeric|between:0,100',
            'qr_size' => 'required|numeric|between:1,100',
        ]);

        $elemen = json_decode($r->elemen, true);
        validator(['elemen' => $elemen], [
            'elemen' => 'required|array|min:1|max:30',
            'elemen.*.teks' => 'nullable|string|max:600',
            'elemen.*.x' => 'required|numeric|between:0,100',
            'elemen.*.y' => 'required|numeric|between:0,100',
            'elemen.*.w' => 'required|numeric|between:1,100',
            'elemen.*.ukuran' => 'required|numeric|between:6,150',
            'elemen.*.font' => ['required', Rule::in(SertifikatTemplate::daftarFont())],
            'elemen.*.tebal' => 'boolean',
            'elemen.*.miring' => 'boolean',
            'elemen.*.rata' => 'required|in:left,center,right',
            'elemen.*.warna' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ])->validate();

        // simpan hanya kolom yang dikenal
        $bersih = collect($elemen)->map(fn ($e) => [
            'teks' => (string) ($e['teks'] ?? ''), 'x' => (float) $e['x'], 'y' => (float) $e['y'], 'w' => (float) $e['w'],
            'ukuran' => (float) $e['ukuran'], 'font' => $e['font'], 'tebal' => (bool) $e['tebal'],
            'miring' => (bool) $e['miring'], 'rata' => $e['rata'], 'warna' => $e['warna'],
        ])->values()->all();

        $tpl = SertifikatTemplate::first() ?? new SertifikatTemplate;
        $tpl->orientasi = $r->orientasi;
        $tpl->elemen = $bersih;
        $tpl->qr_x = (float) $r->qr_x;
        $tpl->qr_y = (float) $r->qr_y;
        $tpl->qr_size = (float) $r->qr_size;
        if ($r->hasFile('gambar')) {
            $tpl->gambar = $r->file('gambar')->store('sertifikat', 'public');
        }
        $tpl->save();

        return back()->with('ok', 'Template sertifikat disimpan.');
    }
}
