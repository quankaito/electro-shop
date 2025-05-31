<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'cost',
        'is_active',
        'logo',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cost' => 'decimal:2', // Hoặc 'decimal:0' cho VNĐ
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}