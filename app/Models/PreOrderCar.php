<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrderCar extends Model
{
    use HasFactory;

    protected $table = 'pre_order_car';
    public $timestamps = false;

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function booking()
    {
        return $this->belongsTo(BookingOrder::class, 'booking_id', 'id');
    }

    public function car_file()
    {
        return $this->hasMany(CarFile::class, 'preorder_id', 'id');
    }

    public function agro_file()
    {
        return $this->hasMany(AgroFile::class, 'preorder_id', 'id');
    }

    public function comment()
    {
        return $this->hasMany(PreorderComment::class, 'preorder_id', 'id')->orderByDesc('created_at');
    }

}
