<?php

namespace Tests\Feature;

use App\Models\Kecamatan;
use App\Models\KodePenari;
use App\Models\Penari;
use App\Models\SertifikatTemplate;
use App\Models\User;
use App\Services\KodeVerifikasiGenerator;
use App\Services\QrCodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SertifikatVerifikasiTest extends TestCase
{
    use RefreshDatabase;

    private static int $urutan = 0;

    protected function tearDown(): void
    {
        Storage::disk('public')->delete('sertifikat/contoh.png');
        parent::tearDown();
    }

    public function test_kolom_verifikasi_dan_qr_tersedia(): void
    {
        $this->assertTrue(Schema::hasColumn('penari', 'kode_verifikasi'));

        foreach (['qr_x', 'qr_y', 'qr_size'] as $kolom) {
            $this->assertTrue(Schema::hasColumn('sertifikat_templates', $kolom), "Kolom {$kolom} tidak ada");
        }

        $tpl = SertifikatTemplate::create(['orientasi' => 'landscape'])->fresh();
        $this->assertEquals(78.0, (float) $tpl->qr_x);
        $this->assertEquals(86.0, (float) $tpl->qr_y);
        $this->assertEquals(12.0, (float) $tpl->qr_size);
    }

    public function test_generator_menghasilkan_format_gs_8_alfanumerik_kapital(): void
    {
        $generator = app(KodeVerifikasiGenerator::class);

        for ($i = 0; $i < 20; $i++) {
            $this->assertMatchesRegularExpression('/^GS-[A-Z0-9]{8}$/', $generator->generate());
        }
    }

    public function test_kode_tersimpan_dan_tidak_berubah_saat_dipanggil_ulang(): void
    {
        $penari = $this->penari();

        $kode = $penari->pastikanKodeVerifikasi();

        $this->assertMatchesRegularExpression('/^GS-[A-Z0-9]{8}$/', $kode);
        $this->assertSame($kode, $penari->fresh()->kode_verifikasi);
        $this->assertSame($kode, $penari->pastikanKodeVerifikasi());
    }

    public function test_kode_unik_antar_penari(): void
    {
        $a = $this->penari()->pastikanKodeVerifikasi();
        $b = $this->penari()->pastikanKodeVerifikasi();

        $this->assertNotSame($a, $b);
    }

    public function test_penari_lama_dengan_kode_null_tidak_error(): void
    {
        $penari = $this->penari(['kode_verifikasi' => null]);

        $this->assertNull($penari->kode_verifikasi);
        $this->assertMatchesRegularExpression('/^GS-[A-Z0-9]{8}$/', $penari->pastikanKodeVerifikasi());
    }

    public function test_retry_saat_kode_generated_bentrok_dengan_penari_lain(): void
    {
        $this->penari()->forceFill(['kode_verifikasi' => 'GS-AAAAAAAA'])->save();

        $this->app->instance(KodeVerifikasiGenerator::class, new class extends KodeVerifikasiGenerator
        {
            public int $panggilan = 0;

            public function generate(): string
            {
                return ++$this->panggilan === 1 ? 'GS-AAAAAAAA' : 'GS-BBBBBBBB';
            }
        });

        $penari = $this->penari();

        $this->assertSame('GS-BBBBBBBB', $penari->pastikanKodeVerifikasi());
        $this->assertSame('GS-BBBBBBBB', $penari->fresh()->kode_verifikasi);
    }

    public function test_kode_verifikasi_tidak_bisa_diisi_melalui_mass_assignment(): void
    {
        $penari = $this->penari(['kode_verifikasi' => 'GS-INJEKSI']);

        $this->assertNull($penari->kode_verifikasi);
        $this->assertNull($penari->fresh()->kode_verifikasi);
    }

    public function test_gagal_database_non_unik_tidak_dianggap_bentrok(): void
    {
        $penari = $this->penari();

        DB::statement('DROP TABLE penari');
        DB::statement('CREATE TABLE penari (id INTEGER PRIMARY KEY, nama TEXT)');
        DB::statement('INSERT INTO penari (id, nama) VALUES (?, ?)', [$penari->id, 'Ayu Lestari']);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/no such column: kode_verifikasi/i');

        $penari->pastikanKodeVerifikasi();
    }

    public function test_unduh_sertifikat_membuat_kode_secara_otomatis(): void
    {
        $user = User::factory()->create(['role' => 'penari']);
        $kecamatan = Kecamatan::create([
            'nama' => 'Kabat Unduh',
            'kode_singkat' => 'KBU',
            'kuota' => 10,
        ]);
        $kode = KodePenari::create([
            'kecamatan_id' => $kecamatan->id,
            'kode' => 'KBU-001',
        ]);
        Penari::create([
            'user_id' => $user->id,
            'kode_penari_id' => $kode->id,
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Ayu Lestari',
            'tanggal_lahir' => '2008-03-14',
            'alamat' => 'Banyuwangi',
            'foto' => 'foto-penari/contoh.jpg',
            'status' => 'aktif',
        ]);

        Storage::disk('public')->put(
            'sertifikat/contoh.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Zl8kAAAAASUVORK5CYII=')
        );
        SertifikatTemplate::create([
            'gambar' => 'sertifikat/contoh.png',
            'orientasi' => 'landscape',
            'elemen' => SertifikatTemplate::elemenDefault(),
        ]);

        $this->actingAs($user)->get(route('penari.sertifikat'))->assertOk();

        $this->assertMatchesRegularExpression(
            '/^GS-[A-Z0-9]{8}$/',
            $user->fresh()->penari->kode_verifikasi
        );
    }

    public function test_halaman_admin_menampilkan_form_pengaturan_qr(): void
    {
        SertifikatTemplate::create([
            'orientasi' => 'landscape',
            'qr_x' => 12.5,
            'qr_y' => 20,
            'qr_size' => 9,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.template'))
            ->assertOk()
            ->assertSee('name="qr_x"', false)
            ->assertSee('name="qr_y"', false)
            ->assertSee('name="qr_size"', false)
            ->assertSee('value="12.5"', false)
            ->assertSee('value="20"', false)
            ->assertSee('value="9"', false);
    }

    public function test_admin_dapat_menyimpan_pengaturan_qr(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.template.simpan'), [
            'orientasi' => 'landscape',
            'elemen' => json_encode(SertifikatTemplate::elemenDefault()),
            'qr_x' => '12.5',
            'qr_y' => '20',
            'qr_size' => '9',
        ])->assertRedirect()->assertSessionHas('ok', 'Template sertifikat disimpan.');

        $tpl = SertifikatTemplate::first();
        $this->assertSame(12.5, (float) $tpl->qr_x);
        $this->assertSame(20.0, (float) $tpl->qr_y);
        $this->assertSame(9.0, (float) $tpl->qr_size);
    }

    public function test_admin_menolak_koordinat_qr_di_luar_rentang(): void
    {
        SertifikatTemplate::create([
            'orientasi' => 'landscape',
            'qr_x' => 10,
            'qr_y' => 20,
            'qr_size' => 8,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.template.simpan'), [
            'orientasi' => 'landscape',
            'elemen' => json_encode(SertifikatTemplate::elemenDefault()),
            'qr_x' => '150',
            'qr_y' => '20',
            'qr_size' => '8',
        ])->assertSessionHasErrors('qr_x');

        $this->assertSame(10.0, (float) SertifikatTemplate::first()->qr_x);
    }

    public function test_generator_qr_menghasilkan_data_uri_png(): void
    {
        $uri = app(QrCodeGenerator::class)->dataUri('https://gandrungsewu.test/verifikasi/GS-ABCDEFGH');

        $this->assertStringStartsWith('data:image/png;base64,', $uri);
        $png = base64_decode(substr($uri, strlen('data:image/png;base64,')));
        $this->assertSame("\x89PNG\r\n\x1a\n", substr($png, 0, 8));
    }

    public function test_pdf_menempatkan_qr_sesuai_pengaturan_admin(): void
    {
        $penari = $this->penari();
        $tpl = new SertifikatTemplate(['qr_x' => 10, 'qr_y' => 20, 'qr_size' => 5]);

        $this->view('sertifikat.pdf', [
            'penari' => $penari,
            'tpl' => $tpl,
            'bg' => 'latar.png',
            'W' => 1000,
            'H' => 1000,
            'fontCustom' => [],
            'qrDataUri' => 'data:image/png;base64,AAAA',
        ])
            ->assertSee('data:image/png;base64,AAAA', false)
            ->assertSee('left:100pt;top:200pt;width:50pt;height:50pt', false);
    }

    public function test_unduh_mengirim_url_verifikasi_ke_generator_qr(): void
    {
        $user = User::factory()->create(['role' => 'penari']);
        $kecamatan = Kecamatan::create([
            'nama' => 'Kabat QR',
            'kode_singkat' => 'KQR',
            'kuota' => 10,
        ]);
        $kode = KodePenari::create([
            'kecamatan_id' => $kecamatan->id,
            'kode' => 'KQR-001',
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
        $penari->forceFill(['kode_verifikasi' => 'GS-QR123456'])->save();
        Storage::disk('public')->put(
            'sertifikat/contoh.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Zl8kAAAAASUVORK5CYII=')
        );
        SertifikatTemplate::create([
            'gambar' => 'sertifikat/contoh.png',
            'orientasi' => 'landscape',
            'elemen' => SertifikatTemplate::elemenDefault(),
        ]);

        $dataUri = app(QrCodeGenerator::class)->dataUri('seed');
        $qr = $this->mock(QrCodeGenerator::class);
        $qr->shouldReceive('dataUri')
            ->once()
            ->with(url('/verifikasi/GS-QR123456'))
            ->andReturn($dataUri);

        $this->actingAs($user)->get(route('penari.sertifikat'))->assertOk();
    }

    private function penari(array $atribut = []): Penari
    {
        $user = User::factory()->create(['role' => 'penari']);
        $kecamatan = Kecamatan::create([
            'nama' => 'Kabat '.(++self::$urutan),
            'kode_singkat' => 'KB'.self::$urutan,
            'kuota' => 10,
        ]);
        $kode = KodePenari::create([
            'kecamatan_id' => $kecamatan->id,
            'kode' => 'KB-'.self::$urutan,
        ]);

        return Penari::create(array_merge([
            'user_id' => $user->id,
            'kode_penari_id' => $kode->id,
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Ayu Lestari',
            'tanggal_lahir' => '2008-03-14',
            'alamat' => 'Banyuwangi',
            'foto' => 'foto-penari/contoh.jpg',
            'status' => 'aktif',
        ], $atribut));
    }
}
