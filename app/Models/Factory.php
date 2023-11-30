<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
    use HasFactory;

    protected $table = 'factory';
    public $timestamps = false;

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }
}
