<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'merchant_id', 'sku', 'barcode', 'name', 'description', 'image', 'quantity', 'low_count', 'spoilt', 'amount'
    ];

    public function merchant(){
        return $this->belongsTo(Merchant::class);
    }

    public function inventory_histories(){
        return $this->hasMany(InventoryHistory::class);
    }

    public function order_items(){
        return $this->hasMany(OrderItem::class);
    }

    public $incrementing = false;
}
