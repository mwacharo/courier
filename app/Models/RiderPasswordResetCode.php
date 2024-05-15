<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class RiderPasswordResetCode extends Model
{
    use Uuids;

    protected $fillable = [
        'rider_id', 'code', 'status'
    ];

    public $incrementing = false;
}
