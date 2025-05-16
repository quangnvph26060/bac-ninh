<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price_usd',
        'price_vnd',
        'distributor',
        'stock',
        'sku',
        'type',
        'status'
    ];

    public function variants()
    {
        return $this->hasMany(MaterialVariant::class);
    }

    public function attributes()
    {
        return $this->hasMany(MaterialAttribute::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latastproduct = self::orderBy('id', 'desc')->first();
            $nextNumber = $latastproduct ? ((int)substr($latastproduct->sku, 2)) + 1 : 1;
            $model->sku = 'VT' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }
}
