<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'message', 'is_read'
    ];

    public $incrementing = false;
}
