<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgroFile extends Model
{
    use HasFactory;

    protected $table = 'agro_file';
    public $timestamps = false;
}
