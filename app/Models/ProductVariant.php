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
        'sale_price',
        'attribute_value_combine',
        'product_unit',
        'discount_price',
        'discount_start',
        'discount_end',
        'stock_status',
    ];

    protected $casts = [
        'discount_start' => 'date',
        'discount_end' => 'date',
    ];

    // Mối quan hệ với bảng products
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
