<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialImportDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_import_id',
        'material_id',
        'type_id',
        'supplier_name',
        'quantity',
        'price',
        'unit',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function import()
    {
        return $this->belongsTo(MaterialImport::class, 'material_import_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
