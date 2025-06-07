<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'max_discount_amount',
        'min_order_value',
        'start_date',
        'end_date',
        'usage_limit_per_code',
        'usage_limit_per_user',
        'times_used',
        'is_active',
        'is_featured_on_home',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_promotion')
                    ->withPivot('discount_applied')
                    ->withTimestamps();
    }
}