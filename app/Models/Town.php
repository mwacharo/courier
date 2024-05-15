<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Town extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'name', 'country_id'
    ];

    public $incrementing = false;

    public function country(){
        return $this->belongsTo(Country::class);
    }
}
