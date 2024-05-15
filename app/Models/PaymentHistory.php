<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use Uuids;

    protected $fillable = [
        'order_no', 'reference_name', 'reference_no', 'amount', 'payment_channel', 'status'
    ];

    public $incrementing = false;
}
