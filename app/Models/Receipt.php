<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    public $fillable = [
        'money_account_id',
        'contra_money_account_id',
        'voucher_type_id',
        'transaction_date',
        'type',
        'objectable_type',
        'objectable_id',
        'amount',
        'note',
        'file_path',
        'created_by'
    ];

    protected $casts = [
        'transaction_date' => 'date'
    ];

    public function objectable()
    {
        return $this->morphTo();
    }

    public function cashAccount()
    {
        return $this->belongsTo(MoneyAccount::class, 'money_account_id');
    }

    public function contraMoneyAccount()
    {
        return $this->belongsTo(MoneyAccount::class, 'contra_money_account_id');
    }


    public function voucherType()
    {
        return $this->belongsTo(VoucherType::class);
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
