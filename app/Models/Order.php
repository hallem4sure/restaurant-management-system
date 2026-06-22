<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'table_id',
        'reservation_id',
        'waiter_id',
        'offer_id',
        'type',
        'status',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'service_charge_rate',
        'service_charge_amount',
        'total_amount',
        'special_instructions',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_charge_rate' => 'decimal:2',
            'service_charge_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function bill()
    {
        return $this->hasOne(Bill::class, 'order_id');
    }
}
