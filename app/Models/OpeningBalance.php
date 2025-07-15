<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'type',
        'amount',
        'note',
        'object_type',
        'object_id',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}
