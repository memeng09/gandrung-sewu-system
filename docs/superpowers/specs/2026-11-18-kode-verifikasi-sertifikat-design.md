# Spesifikasi: Kode Verifikasi Sertifikat Gandrung Sewu

Tanggal: 2026-11-18

## Tujuan

Menyiapkan kolom database untuk kode verifikasi unik sertifikat dan pengaturan posisi/ukuran QR, serta membuat kode unik secara otomatis dan permanen untuk setiap penari saat sertifikat pertama kali diunduh.

## Keputusan yang sudah disetujui

- `kode_verifikasi` disimpan sebagai kolom baru di tabel `penari` (bukan tabel sertifikat baru).
- Format kode: `GS-` + 8 karakter alfanumerik huruf kapital/angka, acak, **tidak** turunan ID penari.
- Waktu pembuatan: otomatis saat unduh pertama (`penari.sertifikat`), idempoten untuk unduh berikutnya.
- QR **belum** dirender ke PDF; hanya kolom pengaturannya yang disiapkan.
- Pembuatan kode memakai *atomic conditional update* + retry.

## Perubahan database

1. Migration baru `add_kode_verifikasi_to_penari_table`:
   - `kode_verifikasi` → `string(12)`, `nullable`, `unique`, setelah kolom `status`.
   - Nullable agar baris lama tetap valid; MySQL/SQLite mengizinkan beberapa `NULL` pada index unique.
2. Migration baru `add_qr_settings_to_sertifikat_templates_table`:
   - `qr_x` → `decimal(5,1)`, default `78`.
   - `qr_y` → `decimal(5,1)`, default `86`.
   - `qr_size` → `decimal(5,1)`, default `12`.
   - Semua dalam persen halaman, mengikuti konvensi `elemen.x`, `elemen.y`, `elemen.w` pada template yang ada.
3. `App\Models\SertifikatTemplate::$fillable` ditambah `qr_x`, `qr_y`, `qr_size`.
4. Tidak ada backfill massal; penari lama tetap `NULL` sampai unduh pertama.

## Perubahan kode

1. `App\Services\KodeVerifikasiGenerator` (baru):
   - `generate(): string` → `GS-` + 8 karakter dari alfabet `ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789` menggunakan `random_int()`.
   - Tidak bergantung pada data penari atau ID.
2. `App\Models\Penari`:
   - `pastikanKodeVerifikasi(): string`:
     - Jika `kode_verifikasi` sudah ada, kembalikan nilainya (tanpa update).
     - Maksimal 5 percobaan: minta kode dari generator, lalu
       `static::whereKey($this->id)->whereNull('kode_verifikasi')->update(['kode_verifikasi' => $kode])`.
     - Jika 1 baris terubah, simpan ke atribut model dan kembalikan kode.
     - Jika 0 baris terubah (request lain menang), muat ulang dan kembalikan kode yang tersimpan.
     - Jika `QueryException` unique, muat ulang; jika tetap kosong, lanjut percobaan berikutnya.
     - Setelah 5 percobaan gagal, lempar `RuntimeException`.
3. `App\Http\Controllers\Penari\SertifikatController::download()`:
   - Setelah validasi penari aktif + template ada, panggil `$penari->pastikanKodeVerifikasi()`.
   - Alur `Pdf::loadView(...)`, ukuran A4, dan nama file tidak diubah.
   - Template Blade `sertifikat.pdf` **tidak** diubah pada spesifikasi ini.

## Kompatibilitas data lama

- Baris `penari` dengan `kode_verifikasi = NULL` tidak error; kode dibuat saat unduh pertama.
- Baris `sertifikat_templates` lama mendapat nilai default QR dari migration.
- `elemen = NULL` tetap difallback ke `SertifikatTemplate::elemenDefault()`.
- `nomor_sertifikat` (turunan ID + tahun) tidak diubah dan tetap berbeda dari `kode_verifikasi`.

## Pengujian

- `tests/Feature/SertifikatVerifikasiTest.php` dengan `RefreshDatabase` (SQLite in-memory sesuai `phpunit.xml`):
  - Skema: kolom `kode_verifikasi`, `qr_x`, `qr_y`, `qr_size` ada.
  - Format: `^GS-[A-Z0-9]{8}$`.
  - Persistensi: nilai tersimpan di database dan dikembalikan lagi pada pemanggilan kedua (idempoten).
  - Keunikan: dua penari mendapat kode berbeda.
  - Data lama: penari dengan `kode_verifikasi = NULL` berhasil diproses.
  - Bentrok: fake generator mengembalikan kode yang sudah dipakai, lalu kode baru; retry harus berhasil.
- Verifikasi sebelum selesai: jalankan tes (`php artisan test` atau `composer test`), lalu `vendor/bin/pint --test` jika tersedia.

## Catatan lingkungan

- `composer.json` membutuhkan PHP `^8.3`, sementara PHP CLI yang terdeteksi awal adalah 8.2.12. Sebelum implementasi, cek PHP 8.3 pada Laragon/XAMPP atau sesuaikan interpreter agar `composer`/`artisan` bisa dijalankan.
- Sesuai `AGENTS.md`, Laravel Boost harus dipasang (`composer require laravel/boost --dev`, lalu `php artisan boost:install`) dan `AGENTS.md` dibaca ulang sebelum perubahan aplikasi.

## Di luar lingkup

- Render QR code ke PDF dan halaman verifikasi publik.
- UI editor untuk mengatur `qr_x`, `qr_y`, `qr_size` di halaman admin.
- Backfill kode untuk seluruh penari lama.
