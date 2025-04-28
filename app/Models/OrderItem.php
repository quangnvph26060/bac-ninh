<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'quantity',
        'price',
        'original_price',
        'image'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
