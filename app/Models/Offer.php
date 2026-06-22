<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'is_active',
        'starts_at',
        'ends_at',
        'applicable_days',
        'applicable_from_time',
        'applicable_to_time',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'applicable_days' => 'array',
        ];
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'offer_menu_item');
    }
}
