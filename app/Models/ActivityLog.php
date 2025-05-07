<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'action',
        'model_type',
        'model_id',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
