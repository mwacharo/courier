<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryHistory extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'transaction_type', 'inventory_id', 'quantity', 'balance', 'admin_id'
    ];

    public function inventory(){
        return $this->belongsTo(Inventory::class);
    }

    public $incrementing = false;
}
