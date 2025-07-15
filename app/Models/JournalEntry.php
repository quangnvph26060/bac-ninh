<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'object_type',
        'document',
        'amount',
        'debit_account',
        'credit_account',
        'note',
        'file',
        'related_type',
        'related_id'
    ];

    protected static function booted()
    {
        static::deleting(function ($journalEntry) {
            switch ($journalEntry->related_type) {
                case 'receipt':
                    Receipt::where('id', $journalEntry->related_id)->delete();
                    break;
                case 'opening_balance':
                    OpeningBalance::where('id', $journalEntry->related_id)->delete();
                    break;
            }
        });
    }
}
