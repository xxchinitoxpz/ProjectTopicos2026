<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PublicImageStorage
{
    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    public static function url(?string $path): string
    {
        if (empty($path)) {
            return self::placeholder();
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        return self::placeholder();
    }

    public static function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public static function placeholder(): string
    {
        return 'https://ui-avatars.com/api/?name=RSU&color=10B981&background=f0fdf4&size=128';
    }
}
