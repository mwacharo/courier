<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboundZoneCharge extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'zone', 'same_day_charge', 'same_day_tax', 'same_day_total_amount', 'overnight_charge', 'overnight_tax', 'overnight_total_amount', 'extra_weight'
    ];

    public $incrementing = false;
}
