<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $guard_name = 'admin';
    protected $fillable = [
        'employee_code',
        'full_name',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'address',
        'password',
        'remember_token',
        'status',
        'contract_type',
        'avatar',
        'identity_card_number',
        'identity_card_image',
        'note',
        'is_admin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->employee_code = generateEmployeeCode();
        });
    }
}
