<?php

namespace App\Models;

use App\Traits\Uuids;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable, Uuids, HasRoles;
    use SoftDeletes;

    protected $guard = 'admin';
    protected $fillable = [
        'first_name', 'last_name', 'national_id', 'date_of_birth', 'phone_number', 'email', 'password', 'profile_image', 'role', 'enabled'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];


    public $incrementing = false;


    public function getRouteKeyName()
    {
        return 'uuid';
    }



}
