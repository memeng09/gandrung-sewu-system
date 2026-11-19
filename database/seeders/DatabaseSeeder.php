<?php
namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder {
    public function run(): void {
        $data = ['Pesanggaran'=>'PSG','Siliragung'=>'SLG','Bangorejo'=>'BGR','Purwoharjo'=>'PWH','Tegaldlimo'=>'TGD',
            'Muncar'=>'MCR','Cluring'=>'CLR','Gambiran'=>'GBR','Tegalsari'=>'TGS','Glenmore'=>'GLM','Kalibaru'=>'KLB',
            'Genteng'=>'GTG','Srono'=>'SRN','Rogojampi'=>'RGJ','Kabat'=>'KBT','Singojuruh'=>'SGJ','Sempu'=>'SMP',
            'Songgon'=>'SGG','Glagah'=>'GLG','Licin'=>'LCN','Banyuwangi'=>'BWI','Giri'=>'GIR','Kalipuro'=>'KLP',
            'Wongsorejo'=>'WSR','Blimbingsari'=>'BLB'];
        foreach ($data as $nama => $kode) {
            Kecamatan::firstOrCreate(['nama' => $nama], ['kode_singkat' => $kode]);
        }
    }
}