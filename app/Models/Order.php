<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'establishment_id',
        'table_id',
        'table_session_id',
        'order_number',
        'server_id',
        'status',
        'subtotal',
        'tax',
        'total',
        'notes',
        'created_by',
        'prepared_at',
        'served_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'prepared_at' => 'datetime',
        'served_at' => 'datetime',
    ];

    // Boot method to auto-generate order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    // Relationships
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function tableSession(): BelongsTo
    {
        return $this->belongsTo(TableSession::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(User::class, 'server_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeByServer($query, int $userId)
    {
        return $query->where('server_id', $userId);
    }

    public function scopeForEstablishment($query, int $establishmentId)
    {
        return $query->where('establishment_id', $establishmentId);
    }

    // Helper Methods
    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return sprintf('#ORD-%s-%03d', $date, $count);
    }

    public function updateStatus(string $newStatus): void
    {
        $this->update(['status' => $newStatus]);

        if ($newStatus === 'preparing') {
            $this->update(['prepared_at' => now()]);
        } elseif ($newStatus === 'served') {
            $this->update(['served_at' => now()]);
        }

        // Trigger event for real-time notification
        // event(new OrderStatusChanged($this));
    }

    public function calculateTotal(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $tax = 0;

        $this->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal,
        ]);
    }

    public function getWaitingTime(): ?int
    {
        if ($this->served_at) {
            return $this->served_at->diffInMinutes($this->created_at);
        }
        return now()->diffInMinutes($this->created_at);
    }
}
