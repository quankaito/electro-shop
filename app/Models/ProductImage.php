<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'alt_text',
        'is_thumbnail',
        'sort_order',
    ];

    protected $casts = [
        'is_thumbnail' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}