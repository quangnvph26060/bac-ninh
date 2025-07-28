<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'note',
        'tableable_type',
        'tableable_id',
        'transaction_date'
    ];

    public function tableable()
    {
        return $this->morphTo();
    }

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

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'income' => 'Phiếu thu',
            'expense' => 'Phiếu trả',
            'other' => 'Khác',
            'debit_notice' => 'Báo nợ (Rút tiền)',
            'credit_notice' => 'Báo có (Nộp tiền)',
            default => 'Không rõ',
        };
    }



    protected $casts = [
        'transaction_date' => 'date'
    ];
}
