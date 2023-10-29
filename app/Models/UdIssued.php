<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UdIssued extends Model
{
    use HasFactory;

    protected $table = 'ud_issued';
    public $timestamps = false;
}
