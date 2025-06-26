<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUsage extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'code', 'productable_type', 'productable_id', 'quantity', 'date', 'note', 'created_by'];

    public function productable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(MaterialUsageDetail::class);
    }
}
