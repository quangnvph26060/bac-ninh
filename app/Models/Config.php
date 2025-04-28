<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;


    // Cập nhật các cột có thể điền được
    protected $fillable = [
        'title',
        'company',
        'logo',
        'favicon',
        'address',
        'email',
        'hotline',
        'groups',
        'facebook',
        'youtobe',
        'tiktok',
        'copyright',
        'seo_title',
        'seo_description'
    ];
}
