<?php

namespace App\Models;

use App\Models\Attribute;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialAttribute extends Model
{
    use HasFactory;

    protected $table = 'material_attributes';

    protected $fillable = [
        'material_id',
        'attribute_id',
        'attribute_values_ids',
    ];

    // Nếu attribute_values_ids là array (lưu dưới dạng JSON)
    protected $casts = [
        'attribute_values_ids' => 'array',
    ];

    // Quan hệ với Material
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // Quan hệ với Attribute
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
