# Kode Verifikasi Sertifikat Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menambahkan kolom kode verifikasi unik dan pengaturan QR pada database, lalu membuat kode `GS-XXXXXXXX` secara otomatis dan permanen saat sertifikat pertama kali diunduh.

**Architecture:** Dua migration menambah kolom nullable/unique di `penari` dan kolom pengaturan QR di `sertifikat_templates`. Service `KodeVerifikasiGenerator` menghasilkan kode acak; `Penari::pastikanKodeVerifikasi()` menyimpannya dengan atomic conditional update + retry; `SertifikatController::download()` memanggilnya sebelum render PDF.

**Tech Stack:** Laravel 13, PHP ^8.3, SQLite in-memory (phpunit.xml), PHPUnit 12, Laravel Pint, DomPDF, Laravel Boost.

## Global Constraints

- `composer.json` mensyaratkan PHP `^8.3`; PHP CLI yang terdeteksi awal adalah 8.2.12. Semua perintah `composer`/`artisan`/`pint` di bawah harus memakai `php.exe` 8.3 yang ditemukan di Task 1.
- Laravel Boost harus dipasang (`composer require laravel/boost --dev`, lalu `php artisan boost:install`) dan `AGENTS.md` dibaca ulang sebelum perubahan aplikasi, sesuai `AGENTS.md` repo.
- Jangan commit kecuali pengguna meminta eksplisit.
- Format kode harus tepat `GS-` + 8 karakter dari alfabet `ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789` (11 karakter total).
- Kode tidak boleh berasal dari ID penari dan tidak boleh berubah setelah dibuat.
- QR belum dirender ke PDF; tidak ada backfill massal untuk penari lama.
- Ikuti gaya kode yang ada: class migration memakai `return new class extends Migration { ... }`, method model satu baris bila pendek, dan penamaan variabel Bahasa Indonesia.

---

## File Structure

- `database/migrations/2026_11_18_000001_add_kode_verifikasi_to_penari_table.php` (baru) — kolom `kode_verifikasi`.
- `database/migrations/2026_11_18_000002_add_qr_settings_to_sertifikat_templates_table.php` (baru) — kolom `qr_x`, `qr_y`, `qr_size`.
- `app/Models/SertifikatTemplate.php` (ubah) — tambah 3 kolom ke `$fillable`.
- `app/Services/KodeVerifikasiGenerator.php` (baru) — generator kode acak.
- `app/Models/Penari.php` (ubah) — method `pastikanKodeVerifikasi()`.
- `app/Http/Controllers/Penari/SertifikatController.php` (ubah) — panggil method sebelum render PDF.
- `tests/Feature/SertifikatVerifikasiTest.php` (baru) — seluruh pengujian fitur ini.
- `docs/superpowers/specs/2026-11-18-kode-verifikasi-sertifikat-design.md` — spec yang sudah disetujui (sumber keputusan).

---

### Task 1: Lingkungan dan migration

**Files:**
- Modify: `AGENTS.md` (dibaca ulang setelah Boost, bukan diedit manual oleh agen)
- Create: `database/migrations/2026_11_18_000001_add_kode_verifikasi_to_penari_table.php`
- Create: `database/migrations/2026_11_18_000002_add_qr_settings_to_sertifikat_templates_table.php`
- Modify: `app/Models/SertifikatTemplate.php:7`
- Test: `tests/Feature/SertifikatVerifikasiTest.php`

**Interfaces:**
- Consumes: struktur tabel `penari` dan `sertifikat_templates` dari migration yang sudah ada.
- Produces: kolom `penari.kode_verifikasi` (string 12, nullable, unique), `sertifikat_templates.qr_x/qr_y/qr_size` (decimal 5,1, default 78/86/12), serta `$fillable` model template yang sudah memuat ketiga kolom QR.

- [ ] **Step 1: Temukan PHP 8.3 dan pasang Laravel Boost**

Jalankan di PowerShell dari root proyek:

```powershell
Get-ChildItem -LiteralPath "C:\laragon\bin\php" -Directory -ErrorAction SilentlyContinue | Select-Object -ExpandProperty Name
Get-ChildItem -LiteralPath "C:\xampp2" -Directory -ErrorAction SilentlyContinue | Select-Object -ExpandProperty Name
```

Pakai `php.exe` 8.3 yang ditemukan (misalnya `C:\laragon\bin\php\php-8.3.x\php.exe`) sebagai `$PHP`. Jika tidak ada PHP 8.3, jalankan installer Windows dari `AGENTS.md`, lalu minta pengguna membuka terminal baru dan mengulang prompt sebelum melanjutkan.

```powershell
$PHP = "PATH_PHP_8_3_DITEMUKAN\php.exe"
$env:PATH = (Split-Path -Parent $PHP) + ";" + $env:PATH
& $PHP -v
& $PHP -r "echo PHP_VERSION;"
composer require laravel/boost --dev
& $PHP artisan boost:install
```

Setelah itu baca kembali `C:\laragon\www\gandrung-sewu-system - Backuptest\AGENTS.md` dan ikuti panduan Boost yang dihasilkan.

