<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderImportFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_file_path',
        'data_file_path',
        'updated_at_sample',
        'updated_at_data',
        'uploaded_by'
    ];

    protected $casts = [
        'updated_at_sample' => 'date',
        'updated_at_data' => 'date'
    ];
}
