<?php

namespace App\Models;

use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
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
        'barcode'
    ];

    public function getOrderdetailAttribute()
    {
        return OrderDetail::where('order_id', $this->attributes['id'])->get();
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    protected static function booted()
    {
        static::creating(function ($order) {
            do {
                $barcode = 'ORDER-' . strtoupper(Str::random(8));
            } while (Order::where('barcode', $barcode)->exists());

            $order->barcode = $barcode;
        });
    }

}
