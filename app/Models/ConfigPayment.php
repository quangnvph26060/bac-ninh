<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_id',
        'enjoyer',
        'account_number'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
