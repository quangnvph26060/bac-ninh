<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialImport extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'supplier_id', 'date', 'note', 'created_by'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(MaterialImportDetail::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function debt()
    {
        return $this->hasOne(SupplierDebt::class);
    }

    public function getPaidAttribute()
    {
        return $this->debt->payments->sum('amount');
    }

    public function getTotalAttribute()
    {
        return $this->details->sum('total_price');
    }
    public function getPaymentStatusAttribute()
    {
        $debt = $this->debt;

        if (!$debt) {
            return '<span class="badge bg-secondary">Không có dữ liệu</span>';
        }

        if ($debt->paid_amount >= $debt->total_amount && $debt->status === "paid") {
            return '<span class="badge bg-success">Đã thanh toán</span>';
        }

        if ($debt->paid_amount > 0 && $debt->paid_amount < $debt->total_amount && $debt->status === "partial") {
            return '<span class="badge bg-warning text-dark">Thanh toán một phần</span>';
        }

        return '<span class="badge bg-danger">Chưa thanh toán</span>';
    }

    protected $casts = [
        'date' => 'date'
    ];
}
