<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SertifikatTemplate;
use Illuminate\Http\Request;

class TemplateController extends Controller {
    public function edit() {
        return view('admin.template', ['tpl' => SertifikatTemplate::first() ?? new SertifikatTemplate([
            'nama_y' => 48, 'nama_size' => 42, 'info_y' => 62, 'nomor_y' => 88, 'warna' => '#5E1220',
        ])]);
    }

    public function simpan(Request $r) {
        $d = $r->validate([
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'nama_y' => 'required|numeric|between:0,100', 'nama_size' => 'required|integer|between:12,120',
            'info_y' => 'required|numeric|between:0,100', 'nomor_y' => 'required|numeric|between:0,100',
            'warna' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);
        if ($r->hasFile('gambar')) $d['gambar'] = $r->file('gambar')->store('sertifikat', 'public');
        else unset($d['gambar']);

        $tpl = SertifikatTemplate::first() ?? new SertifikatTemplate();
        $tpl->fill($d)->save();
        return back()->with('ok', 'Template sertifikat disimpan.');
    }
}