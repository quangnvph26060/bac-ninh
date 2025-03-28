<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'sgo_transactions';
    protected $fillable = [
        // "wallet_id",
        "amount",
        "status",
        "user_id",
        "notification",
        // 'method_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // public function method(){
    //     return $this->belongsTo(Method::class, 'method_id');
    // }
    // public function wallet(){
    //     return $this->belongsTo(Wallet::class, 'wallet_id');
    // }
}
