<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'inventory_product', 'inventory_id', 'sku', 'description', 'weight', 'price', 'quantity', 'quantity_returned'
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function inventory(){
        return $this->belongsTo(Inventory::class);
    }

    public $incrementing = false;
}
