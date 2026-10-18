<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    private const MAX_SIZE = 1600;

    private const QUALITY = 80;

    private ImageManager $manager;

    public function __construct()
    {
        ini_set('memory_limit', '512M');
        $this->manager = new ImageManager(new Driver);
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

        $path = $folder.'/'.Str::uuid().'.webp';

        $hasSupabaseKey = ! empty(config('filesystems.disks.supabase.key'));
        $defaultDisk = config('filesystems.default');
        $diskName = ($defaultDisk === 'supabase' || $hasSupabaseKey) ? 'supabase' : 'public';

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
        $path = $this->pathFromUrl($url);
        $hasSupabaseKey = ! empty(config('filesystems.disks.supabase.key'));

        // Delete from Supabase if configured or if URL belongs to Supabase
        if ($hasSupabaseKey || Str::contains($url, '.supabase.co')) {
            try {
                Storage::disk('supabase')->delete($path);
            } catch (\Throwable $e) {
                \Log::warning('Failed to delete image from Supabase: '.$e->getMessage());
            }
        }

        // Also clean up from local public storage if exists
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to delete image from local disk: '.$e->getMessage());
        }
    }

    public function publicUrl(string $path): string
    {
        $configuredUrl = config('filesystems.disks.supabase.url');
        if (! empty($configuredUrl)) {
            return rtrim($configuredUrl, '/').'/'.ltrim($path, '/');
        }

        $servicesUrl = config('services.supabase.public_url');
        if (! empty($servicesUrl)) {
            return rtrim($servicesUrl, '/').'/'.ltrim($path, '/');
        }

        $endpoint = config('filesystems.disks.supabase.endpoint');
        $bucket = config('filesystems.disks.supabase.bucket', 'nusarattan');
        if (! empty($endpoint) && ! empty($bucket)) {
            $base = rtrim(str_replace('/storage/v1/s3', '', $endpoint), '/');

            return "{$base}/storage/v1/object/public/{$bucket}/".ltrim($path, '/');
        }

        return Storage::disk('public')->url($path);
    }

    public function pathFromUrl(string $url): string
    {
        if (Str::contains($url, '/storage/v1/object/public/')) {
            $afterPublic = Str::after($url, '/storage/v1/object/public/');
            $bucket = config('filesystems.disks.supabase.bucket', 'nusarattan');
            if (Str::startsWith($afterPublic, $bucket.'/')) {
                return Str::after($afterPublic, $bucket.'/');
            }

            return Str::after($afterPublic, '/');
        }

        if (Str::contains($url, '/storage/')) {
            return ltrim(Str::after($url, '/storage/'), '/');
        }

        return basename($url);
    }
}
