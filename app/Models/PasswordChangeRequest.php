<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'new_password',
        'status',
    ];

    protected $casts = [
        'new_password' => 'hashed'
    ];
}
