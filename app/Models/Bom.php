<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    use HasFactory;
    protected $fillable = ['productable_type', 'productable_id'];

    public function productable()
    {
        return $this->morphTo();
    }

    public function bomItems()
    {
        return $this->hasMany(BomItem::class);
    }

    public function getProductableNameAttribute()
    {
        if ($this->productable_type === \App\Models\Product::class) {
            return $this->productable->name;
        }

        if ($this->productable_type === \App\Models\ProductVariant::class) {
            $productName = optional($this->productable->product)->name;
            $sku = $this->productable->sku;
            return "{$productName} - {$sku}";
        }

        return null;
    }
}
