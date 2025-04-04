<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_id',
        'company_name',
        'representative_name',
        'position',
        'phone',
        'email',
        'address',
        'tax_code',
        'bank_account_number',
        'notes',
        'status'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function brands()
    {
        return $this->hasMany(BrandSupplier::class);
    }
}
