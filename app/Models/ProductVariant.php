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
        'stock',
        'standard_shipping',
        'express_shipping',
        'international_shipping',
        'design_width',
        'design_height',
        'design_ppi',
        'design_format'
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

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_value_variants');
    }

    public function getAttributeValueNamesAttribute()
    {
        if (!$this->attribute_value_combine) return [];

        $ids = explode('-', $this->attribute_value_combine); // ['9', '19', '40']
        return AttributeValue::whereIn('id', $ids)->pluck('value')->toArray(); // ['X-L', 'Đỏ', 'Cotton']
    }
}
