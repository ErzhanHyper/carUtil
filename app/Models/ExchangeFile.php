<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeFile extends Model
{
    use HasFactory;

    protected $table = 'exchange_file';
    public $timestamps = false;
}
