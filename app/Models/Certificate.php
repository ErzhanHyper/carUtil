<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;
    protected $table = 'certificate';
    public $timestamps = false;


    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id', 'id');
    }

}
