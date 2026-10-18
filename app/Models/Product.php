<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'slug',
        'material',
        'description',
        'width_cm',
        'depth_cm',
        'height_cm',
        'price_from',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price_from' => 'decimal:2',
        'width_cm' => 'integer',
        'depth_cm' => 'integer',
        'height_cm' => 'integer',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
            ->ofMany(
                ['is_primary' => 'max', 'sort_order' => 'min', 'id' => 'min'],
                function ($query) {
                    $query->orderByDesc('is_primary')->orderBy('sort_order')->orderBy('id');
                }
            );
    }

    public function furnitureSets(): BelongsToMany
    {
        return $this->belongsToMany(FurnitureSet::class)
            ->withPivot('group', 'sort_order')
            ->orderByPivot('sort_order');
    }

    public function getFormattedPriceAttribute(): ?string
    {
        if ($this->price_from === null) {
            return null;
        }

        $formatted = number_format((float) $this->price_from, 0);
        return "Starts from \${$formatted}";
    }

    public function getDimensionsAttribute(): ?string
    {
        $parts = [];
        if ($this->width_cm) $parts[] = "{$this->width_cm}W";
        if ($this->depth_cm) $parts[] = "{$this->depth_cm}D";
        if ($this->height_cm) $parts[] = "{$this->height_cm}H";

        return !empty($parts) ? implode(', ', $parts) : null;
    }
}

