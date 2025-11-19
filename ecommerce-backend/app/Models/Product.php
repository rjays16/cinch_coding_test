<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'description',
        'sku',
        'category',
        'brand',
        'price',
        'compare_price',
        'stock',
        'is_active',
        'sizes',
        'color',
        'weight',
        'material',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'sizes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['image_url'];

    /**
     * Get the seller that owns the product.
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return url('storage/' . $this->image);
        }
        return null;
    }
}
