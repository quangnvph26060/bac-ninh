<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'config_payment_id',
        'code',
        'amount',
        'note',
        'type',
        'balance_before',
        'balance_after',
        'status',
        'proof',
        'transaction_code',
        'metadata',
        'reason',
        'is_topup_request'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function configPayment()
    {
        return $this->belongsTo(ConfigPayment::class);
    }

    protected $casts = [
        'metadata' => 'array',
        'is_topup_request' => 'boolean'
    ];
}
