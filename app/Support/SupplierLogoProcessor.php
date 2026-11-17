<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupplierLogoProcessor
{
    /**
     * Resize oversized uploaded logos and store them as optimized WebP files on the public disk.
     * Falls back to storing the original upload when GD or WebP decoding is unavailable.
     */
    public static function storeOptimized(
        UploadedFile $file,
        string $directory = 'suppliers',
        int $maxDimension = 512,
        int $quality = 82
    ): string {
        $directory = trim($directory, '/');

        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return $file->store($directory, 'public');
        }

        $realPath = $file->getRealPath();
        $raw = is_string($realPath) ? @file_get_contents($realPath) : false;
        if ($raw === false || $raw === '') {
            return $file->store($directory, 'public');
        }

        $source = @imagecreatefromstring($raw);
        if (! $source) {
            return $file->store($directory, 'public');
        }

        $target = null;

        try {
            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            if ($sourceWidth <= 0 || $sourceHeight <= 0) {
                return $file->store($directory, 'public');
            }

            $longest = max($sourceWidth, $sourceHeight);
            if ($longest > $maxDimension) {
                $scale = $maxDimension / $longest;
                $targetWidth = max(1, (int) round($sourceWidth * $scale));
                $targetHeight = max(1, (int) round($sourceHeight * $scale));
            } else {
                $targetWidth = $sourceWidth;
                $targetHeight = $sourceHeight;
            }

            $target = imagecreatetruecolor($targetWidth, $targetHeight);
            if (! $target) {
                return $file->store($directory, 'public');
            }

            // Preserve alpha transparency for PNG and WebP logos.
            imagealphablending($target, false);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
            imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);

            imagecopyresampled(
                $target,
                $source,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $sourceWidth,
                $sourceHeight
            );

            ob_start();
            $encoded = imagewebp($target, null, $quality);
            $webpBytes = ob_get_clean();

            if (! $encoded || ! is_string($webpBytes) || $webpBytes === '') {
                return $file->store($directory, 'public');
            }

            $baseName = pathinfo($file->hashName(), PATHINFO_FILENAME) ?: Str::random(32);
            $path = "{$directory}/{$baseName}.webp";

            Storage::disk('public')->put($path, $webpBytes);

            return $path;
        } finally {
            imagedestroy($source);
            if ($target) {
                imagedestroy($target);
            }
        }
    }
}
