<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'specific_price',
        'stock_quantity',
        'image_id', // ID của ảnh từ bảng product_images
    ];

    protected $casts = [
        'specific_price' => 'decimal:2', // Hoặc 'decimal:0'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function image()
    {
        return $this->belongsTo(ProductImage::class, 'image_id');
    }

    public function options()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_options');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}