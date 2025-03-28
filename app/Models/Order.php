<?php

namespace App\Models;

use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        "total_money",
        "status",
        "note",
        "receive_address",
        "user_id",
        'client_id',
        'name',
        'phone',
        'zip_code',
        'notification',
    ];

    protected $appends = ['orderdetail'];

    public function getOrderdetailAttribute(){
        return OrderDetail::where('order_id',$this->attributes['id'])->get();
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
