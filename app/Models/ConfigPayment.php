<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'title',
        'content',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if ($model->image) {
                deleteImage($model->image);
            }
        });
    }
}
