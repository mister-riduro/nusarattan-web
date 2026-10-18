<?php

namespace App\Models;

use App\Services\ImageUploadService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'url',
        'alt',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ProductImage $image) {
            if ($image->url && class_exists(ImageUploadService::class)) {
                try {
                    app(ImageUploadService::class)->delete($image->url);
                } catch (\Throwable $e) {
                    // Log or ignore if storage not configured / file already gone
                    \Log::warning("Failed to delete image: " . $e->getMessage());
                }
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

