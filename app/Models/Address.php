<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone_number',
        'address_line1',
        'address_line2',
        'ward_id',
        'district_id',
        'province_id',
        'country_code',
        'postal_code',
        'is_default_shipping',
        'is_default_billing',
        'type',
    ];

    protected $casts = [
        'is_default_shipping' => 'boolean',
        'is_default_billing' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    // Accessor để lấy địa chỉ đầy đủ
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->address_line1,
            $this->address_line2,
            $this->ward?->name,
            $this->district?->name,
            $this->province?->name,
        ];
        return implode(', ', array_filter($parts));
    }
}