<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TableSession extends Model
{
    protected $fillable = [
        'table_id',
        'establishment_id',
        'session_token',
        'started_at',
        'ended_at',
        'is_active',
        'customer_name'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    // Boot method to auto-generate session token
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->session_token)) {
                $session->session_token = Str::uuid()->toString();
            }
        });
    }

    // Relationships
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
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

    public function scopeByToken($query, string $token)
    {
        return $query->where('session_token', $token);
    }

    // Helper Methods
    public static function startSession(int $tableId): self
    {
        return self::create([
            'table_id' => $tableId,
            'establishment_id' => Table::find($tableId)->establishment_id,
            'started_at' => now(),
            'is_active' => true,
        ]);
    }

    public function end(): void
    {
        $this->update([
            'ended_at' => now(),
            'is_active' => false,
        ]);
    }

    public function getDuration(): ?int
    {
        if (!$this->ended_at) {
            return now()->diffInMinutes($this->started_at);
        }
        return $this->ended_at->diffInMinutes($this->started_at);
    }
}
