<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'transaction_type',
        'status',
        'note',
        'bank_account',
        'code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
