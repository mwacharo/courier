<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use Uuids;
    use SoftDeletes;

    protected $fillable = [
        'order_no', 'destination_type', 'inbound_rate_type', 'delivery_distance', 'is_sender_merchant', 'merchant_id', 'sender_name', 'sender_address', 'sender_email', 'sender_phone', 'sender_phone_alternative', 'sender_country', 'sender_town',
        'pickup_country', 'pickup_town', 'pickup_address', 'receiver_name', 'receiver_address', 'receiver_email', 'receiver_phone', 'receiver_phone_alternative', 'receiver_country', 'receiver_town', 'receiver_latitude', 'receiver_longitude',
        'special_instruction', 'payment_type', 'cash_on_delivery', 'cash_on_delivery_amount', 'amount', 'return_amount', 'total_weight', 'service_type', 'insurance', 'order_status', 'status_reason', 'payment_status', 'payment_method', 'cash_amount',
        'mpesa_amount', 'cash_mpesa_amount', 'transaction_code', 'rider_id', 'zone_id', 'delivery_date', 'status_date', 'scheduled_date', 'delivered_date', 'follow_up', 'follow_up_date', 'admin_id', 'inventory', 'agent'
    ];

    public $incrementing = false;

    public function sender_country_item(){
        return $this->belongsTo(Country::class, 'sender_country');
    }

    public function sender_town_item(){
        return $this->belongsTo(Country::class, 'sender_town');
    }

    public function receiver_country_item(){
        return $this->belongsTo(Country::class, 'receiver_country');
    }

    public function receiver_town_item(){
        return $this->belongsTo(Country::class, 'receiver_town');
    }

    public function merchant(){
        return $this->belongsTo(Merchant::class);
    }

    public function rider(){
        return $this->belongsTo(Rider::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function order_items(){
        return $this->hasMany(OrderItem::class);
    }

    public function order_logs(){
        return $this->hasMany(OrderLog::class);
    }

    public function order_scans(){
        return $this->hasMany(OrderScan::class);
    }

    public function waybill_print_logs(){
        return $this->hasMany(WaybillPrintLog::class);
    }

    public function getUpdatedAtAttribute($key)
    {
        return Carbon::parse($key)->format('Y-m-d');
    }
}
