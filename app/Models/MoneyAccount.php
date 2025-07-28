<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'created_by',
        'level',
        'status',
        'parent_id',
        'is_default'
    ];

    /**
     * Tài khoản cha
     */
    public function parent()
    {
        return $this->belongsTo(MoneyAccount::class, 'parent_id');
    }

    /**
     * Các tài khoản con
     */
    public function children()
    {
        return $this->hasMany(MoneyAccount::class, 'parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function entries()
    {
        return $this->hasMany(TransactionEntry::class, 'account_id');
    }

    public function contraEntries()
    {
        return $this->hasMany(TransactionEntry::class, 'contra_account_id');
    }


    protected $casts = [
        'is_default' => 'boolean'
    ];
}
