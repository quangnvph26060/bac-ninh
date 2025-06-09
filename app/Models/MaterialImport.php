<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_date',
        'import_code'
    ];

}
