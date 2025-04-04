<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\ProductImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'discount_id',
        'company_id',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'image',
        'type',
        'sale_price',
        'import_price',
        'discount_price',
        'discount_start',
        'discount_end',
        'product_unit',
        'stock',
        'description',
        'is_featured',
        'is_show_home',
        'cross_sell',
        'status',
        'sku',
        'seo_title',
        'seo_description',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_show_home' => 'boolean',
        'discount_start' => 'date',
        'discount_end'  => 'date',
    ];

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes')->withPivot('attribute_values_ids');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

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
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function images()
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
            $nextNumber = $latastproduct ? ((int)substr($latastproduct->sku, 2)) + 1 : 1;
            $model->sku = 'KH' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }
}
