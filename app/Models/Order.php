<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address_id',
        'billing_address_id',
        'shipping_method_id',
        'payment_method_id',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'status',
        'notes',
        'admin_notes',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Tự động tạo order_number nếu chưa có
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                // Tạo mã đơn hàng duy nhất, ví dụ: INV-YYYYMMDD-XXXXX
                $prefix = 'ORD-';
                $datePart = now()->format('Ymd');
                // Cố gắng tạo mã duy nhất, có thể cần logic phức tạp hơn nếu có nhiều đơn hàng cùng lúc
                $randomPart = strtoupper(Str::random(5));
                $order->order_number = $prefix . $datePart . '-' . $randomPart;

                // Đảm bảo order_number là duy nhất
                while (static::where('order_number', $order->order_number)->exists()) {
                    $randomPart = strtoupper(Str::random(5));
                    $order->order_number = $prefix . $datePart . '-' . $randomPart;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'order_promotion')
                    ->withPivot('discount_applied')
                    ->withTimestamps();
    }
}