<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FotoPenari {
    // Kecilkan ke maksimal 900x1200 px (tidak diperbesar), simpan sebagai JPG kualitas 80
    public static function simpan(UploadedFile $file): string {
        @ini_set('memory_limit', '512M'); // foto HP beresolusi tinggi butuh memori saat diproses

        $src = $file->getRealPath();
        $info = @getimagesize($src);
        $img = match ($info[2] ?? null) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($src),
            IMAGETYPE_PNG  => @imagecreatefrompng($src),
            default        => false,
        };
        if (!$img) {
            throw ValidationException::withMessages(['foto' => 'Foto tidak bisa dibaca. Gunakan file JPG atau PNG yang utuh.']);
        }

        // Foto dari HP sering menyimpan arah putar di data EXIF. Luruskan supaya tidak miring.
        if (($info[2] ?? null) === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($src);
            $putar = match ($exif['Orientation'] ?? 1) { 3 => 180, 6 => -90, 8 => 90, default => 0 };
            if ($putar) $img = imagerotate($img, $putar, 0);
        }

        $w = imagesx($img);
        $h = imagesy($img);
        $skala = min(900 / $w, 1200 / $h, 1);          // tidak pernah memperbesar
        $nw = max(1, (int) round($w * $skala));
        $nh = max(1, (int) round($h * $skala));

        $out = imagecreatetruecolor($nw, $nh);
        imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255)); // latar putih untuk PNG transparan
        imagecopyresampled($out, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);

        ob_start();
        imagejpeg($out, null, 80);
        $data = ob_get_clean();

        $path = 'foto-penari/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($path, $data);
        return $path;
    }
}