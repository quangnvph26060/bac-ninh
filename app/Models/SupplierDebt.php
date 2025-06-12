<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierDebt extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'supplier_id', 'material_import_id', 'total_amount', 'paid_amount', 'status', 'note'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function import()
    {
        return $this->belongsTo(MaterialImport::class, 'material_import_id');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
