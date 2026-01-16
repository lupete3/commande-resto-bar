<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Establishment extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'address',
        'phone',
        'currency',
        'email',
        'logo',
        'description',
        'is_active',
        'max_tables',
        'subscription_status',
        'subscription_ends_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_ends_at' => 'datetime',
        'max_tables' => 'integer',
    ];

    // Boot method to auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($establishment) {
            if (empty($establishment->slug)) {
                $establishment->slug = Str::slug($establishment->name);
            }
        });
    }

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Helper Methods
    public function isSubscriptionActive(): bool
    {
        if ($this->subscription_status !== 'active') {
            return false;
        }

        if ($this->subscription_ends_at && $this->subscription_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function getAvailableTablesCount(): int
    {
        return $this->tables()->where('is_active', true)->count();
    }
}
