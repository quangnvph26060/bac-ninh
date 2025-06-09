<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function types()
    {
        return $this->belongsToMany(Type::class, 'material_type');
    }

    public function importDetails()
    {
        return $this->hasMany(MaterialImportDetail::class);
    }
}
