<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone_code'
    ];

    public function shippingMethods()
    {
        return $this->belongsToMany(ShippingMethod::class, 'shipping_method_countries')->withPivot('fee');
    }
}
