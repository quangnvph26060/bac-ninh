<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequestItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'material_request_id',
        'material_id',
        'quantity',
        'note'
    ];

    // 1. Quan hệ ngược về yêu cầu vật tư
    public function request()
    {
        return $this->belongsTo(MaterialRequest::class, 'material_request_id');
    }

    // 2. Quan hệ tới vật tư
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
