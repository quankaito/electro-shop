<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'stock_quantity',
        'manage_stock',
        'stock_status',
        'category_id',
        'brand_id',
        'is_featured',
        'is_active',
        'views_count',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2', // Hoặc 'decimal:0'
        'sale_price' => 'decimal:2',    // Hoặc 'decimal:0'
        'manage_stock' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Tự động tạo slug nếu chưa có
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function thumbnail()
    {
        // Lấy ảnh thumbnail (có thể bạn sẽ muốn có logic phức tạp hơn)
        return $this->hasOne(ProductImage::class)->where('is_thumbnail', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlistedByUsers()
    {
        return $this->belongsToMany(User::class, 'wishlists');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessor để lấy giá hiệu lực (có thể là sale_price hoặc regular_price)
    public function getEffectivePriceAttribute()
    {
        return $this->sale_price ?: $this->regular_price;
    }
}