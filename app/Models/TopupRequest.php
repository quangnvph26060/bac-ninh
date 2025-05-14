<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopupRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'config_payment_id',
        'amount',
        'balance_after_topup',
        'proof',
        'transaction_code',
        'note'
    ];

    public function configPayment()
    {
        return $this->belongsTo(ConfigPayment::class);
    }
}
