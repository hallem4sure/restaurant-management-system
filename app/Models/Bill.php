<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_number',
        'order_id',
        'cashier_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'service_charge_amount',
        'total_amount',
        'payment_method',
        'amount_paid',
        'change_amount',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_charge_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
