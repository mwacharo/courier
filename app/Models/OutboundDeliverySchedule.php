<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutboundDeliverySchedule extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'from', 'destination', 'extra_weight', 'charge', 'tax', 'total_amount'
    ];

    public $incrementing = false;
}
