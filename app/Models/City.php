<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = [
        'state_id',
        'name'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }


    // public function district()
    // {
    //     return $this->hasMany(Districts::class);
    // }

    // public function user()
    // {
    //     return $this->hasOne(User::class);
    // }

    // public function company()
    // {
    //     return $this->hasMany(Company::class, 'city_id');
    // }
}
