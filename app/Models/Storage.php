<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use HasFactory;

    protected $table = 'storages';
    protected $fillable = [
        'name',
        'location',
    ];


    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_storage')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
