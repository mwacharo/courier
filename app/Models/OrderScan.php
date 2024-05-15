<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderScan extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'order_no', 'order_id', 'branch_id', 'remarks', 'status', 'scan_type', 'rider_id'
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function rider(){
        return $this->belongsTo(Rider::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public $incrementing = false;
}
