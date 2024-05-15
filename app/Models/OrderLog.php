<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderLog extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'status', 'admin_id'
    ];

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function inventory(){
        return $this->belongsTo(Inventory::class);
    }

    public $incrementing = false;
}
