<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'table_id', 'order_number', 'status', 'payment_status',
        'payment_method', 'payment_token', 'midtrans_order_id',
        'transaction_id', 'payment_type', 'transaction_time',
        'subtotal', 'total_amount', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'total_amount' => 'integer',
        ];
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
