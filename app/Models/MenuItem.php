<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'description',
        'price',
        'preparation_time',
        'is_available',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(MenuSubcategory::class, 'subcategory_id');
    }

    public function images()
    {
        return $this->hasMany(MenuItemImage::class, 'menu_item_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'menu_item_tag');
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_menu_item');
    }
}
