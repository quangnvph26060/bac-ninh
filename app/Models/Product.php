<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\ProductImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'discount_id',
        'name',
        'price',
        'priceBuy',
        'product_unit',
        'quantity',
        'description',
        'is_featured',
        'is_new_arrival',
        'category_id',
        'status',
        'brand_id'

    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function carts()
    {
        return $this->belongsToMany(Cart::class);
    }
    public function category()
    {
        return $this->belongsTo(Product::class);
    }

    public function productCompanies()
    {
        return $this->belongsToMany(Company::class, 'company_product');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImages::class);
    }

    public function storages()
    {
        return $this->belongsToMany(Storage::class, 'product_storage')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latastproduct = self::orderBy('id', 'desc')->first();
            $nextNumber = $latastproduct ? ((int)substr($latastproduct->code, 2)) + 1 : 1;
            $model->code = 'KH' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }
}
