<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallHistory extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $guarded = [];
    public $incrementing = false;
}
