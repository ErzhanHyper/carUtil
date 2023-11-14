<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderComment extends Model
{
    use HasFactory;

    protected $table = 'pre_order_history';
    public $timestamps = false;
}
