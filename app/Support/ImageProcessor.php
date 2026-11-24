<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Memotong (crop tengah) dan mengecilkan gambar unggahan memakai GD, lalu menyimpannya sebagai JPEG
 * di disk privat. Re-encode otomatis membuang metadata EXIF (termasuk lokasi GPS).
 */
class ImageProcessor
{
    public static function store(UploadedFile $file, string $dir, int $w, int $h): string
    {
        $info = @getimagesize($file->getRealPath());
        if (! $info || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            throw new RuntimeException('Format gambar tidak didukung.');
        }
        $src = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));
        if (! $src) {
            throw new RuntimeException('Gambar tidak bisa dibaca.');
        }

        $sw = imagesx($src);
        $sh = imagesy($src);
        $ratio = max($w / $sw, $h / $sh);
        $cw = (int) round($w / $ratio);
        $ch = (int) round($h / $ratio);
        $cx = (int) round(($sw - $cw) / 2);
        $cy = (int) round(($sh - $ch) / 2);

        $dst = imagecreatetruecolor($w, $h);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255)); // latar putih untuk PNG transparan
        imagecopyresampled($dst, $src, 0, 0, $cx, $cy, $w, $h, $cw, $ch);

        ob_start();
        imagejpeg($dst, null, 82);
        $jpeg = (string) ob_get_clean();
        imagedestroy($src);
        imagedestroy($dst);

        $path = trim($dir, '/').'/'.Str::uuid().'.jpg';
        Storage::disk(config('reuni.disk'))->put($path, $jpeg);

        return $path;
    }

    public static function delete(?string $path): void
    {
        if ($path) {
            Storage::disk(config('reuni.disk'))->delete($path);
        }
    }
}
