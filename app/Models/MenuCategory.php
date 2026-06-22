<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'section_id',
        'name',
        'description',
        'image',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function section()
    {
        return $this->belongsTo(MenuSection::class, 'section_id');
    }

    public function subcategories()
    {
        return $this->hasMany(MenuSubcategory::class, 'category_id');
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'category_id');
    }
}
