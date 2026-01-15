<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $fillable = [
        'establishment_id',
        'category_id',
        'name',
        'description',
        'price',
        'currency',
        'image_path',
        'is_available',
        'preparation_time',
        'allergens',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'preparation_time' => 'integer',
        'sort_order' => 'integer',
        'allergens' => 'array',
    ];

    // Relationships
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeForEstablishment($query, int $establishmentId)
    {
        return $query->where('establishment_id', $establishmentId);
    }

    public function scopeInCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    // Helper Methods
    public function getFormattedPrice(): string
    {
        $symbols = [
            'CDF' => 'FC',
            'USD' => '$',
            'EUR' => '€'
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;

        return number_format($this->price, 2) . ' ' . $symbol;
    }

    public function getImageUrl(): string
    {
        if ($this->image_path && file_exists(storage_path('app/public/' . $this->image_path))) {
            return asset('storage/' . $this->image_path);
        }
        return asset('images/default-menu-item.png');
    }

    public function hasAllergen(string $allergen): bool
    {
        return in_array($allergen, $this->allergens ?? []);
    }
}
