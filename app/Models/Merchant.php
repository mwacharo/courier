<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Merchant extends Authenticatable
{
    use Notifiable, Uuids, HasRoles;
    use SoftDeletes;

    protected $guard = 'merchant';
    protected $fillable = [
        'name', 'merchant_type', 'address', 'phone_number', 'email', 'country_id', 'town_id',
        'enable_cash_on_delivery_fee', 'cash_on_delivery_fee', 'enable_delivery_fee_nairobi', 'delivery_fee_nairobi', 'enable_delivery_fee_outbound', 'delivery_fee_outbound', 'enable_returns_management_fee',
        'enable_warehousing_fee', 'warehousing_fee', 'enable_packaging_fee', 'packaging_fee', 'enable_call_centre_fee', 'call_centre_fee', 'enable_label_printing_fee', 'label_printing_fee',
        'password', 'contract', 'enabled', 'google_sheet', 'admin_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];


    public $incrementing = false;

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function town(){
        return $this->belongsTo(Town::class);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
