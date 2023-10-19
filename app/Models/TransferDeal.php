<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferDeal extends Model
{
    use HasFactory;

    protected $table = 'transfer_deal';
    public $timestamps = false;

    public function transfer_order()
    {
        return $this->belongsTo(TransferOrder::class, 'transfer_order_id', 'id');
    }
}
