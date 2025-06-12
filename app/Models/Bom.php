<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['productable_type', 'productable_id', 'material_id', 'quantity_required'];

    public function productable()
    {
        return $this->morphTo();
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
