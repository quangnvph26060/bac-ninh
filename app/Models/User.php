<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserInfo;
use App\Models\Storage;
use App\Models\Campaign;
use App\Models\CampaignDetail;

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
        'remember_token',
        'otp_code',
        'otp_expires_at',
        'last_otp_sent_at',
        'created_at',
        'city_id',
        'district_id',
        'wards_id',
        'field_id'
    ];



    protected $hidden = [
        'password',
        'remember_token',
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
}
