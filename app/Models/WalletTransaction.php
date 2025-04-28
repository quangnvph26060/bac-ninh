<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'code',
        'amount',
        'bank_account',
        'type',
        'note',
        'balance_before',
        'balance_after',
        'status',
        'metadata'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
