<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id','product_id','size',
        'quantity','unit_price','total_price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function returnRequest()
    {
        return $this->hasOne(ReturnModel::class, 'order_item_id');
    }

    /**
     * Accessor for 'return' to match existing code usages.
     */
    public function getReturnAttribute()
    {
        return $this->returnRequest;
    }

    /**
     * Accessor for 'price' to match existing code usages.
     */
    public function getPriceAttribute()
    {
        return $this->unit_price;
    }
}

