<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use HasFactory, NodeTrait;
    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'seo_title',
        'seo_description',
        'status',
        'parent_id',
        'depth'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_category');
    }
    public static function boot()
    {
        parent::boot();

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
