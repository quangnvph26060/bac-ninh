<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Storage;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'img_url',
        'name',
        'email',
        'password',
        'address',
        'phone',
        'gender',
        'day_of_birth',
        'status',
        'google_id',
        'otp_code',
        'otp_expires_at',
        'last_otp_sent_at'
    ];



    protected $hidden = [
        'password',
        'remember_token',
        'otp_expires_at',
        'last_otp_sent_at'
    ];

    protected $casts = [
        'day_of_birth' => 'date',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_expires_at' => 'datetime',
        'last_otp_sent_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function isAdmin()
    {
        return $this->role_id == 1;
    }

    // Relationship with City
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Relationship with Field
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    // Relationship with Config
    public function config()
    {
        return $this->hasOne(Config::class);
    }

    // Relationship with Storage
    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_user_usages')
            ->withPivot('usage_time')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
