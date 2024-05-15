<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public $incrementing = false;
}
