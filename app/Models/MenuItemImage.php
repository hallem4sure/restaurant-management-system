<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemImage extends Model
{
    protected $fillable = [
        'menu_item_id',
        'path',
        'alt_text',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}
