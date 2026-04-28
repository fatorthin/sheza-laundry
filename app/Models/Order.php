<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'member_id',
        'user_id',
        'status',
        'payment_status',
        'payment_method',
        'weight',
        'subtotal',
        'tax',
        'total',
        'paid_amount',
        'has_kiloan',
        'is_express',
        'notes',
        'picked_up_at'
    ];

    protected $casts = [
        'weight'      => 'decimal:2',
        'subtotal'    => 'decimal:2',
        'tax'         => 'decimal:2',
        'total'       => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'has_kiloan'  => 'boolean',
        'is_express'  => 'boolean',
        'picked_up_at' => 'datetime',
    ];

    public static function generateOrderNumber(): string
    {
        $prefix = 'SL-' . date('Ymd') . '-';
        $last = static::where('order_number', 'like', $prefix . '%')
            ->orderByDesc('id')->first();
        $seq = $last ? ((int) substr($last->order_number, -3)) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'baru'          => 'Baru',
            'dicuci'        => 'Dicuci',
            'disetrika'     => 'Disetrika',
            'siap_diambil'  => 'Siap Diambil',
            'selesai'       => 'Selesai',
            default         => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'baru'         => 'blue',
            'dicuci'       => 'yellow',
            'disetrika'    => 'orange',
            'siap_diambil' => 'green',
            'selesai'      => 'gray',
            default        => 'gray',
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum(fn($item) => $item->subtotal ?? 0);
        $this->update(['subtotal' => $subtotal, 'tax' => 0, 'total' => $subtotal]);
    }

    public function recalculateTotal()
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->tax = 0;
        $this->total = $this->subtotal;
        $this->save();
    }
}
