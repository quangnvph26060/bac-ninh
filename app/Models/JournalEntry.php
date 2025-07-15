<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'type',
        'object_type',
        'document',
        'amount',
        'debit_account',
        'credit_account',
        'note',
        'file'
    ];

}