<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = ['supplier_id', 'name', 'slug', 'logo', 'description', 'website', 'seo_title', 'seo_description', 'status'];

    public function suppliers()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            // Lấy giá trị ảnh cũ trước khi cập nhật
            $oldImage = $model->getOriginal('logo');
            // Nếu có ảnh cũ và ảnh mới khác ảnh cũ
            if (!empty($oldImage) && $oldImage !== $model->image) {
                deleteImage($oldImage);
            }
        });
    }
}
