<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefFactory extends Model
{
    use HasFactory;

    protected $table = 'ref_factory';
    public $timestamps = false;
}
