<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'order_id',
        'subject_id',
        'code',
        'is_confirmed',
        'rating',
        'feedback',
        'status'
    ];

    protected $casts = [
        'is_confirmed' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public static function getStatusCountsByUser($userId)
    {
        return self::where('user_id', $userId)
            ->selectRaw("status, COUNT(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status');
    }
}
