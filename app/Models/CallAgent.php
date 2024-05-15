<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallAgent extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $guarded = [];
    public $incrementing = false;

    protected $fillable = [
        'phone_number','client_name','admin_id','status','session_id',
    ];
}
