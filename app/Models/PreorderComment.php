<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderComment extends Model
{
    use HasFactory;

    protected $table = 'preorder_comment';
    public $timestamps = false;
}
