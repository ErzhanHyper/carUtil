<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order';
    public $timestamps = false;

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function preorder()
    {
        return $this->belongsTo(PreOrderCar::class, 'id', 'order_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'id', 'order_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function transfer()
    {
        return $this->belongsTo(TransferOrder::class, 'id', 'order_id');
    }

    public function history()
    {
        return $this->hasMany(OrderHistory::class, 'order_id', 'id');
    }
}