- [ ] **Step 2: Tulis tes skema yang gagal**

Buat `tests/Feature/SertifikatVerifikasiTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Kecamatan;
use App\Models\KodePenari;
use App\Models\Penari;
use App\Models\SertifikatTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SertifikatVerifikasiTest extends TestCase
{
    use RefreshDatabase;

    private static int $urutan = 0;

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

    private function penari(array $atribut = []): Penari
    {
        $user = User::factory()->create(['role' => 'penari']);
        $kecamatan = Kecamatan::create([
            'nama' => 'Kabat ' . (++self::$urutan),
            'kode_singkat' => 'KB' . self::$urutan,
            'kuota' => 10,
        ]);
        $kode = KodePenari::create([
            'kecamatan_id' => $kecamatan->id,
            'kode' => 'KB-' . self::$urutan,
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
```

- [ ] **Step 3: Jalankan tes untuk memastikan gagal**

```powershell
& $PHP artisan test --filter=SertifikatVerifikasiTest
```

Expected: FAIL — `kode_verifikasi` belum ada di tabel `penari`.

- [ ] **Step 4: Buat migration `kode_verifikasi`**

Buat `database/migrations/2026_11_18_000001_add_kode_verifikasi_to_penari_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('penari', function (Blueprint $t) {
            $t->string('kode_verifikasi', 12)->nullable()->unique()->after('status');
        });
    }

    public function down(): void {
        Schema::table('penari', fn (Blueprint $t) => $t->dropColumn('kode_verifikasi'));
    }
};
```

- [ ] **Step 5: Buat migration pengaturan QR**

Buat `database/migrations/2026_11_18_000002_add_qr_settings_to_sertifikat_templates_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sertifikat_templates', function (Blueprint $t) {
            $t->decimal('qr_x', 5, 1)->default(78)->after('elemen');
            $t->decimal('qr_y', 5, 1)->default(86)->after('qr_x');
            $t->decimal('qr_size', 5, 1)->default(12)->after('qr_y');
        });
    }

    public function down(): void {
        Schema::table('sertifikat_templates', fn (Blueprint $t) => $t->dropColumn(['qr_x', 'qr_y', 'qr_size']));
    }
};
```

- [ ] **Step 6: Tambah kolom QR ke `$fillable` model template**

Ubah baris 7 `app/Models/SertifikatTemplate.php` menjadi:

```php
    protected $fillable = ['gambar', 'orientasi', 'elemen', 'nama_y', 'nama_size', 'info_y', 'nomor_y', 'warna', 'qr_x', 'qr_y', 'qr_size'];
```

- [ ] **Step 7: Jalankan tes sampai lolos**

```powershell
& $PHP artisan test --filter=SertifikatVerifikasiTest
```

Expected: PASS (1 test).

---

### Task 2: Generator kode dan method model

**Files:**
- Create: `app/Services/KodeVerifikasiGenerator.php`
- Modify: `app/Models/Penari.php`
- Test: `tests/Feature/SertifikatVerifikasiTest.php` (tambah 4 tes)

**Interfaces:**
- Consumes: kolom `penari.kode_verifikasi` dari Task 1.
- Produces: `App\Services\KodeVerifikasiGenerator::generate(): string` dan `App\Models\Penari::pastikanKodeVerifikasi(): string`. Task 3 memanggil method model ini.

- [ ] **Step 1: Tulis tes generator, idempotensi, keunikan, data lama, dan bentrok**

Tambahkan import ke `tests/Feature/SertifikatVerifikasiTest.php`:

```php
use App\Services\KodeVerifikasiGenerator;
```

Tambahkan method-method ini di dalam class `SertifikatVerifikasiTest`:

```php
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
        $this->penari(['kode_verifikasi' => 'GS-AAAAAAAA']);

        $this->app->instance(KodeVerifikasiGenerator::class, new class extends KodeVerifikasiGenerator {
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
```

- [ ] **Step 2: Jalankan tes untuk memastikan gagal**

```powershell
& $PHP artisan test --filter=SertifikatVerifikasiTest
```

Expected: FAIL — class `KodeVerifikasiGenerator` dan method `pastikanKodeVerifikasi()` belum ada.

- [ ] **Step 3: Buat service generator**

Buat `app/Services/KodeVerifikasiGenerator.php`:

```php
<?php

namespace App\Services;

class KodeVerifikasiGenerator
{
    private const ALFABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    public function generate(): string
    {
        $kode = '';

        for ($i = 0; $i < 8; $i++) {
            $kode .= self::ALFABET[random_int(0, strlen(self::ALFABET) - 1)];
        }

        return 'GS-' . $kode;
    }
}
```

- [ ] **Step 4: Tambah method ke model `Penari`**

Tambahkan import di bagian atas `app/Models/Penari.php` setelah `use Illuminate\Database\Eloquent\Model;`:

```php
use App\Services\KodeVerifikasiGenerator;
use Illuminate\Database\QueryException;
```

Tambahkan method ini sebelum method `isiTeks()`:

