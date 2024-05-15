<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'name', 'address', 'phone_number', 'email', 'country_id'
    ];

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public $incrementing = false;
}
