<?php

namespace App\Models;

use App\Models\WarehouseDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'purchase_date',
        'price_usd',
        'price_vnd',
    ];

    public function details()
    {
        return $this->hasMany(WarehouseDetail::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $last = self::orderBy('id', 'desc')->first();

            $nextId = $last ? $last->id + 1 : 1;

            $model->code = 'STOCK' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        });
    }
}
