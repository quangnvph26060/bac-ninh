<?php

namespace App\Models;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'zip_code',
        'order_code',
        'order_name',
        'payment_status',
        'status',
        'total',
        'payment_method',
        'phone_number',
        'shipping_address',
        'note',
        'discount',
        'shipping_fee',
        'tax',
        'barcode',
        'tracking',
        'canceled_by'
    ];


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, OrderItem::class, 'order_id', 'id', 'id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    protected static function booted()
    {
        // static::creating(function ($order) {
        //     do {
        //         $barcode = (string) mt_rand(100000000000, 999999999999);
        //     } while (Order::where('barcode', $barcode)->exists());

        //     $order->barcode = $barcode;
        // });
    }
}
