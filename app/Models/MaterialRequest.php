<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'code',
        'quantity',
        'status',
        'note',
        'created_by',
        'created_at'
    ];

    // 1. Quan hệ với các chi tiết vật tư
    public function items()
    {
        return $this->hasMany(MaterialRequestItem::class);
    }

    // 2. Quan hệ với người tạo yêu cầu
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    // 3. Quan hệ đa hình đến sản phẩm hoặc biến thể sản phẩm
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    // 4. Quan hệ đến đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
