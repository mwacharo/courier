<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaybillPrintLog extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'admin_id'
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public $incrementing = false;
}
