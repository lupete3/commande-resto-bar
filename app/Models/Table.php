<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = [
        'establishment_id',
        'table_number',
        'capacity',
        'qr_code_path',
        'is_active',
        'location'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    // Relationships
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TableSession::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->whereDoesntHave('sessions', function ($q) {
                $q->where('is_active', true);
            });
    }

    public function scopeForEstablishment($query, int $establishmentId)
    {
        return $query->where('establishment_id', $establishmentId);
    }

    // Helper Methods
    public function getCurrentSession(): ?TableSession
    {
        return $this->sessions()->where('is_active', true)->first();
    }

    public function isOccupied(): bool
    {
        return $this->sessions()->where('is_active', true)->exists();
    }

    public function getTableUrl(): string
    {
        return route('client.menu', [
            'slug' => $this->establishment->slug,
            'table' => $this->table_number
        ]);
    }

    public function getQrCodeUrl(): string
    {
        if ($this->qr_code_path) {
            return asset('storage/' . $this->qr_code_path);
        }
        return '';
    }
}
