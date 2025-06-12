<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name', 'code', 'unit', 'min_stock', 'note'];

    public function importDetails()
    {
        return $this->hasMany(MaterialImportDetail::class);
    }

    public function usageDetails()
    {
        return $this->hasMany(MaterialUsageDetail::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function boms()
    {
        return $this->hasMany(Bom::class);
    }

    public function setUnitAttribute($value)
    {
        $this->attributes['unit'] = strtolower($value);
    }
}
