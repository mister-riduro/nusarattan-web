<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;

class ImageUploadService
{
    private const MAX_SIZE = 1600;
    private const QUALITY = 80;

    private ImageManager $manager;

    public function __construct()
    {
        ini_set('memory_limit', '512M');
        $this->manager = new ImageManager(new Driver());
    }

    /** Konversi ke webp, upload ke Supabase (atau fallback disk lokal), kembalikan URL publik. */
    public function store(UploadedFile|string $file, string $folder = 'products'): string
    {
        $source = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (method_exists($this->manager, 'read')) {
            $image = $this->manager->read($source);
        } else {
            $image = $this->manager->decodePath($source);
        }

        if (method_exists($image, 'scaleDown')) {
            $image->scaleDown(self::MAX_SIZE, self::MAX_SIZE);
        }

        if (method_exists($image, 'toWebp')) {
            $webp = $image->toWebp(self::QUALITY);
        } elseif (method_exists($image, 'encodeUsingMediaType')) {
            $webp = $image->encodeUsingMediaType('image/webp', quality: self::QUALITY);
        } else {
            $webp = $image->encode(new WebpEncoder(quality: self::QUALITY));
        }

        $path = $folder . '/' . Str::uuid() . '.webp';

        $hasSupabaseKey = !empty(config('filesystems.disks.supabase.key'));
        $diskName = $hasSupabaseKey ? 'supabase' : 'public';

        Storage::disk($diskName)->put($path, (string) $webp, [
            'ContentType' => 'image/webp',
            'CacheControl' => 'public, max-age=31536000, immutable',
        ]);

        unset($image);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        if ($diskName === 'supabase') {
            return $this->publicUrl($path);
        }

        return Storage::disk('public')->url($path);
    }

    public function delete(string $url): void
    {
        $hasSupabaseKey = !empty(config('filesystems.disks.supabase.key'));
        $diskName = $hasSupabaseKey ? 'supabase' : 'public';

        if ($diskName === 'supabase') {
            Storage::disk('supabase')->delete($this->pathFromUrl($url));
        } else {
            $path = ltrim(Str::after($url, '/storage/'), '/');
            Storage::disk('public')->delete($path);
        }
    }

    private function publicUrl(string $path): string
    {
        $base = config('services.supabase.public_url') ?: config('app.url') . '/storage';
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }

    private function pathFromUrl(string $url): string
    {
        $base = config('services.supabase.public_url') ?: config('app.url') . '/storage';
        return ltrim(Str::after($url, rtrim($base, '/')), '/');
    }
}

