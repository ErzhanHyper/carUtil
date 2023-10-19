<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'client';
    public $timestamps = false;

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function client_type()
    {
        return $this->belongsTo(ClientType::class, 'client_type_id', 'id');
    }
}