```php
    public function pastikanKodeVerifikasi(): string
    {
        if ($this->kode_verifikasi) {
            return $this->kode_verifikasi;
        }

        $generator = app(KodeVerifikasiGenerator::class);

        for ($percobaan = 0; $percobaan < 5; $percobaan++) {
            $kode = $generator->generate();

            try {
                $terubah = static::whereKey($this->id)
                    ->whereNull('kode_verifikasi')
                    ->update(['kode_verifikasi' => $kode]);
            } catch (QueryException $e) {
                $tersimpan = $this->fresh()?->kode_verifikasi;
                if ($tersimpan) {
                    return $this->kode_verifikasi = $tersimpan;
                }
                continue;
            }

            if ($terubah === 1) {
                return $this->kode_verifikasi = $kode;
            }

            $tersimpan = $this->fresh()?->kode_verifikasi;
            if ($tersimpan) {
                return $this->kode_verifikasi = $tersimpan;
            }
        }

        throw new \RuntimeException('Gagal membuat kode verifikasi unik.');
    }
```

- [ ] **Step 5: Jalankan tes sampai lolos**

```powershell
& $PHP artisan test --filter=SertifikatVerifikasiTest
```

Expected: PASS (6 tests).

---

### Task 3: Integrasi ke alur unduh sertifikat

**Files:**
- Modify: `app/Http/Controllers/Penari/SertifikatController.php:12`
- Test: `tests/Feature/SertifikatVerifikasiTest.php` (tambah 1 tes + `tearDown`)

**Interfaces:**
- Consumes: `Penari::pastikanKodeVerifikasi(): string` dari Task 2.
- Produces: route `penari.sertifikat` yang menyimpan kode sebelum `Pdf::loadView(...)`.

- [ ] **Step 1: Tulis tes integrasi unduh**

Tambahkan import ke `tests/Feature/SertifikatVerifikasiTest.php`:

```php
use Illuminate\Support\Facades\Storage;
```

Tambahkan method ini di dalam class:

```php
    protected function tearDown(): void
    {
        Storage::disk('public')->delete('sertifikat/contoh.png');
        parent::tearDown();
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
```

- [ ] **Step 2: Jalankan tes untuk memastikan gagal**

```powershell
& $PHP artisan test --filter=test_unduh_sertifikat_membuat_kode_secara_otomatis
```

Expected: FAIL — `kode_verifikasi` masih `null` setelah unduh.

- [ ] **Step 3: Panggil generator di controller**

Pada `app/Http/Controllers/Penari/SertifikatController.php`, sisipkan tepat setelah baris `abort_unless($penari && $penari->status === 'aktif', 403);`:

```php
        $penari->pastikanKodeVerifikasi();
```

Jangan ubah bagian `Pdf::loadView(...)`, ukuran kertas, atau nama file.

- [ ] **Step 4: Jalankan tes sampai lolos**

```powershell
& $PHP artisan test --filter=SertifikatVerifikasiTest
```

Expected: PASS (7 tests).

---

### Task 4: Verifikasi akhir

**Files:** tidak ada perubahan file; verifikasi saja.

- [ ] **Step 1: Jalankan seluruh tes**

```powershell
& $PHP artisan test
```

Expected: PASS (termasuk tests bawaan Breeze).

- [ ] **Step 2: Jalankan Pint dalam mode cek**

```powershell
& $PHP vendor/bin/pint --test
```

Expected: tidak ada pelanggaran format pada file yang diubah. Jika ada, jalankan `& $PHP vendor/bin/pint app/Models/Penari.php app/Http/Controllers/Penari/SertifikatController.php app/Services/KodeVerifikasiGenerator.php database/migrations tests/Feature/SertifikatVerifikasiTest.php` lalu ulangi mode cek.

- [ ] **Step 3: Periksa perubahan kerja**

```powershell
git status --short
git diff --stat
```

Expected: hanya file yang tercantum di File Structure yang muncul. Jangan commit.

- [ ] **Step 4: Lakukan self-review plan terhadap spec**

Pastikan semua poin spec terpenuhi: kolom nullable+unique, 3 kolom QR, format `GS-` + 8 alfanumerik, atomic retry, integrasi unduh, kompatibilitas data lama, dan tidak ada render QR/backfill.

---

## Self-Review Results

- **Spec coverage:** migration + fillable (Task 1), generator + retry + tes (Task 2), integrasi unduh (Task 3), verifikasi + kompatibilitas (Task 4). Spec "di luar lingkup" tidak dilanggar.
- **Placeholder scan:** tidak ada TBD/TODO; seluruh kode tes dan implementasi dituliskan lengkap. `$PHP = "PATH_PHP_8_3_DITEMUKAN\php.exe"` adalah instruksi konkret hasil penemuan di Task 1 Step 1, bukan placeholder implementasi.
- **Type consistency:** `generate(): string`, `pastikanKodeVerifikasi(): string`, `qr_x/qr_y/qr_size` decimal, `kode_verifikasi` string(12) konsisten di semua task.
- **Commit policy:** langkah commit sengaja tidak dimasukkan karena pengguna belum meminta commit secara eksplisit.
