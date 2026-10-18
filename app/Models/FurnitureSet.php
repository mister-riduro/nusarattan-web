<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FurnitureSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'sale_price',
        'image_url',
        'whatsapp_message',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('group', 'sort_order')
            ->orderByPivot('sort_order');
    }

    public function getFormattedPriceAttribute(): ?string
    {
        return $this->price !== null ? '$' . number_format((float) $this->price, 0) : null;
    }

    public function getFormattedSalePriceAttribute(): ?string
    {
        return $this->sale_price !== null ? '$' . number_format((float) $this->sale_price, 0) : null;
    }
}

