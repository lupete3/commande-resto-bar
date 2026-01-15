<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'establishment_id',
        'plan',
        'price',
        'billing_cycle',
        'started_at',
        'ends_at',
        'renewed_at',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'renewed_at' => 'datetime',
    ];

    // Relationships
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
            ->whereBetween('ends_at', [now(), now()->addDays($days)]);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active' &&
            ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function renew(int $months = 1): void
    {
        $this->update([
            'renewed_at' => now(),
            'ends_at' => ($this->ends_at && $this->ends_at->isFuture())
                ? $this->ends_at->addMonths($months)
                : now()->addMonths($months),
            'status' => 'active'
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
