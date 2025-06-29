<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class CashAccount extends Model
{
    use HasFactory, NodeTrait;

    protected $fillable = [
        'code',
        'name',
        'created_by',
        'level',
        'status',
        '_lft',
        '_rgt',
        'parent_id'
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
}
