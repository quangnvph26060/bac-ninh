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
        'content',
        'is_featured',
        'is_show_home',
        'cross_sell',
        'status',
        'sku',
        'seo_title',
        'seo_description',
        'tags',
        'stock_status'
    ];

    protected $casts = [
        'tags' => 'array',
        'cross_sell' => 'array',
        'is_featured' => 'boolean',
        'is_show_home' => 'boolean',
        'discount_start' => 'date',
        'discount_end'  => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeHome($query)
    {
        return $query->where('is_show_home', 1);
    }

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
        return $this->belongsTo(Category::class);
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

        static::updating(function ($model) {
            // Lấy giá trị ảnh cũ trước khi cập nhật
            $oldImage = $model->getOriginal('image');
            // Nếu có ảnh cũ và ảnh mới khác ảnh cũ
            if (!empty($oldImage) && $oldImage !== $model->image) {
                deleteImage($oldImage);
            }
        });
    }
}
