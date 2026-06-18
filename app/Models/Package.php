<?php

namespace App\Models;

use App\Services\CloudinaryService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = ['name', 'description', 'price', 'original_price', 'is_active', 'sort_order', 'image_path'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'original_price' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackageItem::class);
    }

    public function getSavingsAttribute(): int
    {
        return $this->original_price - $this->price;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedOriginalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->original_price, 0, ',', '.');
    }

    public function getImageUrlAttribute(): ?string
    {
        return CloudinaryService::getImageUrl($this->image_path);
    }
}
