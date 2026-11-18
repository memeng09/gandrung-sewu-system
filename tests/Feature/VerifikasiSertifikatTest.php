<?php

namespace Tests\Feature;

use App\Models\Kecamatan;
use App\Models\KodePenari;
use App\Models\Penari;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifikasiSertifikatTest extends TestCase
{
    use RefreshDatabase;

    public function test_verifikasi_dapat_diakses_tanpa_login_menampilkan_data_penari(): void
    {
        $user = User::factory()->create(['role' => 'penari']);
        $kecamatan = Kecamatan::create([
            'nama' => 'Kabat',
            'kode_singkat' => 'KBV',
            'kuota' => 10,
        ]);
        $kode = KodePenari::create([
            'kecamatan_id' => $kecamatan->id,
            'kode' => 'KBV-001',
        ]);
        $penari = Penari::create([
            'user_id' => $user->id,
            'kode_penari_id' => $kode->id,
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Ayu Lestari',
            'tanggal_lahir' => '2008-03-14',
            'alamat' => 'Banyuwangi',
            'foto' => 'foto-penari/contoh.jpg',
            'status' => 'aktif',
        ]);
        $penari->forceFill([
            'kode_verifikasi' => 'GS-ABC12345',
            'created_at' => '2026-05-14 10:00:00',
        ])->save();

        $this->get(route('verifikasi.sertifikat', ['kode' => 'gs-abc12345']))
            ->assertOk()
            ->assertSee('Sertifikat Asli')
            ->assertSee('GS-ABC12345')
            ->assertSee('Ayu Lestari')
            ->assertSee('2026')
            ->assertSee('Festival Gandrung Sewu 2026');
    }

    public function test_verifikasi_kode_tidak_terdaftar_menampilkan_status_gagal(): void
    {
        $this->get(route('verifikasi.sertifikat', ['kode' => 'GS-TIDAKADA']))
            ->assertOk()
            ->assertSee('Sertifikat Tidak Ditemukan')
            ->assertSee('Kode sertifikat tidak terdaftar.')
            ->assertDontSee('Sertifikat Asli');
    }
}
