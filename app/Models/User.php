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
        'name',
        'phone',
        'email',
        'company_name',
        'password',
        'dob',
        'status',
        'role_id',
        'google_id',
        'city_id',
        'tax_code',
        'store_name',
        'field_id',
        'domain',
        'address',
        'storage_id',
        'wallet',
        'img_url',
        'remember_token',
        'otp_code',
        'otp_expires_at',
        'last_otp_sent_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_expires_at' => 'datetime',
        'last_otp_sent_at' => 'datetime',
    ];

    protected $appends = ['user_info'];

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

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }
}
