<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_tag');
    }
}
