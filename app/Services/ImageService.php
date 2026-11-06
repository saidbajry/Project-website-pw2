<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Semua foto di-crop dari tengah ke rasio tetap lalu di-resize, sehingga
 * tata letak (kiri, kanan, atas, bawah) selalu seragam apa pun ukuran file aslinya.
 */
class ImageService
{
    public static function store(UploadedFile $file, string $dir, int $w, int $h): string
    {
        $src = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));
        abort_unless($src, 422, 'File gambar tidak valid.');

        if ($file->getMimeType() === 'image/jpeg' && function_exists('exif_read_data')) {
            $o = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
            $src = match ($o) { 3 => imagerotate($src, 180, 0), 6 => imagerotate($src, -90, 0), 8 => imagerotate($src, 90, 0), default => $src };
        }

        $sw = imagesx($src); $sh = imagesy($src);
        $ratio = $w / $h;
        $cw = $sw; $ch = (int) round($sw / $ratio);
        if ($ch > $sh) { $ch = $sh; $cw = (int) round($sh * $ratio); }
        $sx = (int) (($sw - $cw) / 2); $sy = (int) (($sh - $ch) / 2);

        $dst = imagecreatetruecolor($w, $h);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $src, 0, 0, $sx, $sy, $w, $h, $cw, $ch);

        $rel = "uploads/{$dir}/" . Str::random(24) . '.jpg';
        $abs = public_path($rel);
        if (! is_dir(dirname($abs))) mkdir(dirname($abs), 0775, true);
        imagejpeg($dst, $abs, 85);
        return $rel;
    }

    /** Hanya mengecilkan (maks. 1600 px), tanpa memotong: rasio asli foto tetap utuh. */
    public static function storeFit(UploadedFile $file, string $dir, int $max = 1600): string
    {
        $src = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));
        abort_unless($src, 422, 'File gambar tidak valid.');

        if ($file->getMimeType() === 'image/jpeg' && function_exists('exif_read_data')) {
            $o = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
            $src = match ($o) { 3 => imagerotate($src, 180, 0), 6 => imagerotate($src, -90, 0), 8 => imagerotate($src, 90, 0), default => $src };
        }

        $sw = imagesx($src); $sh = imagesy($src);
        $scale = min($max / $sw, $max / $sh, 1);
        $w = max(1, (int) round($sw * $scale)); $h = max(1, (int) round($sh * $scale));

        $dst = imagecreatetruecolor($w, $h);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $sw, $sh);

        $rel = "uploads/{$dir}/" . Str::random(24) . '.jpg';
        $abs = public_path($rel);
        if (! is_dir(dirname($abs))) mkdir(dirname($abs), 0775, true);
        imagejpeg($dst, $abs, 88);
        return $rel;
    }

    public static function delete(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/')) @unlink(public_path($path));
    }
}