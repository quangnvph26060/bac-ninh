<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CashAccount;
use App\Models\VoucherType;
use App\Models\User;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_account_id',
        'voucher_type_id',
        'created_by',
        'code',
        'date',
        'type',
        'amount',
        'description',
        'attachment',
    ];

    /**
     * Quan hệ tới tài khoản tiền mặt.
     */
    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class, 'cash_account_id');
    }

    /**
     * Quan hệ tới loại chứng từ.
     */
    public function voucherType()
    {
        return $this->belongsTo(VoucherType::class, 'voucher_type_id');
    }

    /**
     * Quan hệ tới người tạo.
     */
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
