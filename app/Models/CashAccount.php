<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
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
        return $this->belongsTo(CashAccount::class, 'parent_id');
    }

    /**
     * Các tài khoản con
     */
    public function children()
    {
        return $this->hasMany(CashAccount::class, 'parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    protected $casts = [
        'is_default' => 'boolean'
    ];
}
