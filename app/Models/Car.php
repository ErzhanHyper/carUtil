<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $table = 'car';
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function car_type()
    {
        return $this->belongsTo(CarType::class, 'car_type_id', 'id');
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class, 'id', 'car_id');
    }

}
