<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['material_id', 'quantity'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    protected $casts = [
        'updated_at' => 'datetime',
    ];
}
