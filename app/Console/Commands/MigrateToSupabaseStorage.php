<?php

namespace App\Console\Commands;

use App\Models\FurnitureSet;
use App\Models\ProductImage;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateToSupabaseStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate-supabase 
                            {--dry-run : Preview actions without uploading or changing database}
                            {--skip-upload : Skip uploading files, only update database URLs}
                            {--skip-db : Skip database URL updates, only upload files}
                            {--folder= : Only process a specific directory within storage/app/public}
                            {--force : Run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate local storage files to Supabase Storage and update database image URLs';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $skipUpload = (bool) $this->option('skip-upload');
        $skipDb = (bool) $this->option('skip-db');
        $targetFolder = $this->option('folder');
        $force = (bool) $this->option('force');

        $this->info('================================================================');
        $this->info(' Nusarattan: Migrate Storage to Supabase');
        $this->info('================================================================');

        if ($dryRun) {
            $this->warn(' Running in DRY-RUN mode. No files will be uploaded and no DB changes will be saved.');
        }

        // 1. Verify Configuration
        $key = config('filesystems.disks.supabase.key');
        $secret = config('filesystems.disks.supabase.secret');
        $endpoint = config('filesystems.disks.supabase.endpoint');
        $bucket = config('filesystems.disks.supabase.bucket', 'nusarattan');
        $supabasePublicUrl = $this->resolveSupabasePublicUrl();

        $this->table(['Setting', 'Value'], [
            ['Supabase Endpoint', $endpoint ?: '<NOT SET>'],
            ['Supabase Bucket', $bucket ?: '<NOT SET>'],
            ['Public Base URL', $supabasePublicUrl ?: '<NOT SET>'],
            ['S3 Key Configured', ! empty($key) ? 'Yes ('.substr($key, 0, 6).'...)' : 'No'],
            ['S3 Secret Configured', ! empty($secret) ? 'Yes (***)' : 'No'],
            ['Target Folder', $targetFolder ?: 'All (products, furniture-sets, site, about, gallery)'],
        ]);

        if (! $skipUpload && (empty($key) || empty($secret) || empty($endpoint))) {
            $this->error('Missing Supabase S3 credentials in your .env file!');
            $this->line('');
            $this->line('Please add these to your .env:');
            $this->comment('SUPABASE_S3_KEY=your_access_key_id');
            $this->comment('SUPABASE_S3_SECRET=your_secret_access_key');
            $this->comment('SUPABASE_S3_REGION=us-east-1');
            $this->comment('SUPABASE_BUCKET='.($bucket ?: 'nusarattan'));
            $this->comment('SUPABASE_S3_ENDPOINT=https://<project-ref>.supabase.co/storage/v1/s3');
            $this->comment('SUPABASE_PUBLIC_URL=https://<project-ref>.supabase.co/storage/v1/object/public/'.($bucket ?: 'nusarattan'));
            $this->line('');
            $this->line('How to get them in Supabase:');
            $this->line('1. Open Supabase Dashboard -> Project Settings -> Storage -> S3 Access Keys');
            $this->line('2. Click "New Access Key" and copy the Access Key ID and Secret Access Key');
            $this->line('3. Ensure your bucket "'.($bucket ?: 'nusarattan').'" exists and is marked as "Public"');

            if (! $dryRun) {
                return self::FAILURE;
            }
        }

        // Test Supabase Connection if uploading and not dry-run
        if (! $skipUpload && ! $dryRun) {
            $this->info('Testing Supabase Storage connection...');
            try {
                $testPath = '.test_probe_'.Str::random(8).'.txt';
                Storage::disk('supabase')->put($testPath, 'probe', [
                    'ContentType' => 'text/plain',
                ]);
                Storage::disk('supabase')->delete($testPath);
                $this->info('✓ Supabase Storage connection verified successfully!');
            } catch (\Throwable $e) {
                $this->error('Failed to connect to Supabase Storage: '.$e->getMessage());
                $this->warn('Please check your endpoint, bucket name, and S3 credentials.');

                return self::FAILURE;
            }
        }

        if (! $force && ! $dryRun) {
            if (! $this->confirm('Do you want to proceed with migrating files and updating the database?', true)) {
                $this->info('Migration canceled.');

                return self::SUCCESS;
            }
        }

        // 2. Scan Local Files
        $baseStoragePath = storage_path('app/public');
        if ($targetFolder) {
            $scanPath = $baseStoragePath.DIRECTORY_SEPARATOR.trim($targetFolder, '/\\');
        } else {
            $scanPath = $baseStoragePath;
        }

        if (! File::isDirectory($scanPath)) {
            $this->error("Directory does not exist: {$scanPath}");

            return self::FAILURE;
        }

        $allFiles = File::allFiles($scanPath);
        $filesToUpload = [];

        foreach ($allFiles as $file) {
            $filename = $file->getFilename();
            if (in_array($filename, ['.gitignore', '.DS_Store', 'Thumbs.db'])) {
                continue;
            }

            $relativePath = str_replace(['\\', $baseStoragePath.DIRECTORY_SEPARATOR, $baseStoragePath.'/'], ['/', '', ''], $file->getRealPath());
            $relativePath = ltrim($relativePath, '/');

            $filesToUpload[] = [
                'full_path' => $file->getRealPath(),
                'relative_path' => $relativePath,
                'size_kb' => round($file->getSize() / 1024, 1),
                'mime_type' => File::mimeType($file->getRealPath()) ?: 'application/octet-stream',
            ];
        }

        $this->info(sprintf('Found %d local file(s) to process.', count($filesToUpload)));

        // 3. Upload Files
        $uploadedCount = 0;
        $skippedCount = 0;

        if (! $skipUpload && count($filesToUpload) > 0) {
            $this->output->progressStart(count($filesToUpload));

            foreach ($filesToUpload as $item) {
                $rel = $item['relative_path'];
                $full = $item['full_path'];
                $mime = $item['mime_type'];

                if ($dryRun) {
                    $this->output->progressAdvance();

                    continue;
                }

                try {
                    $stream = fopen($full, 'r');
                    Storage::disk('supabase')->put($rel, $stream, [
                        'ContentType' => $mime,
                        'CacheControl' => 'public, max-age=31536000, immutable',
                    ]);
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                    $uploadedCount++;
                } catch (\Throwable $e) {
                    $this->newLine();
                    $this->error("Failed to upload {$rel}: ".$e->getMessage());
                }

                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
            $this->info("✓ Finished uploading: {$uploadedCount} uploaded.");
        }

        // 4. Update Database URLs
        $updatedImages = 0;
        $updatedSets = 0;
        $updatedSettings = 0;

        if (! $skipDb && ! empty($supabasePublicUrl)) {
            $this->info('Updating database image URLs to Supabase public URLs...');

            // A. ProductImage
            $productImages = ProductImage::all();
            foreach ($productImages as $img) {
                $currentUrl = $img->url;
                $newUrl = $this->transformToSupabaseUrl($currentUrl, $supabasePublicUrl);
                if ($newUrl && $newUrl !== $currentUrl) {
                    $updatedImages++;
                    if (! $dryRun) {
                        $img->update(['url' => $newUrl]);
                    }
                }
            }

            // B. FurnitureSet
            $sets = FurnitureSet::all();
            foreach ($sets as $set) {
                $currentUrl = $set->image_url;
                $newUrl = $this->transformToSupabaseUrl($currentUrl, $supabasePublicUrl);
                if ($newUrl && $newUrl !== $currentUrl) {
                    $updatedSets++;
                    if (! $dryRun) {
                        $set->update(['image_url' => $newUrl]);
                    }
                }
            }

            // C. Setting
            $settings = Setting::all();
            foreach ($settings as $setting) {
                $currentValue = $setting->value;
                if ($currentValue && (Str::contains($currentValue, '/storage/') || Str::startsWith($currentValue, 'http'))) {
                    $newValue = $this->transformToSupabaseUrl($currentValue, $supabasePublicUrl);
                    if ($newValue && $newValue !== $currentValue) {
                        $updatedSettings++;
                        if (! $dryRun) {
                            $setting->update(['value' => $newValue]);
                        }
                    }
                }
            }

            $this->table(['Database Table', 'Records Updated'], [
                ['product_images (url)', $updatedImages],
                ['furniture_sets (image_url)', $updatedSets],
                ['settings (value)', $updatedSettings],
            ]);
        }

        $this->newLine();
        $this->info('================================================================');
        $this->info(' Migration Summary:');
        $this->info(' - Files scanned: '.count($filesToUpload));
        $this->info(' - Files uploaded to Supabase: '.($dryRun ? '0 (dry-run)' : $uploadedCount));
        $this->info(' - Total database records updated: '.($updatedImages + $updatedSets + $updatedSettings));
        $this->info('================================================================');

        if (! $dryRun) {
            $this->comment('Recommendation: In your .env, set FILESYSTEM_DISK=supabase so any new uploads in Filament Admin automatically store directly to Supabase.');
        }

        return self::SUCCESS;
    }

    /**
     * Resolve the Supabase public URL from config.
     */
    private function resolveSupabasePublicUrl(): string
    {
        $configured = config('filesystems.disks.supabase.url');
        if (! empty($configured)) {
            return rtrim($configured, '/');
        }

        $servicesUrl = config('services.supabase.public_url');
        if (! empty($servicesUrl)) {
            return rtrim($servicesUrl, '/');
        }

        $endpoint = config('filesystems.disks.supabase.endpoint');
        $bucket = config('filesystems.disks.supabase.bucket', 'nusarattan');

        if (! empty($endpoint) && ! empty($bucket)) {
            $base = rtrim(str_replace('/storage/v1/s3', '', $endpoint), '/');

            return "{$base}/storage/v1/object/public/{$bucket}";
        }

        return '';
    }

    /**
     * Transform a local URL to a Supabase public URL.
     */
    private function transformToSupabaseUrl(?string $url, string $supabaseBaseUrl): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Already pointing to Supabase
        if (Str::contains($url, '.supabase.co')) {
            return $url;
        }

        // Format 1: http://localhost:8000/storage/products/xyz.webp
        // Format 2: /storage/products/xyz.webp
        if (Str::contains($url, '/storage/')) {
            $path = ltrim(Str::after($url, '/storage/'), '/');

            return rtrim($supabaseBaseUrl, '/').'/'.$path;
        }

        return $url;
    }
}
