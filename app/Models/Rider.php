<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rider extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'national_id', 'date_of_birth', 'address', 'phone_number', 'email', 'country_id', 'password', 'profile_image', 'firebase_token', 'enabled'
    ];

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public $incrementing = false;
}
