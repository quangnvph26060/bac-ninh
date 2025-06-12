<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUsageDetail extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['material_usage_id', 'material_id', 'quantity_used', 'note'];

    public function usage()
    {
        return $this->belongsTo(MaterialUsage::class, 'material_usage_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
