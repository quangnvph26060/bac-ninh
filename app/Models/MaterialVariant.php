<?php

namespace App\Models;

use App\Models\AttributeValue;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialVariant extends Model
{
    use HasFactory;

    protected $table = 'material_variants';

    protected $fillable = [
        'material_id',
        'sku',
        'stock',
        'attribute_value_combine',
        'price',
        'product_unit'
    ];

    // Nếu có quan hệ với bảng Material:
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_value_material_variants')->withPivot(['material_variant_id', 'attribute_value_id']);;
    }
}
