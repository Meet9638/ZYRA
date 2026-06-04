<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'order_item_id','return_number','reason',
        'detailed_reason','returned_size',
        'preferred_replacement_size','status'
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}

