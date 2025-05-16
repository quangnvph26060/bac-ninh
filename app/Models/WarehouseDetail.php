<?php

namespace App\Models;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseDetail extends Model
{
    use HasFactory;
    protected $table = 'warehouse_details';

    protected $fillable = [
        'name',
        'type',
        'name_parent',
        'quantity',
        'price',
        'price_type',
        'distributor',
        'warehouse_id',
        'note'
    ];

    // Quan hệ với bảng warehouses
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
