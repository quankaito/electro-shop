<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'country_code'];
    public $timestamps = false; // Không có created_at, updated_at

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}