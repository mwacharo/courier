<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\Notification;
use App\Order;
use App\OrderItem;
use App\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class IntergrationApiController extends Controller
{

    public function getCountryList()
    {
        $countries = DB::table('countries')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get();

        return json_encode($countries);
    }

    public function getTownList(Request $request)
    {
        $towns = DB::table('towns')
            ->where('country_id', $request->id)
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get();

        return json_encode($towns);

    }

    public function shippingCalculator(Request $request){

        $from = $request->from;
        $to = $request->to;
        $weight = $request->weight;
        $merchant_id = $request->merchant_id;
        $destination = $request->destination;

        $merchant = DB::table('merchants')
            ->where('id', $merchant_id)
            ->first();

        if($merchant){

            if($destination == 'outbound'){

                if($merchant->enable_delivery_fee_outbound == 1){

                    $json_array = array(
                        'success' => 1,
                        'amount' => $merchant->delivery_fee_outbound,
                    );

                    $response = $json_array;
                    return json_encode($response);

                }else{

                    $schedule = DB::table('outbound_delivery_schedules')
                        ->where('from', $from)
                        ->where('destination', $to)
                        ->where('deleted_at', null)
                        ->first();

                    if($schedule){

                        $amount = $schedule->total_amount;
                        $extra_weight = $schedule->extra_weight;
                        if($weight > 5){
                            $excess_weight = $weight-5;
                            $amount = $amount + ($excess_weight * $extra_weight);
                        }

                        $json_array = array(
                            'success' => 1,
                            'amount' => $amount,
                        );

                        $response = $json_array;
                        return json_encode($response);

                    }else{

                        $json_array = array(
                            'success' => 0,
                        );

                        $response = $json_array;
                        return json_encode($response);

                    }
                }

            }elseif ($destination == 'inbound'){

                if($merchant->enable_delivery_fee_nairobi == 1){

                    $json_array = array(
                        'success' => 1,
                        'amount' => $merchant->delivery_fee_nairobi,
                    );

                    $response = $json_array;
                    return json_encode($response);

                }else {

                    $json_array = array(
                        'success' => 1,
                        'amount' => 300,
                    );

                    $response = $json_array;
                    return json_encode($response);
                }

            }

        }

    }

    public function createOrder(Request $request){

        $sender_name = "";
        $sender_address = "";
        $sender_email = "";
        $sender_phone = "";
        $sender_phone_alternative = "";
        $sender_country = "";
        $sender_town = "";

        $merchant_id = $request->merchant_id;
        $merchant = DB::table('merchants')
            ->where('id', $merchant_id)
            ->first();
        if($merchant){
            $sender_name = $merchant->name;
            $sender_address = $merchant->address;
            $sender_email = $merchant->email;
            $sender_phone = $merchant->phone_number;
            $sender_country = $merchant->country_id;
            $sender_town = $merchant->town_id;
        }

        $order_no = $request->order_no;
        $cash_on_delivery = $request->cash_on_delivery;
        $cash_on_delivery_amount = $request->cash_on_delivery_amount;
        $is_sender_merchant = true;
        $receiver_name = $request->receiver_name;
        $receiver_address = $request->receiver_address;
        $receiver_email = $request->receiver_email;
        $receiver_phone = $request->receiver_phone;
        $receiver_phone_alternative = $request->receiver_phone_alternative;
        $receiver_latitude = $request->receiver_latitude;
        $receiver_longitude = $request->receiver_longitude;
        $service_type = $request->service_type;
        $pickup_country = $request->pickup_country;
        $pickup_town = $request->pickup_town;
        $pickup_address = $request->pickup_address;
        $amount = $request->amount;
        $items = $request->items;

        $format_phone_number_util = new FormatPhoneNumberUtil();
        if($order_no == ''){

            $order_count = DB::table('orders')
                ->count();
            if($order_count > 0){
                $order = DB::table('orders')
                    ->latest()
                    ->first();
                if($order){
                    $order_id = $order->order_id + 1;
                    $order_no = "BX000".$order_id;
                }
            }else{
                $order_no = "BX0001";
            }

        }

        $order_no_count = DB::table('orders')
            ->where('order_no', $order_no)
            ->count();
        if($order_no_count > 0){

            $json_array = array(
                'success' => 3,
                'message' => 'Order no already exists',
            );

            $response = $json_array;
            return json_encode($response);
        }


        $sender_phone = $format_phone_number_util->formatPhoneNumber($sender_phone);
        if($sender_phone_alternative != ""){
            $sender_phone_alternative = $format_phone_number_util->formatPhoneNumber($sender_phone_alternative);
        }

        $receiver_phone = $format_phone_number_util->formatPhoneNumber($receiver_phone);
        if($receiver_phone_alternative != ""){
            $receiver_phone_alternative = $format_phone_number_util->formatPhoneNumber($receiver_phone_alternative);
        }

        if($cash_on_delivery != ""){
            $cash_on_delivery = true;
        }else{
            $cash_on_delivery = false;
        }

        if($service_type == 'pickup'){

            $service_type = 4;
            if($pickup_country == ''){

                $json_array = array(
                    'success' => 3,
                    'message' => 'Enter pickup country',
                );

                $response = $json_array;
                return json_encode($response);
            }

            if($pickup_town == ''){

                $json_array = array(
                    'success' => 3,
                    'message' => 'Enter pickup town',
                );

                $response = $json_array;
                return json_encode($response);
            }

            if($pickup_address == ''){

                $json_array = array(
                    'success' => 3,
                    'message' => 'Enter pickup address',
                );

                $response = $json_array;
                return json_encode($response);
            }

        }else{
            $service_type = 0;
            $pickup_country = null;
            $pickup_town = null;
            $pickup_address = null;
        }


        $order_object = new Order();
        $order_created = $order_object->create([
            'order_no'=>$order_no,
            'is_sender_merchant'=>$is_sender_merchant,
            'merchant_id'=>$merchant_id,
            'sender_name'=>$sender_name,
            'sender_address'=>$sender_address,
            'sender_email'=>$sender_email,
            'sender_phone'=>$sender_phone,
            'sender_phone_alternative'=>$sender_phone_alternative,
            'sender_country'=>$sender_country,
            'sender_town'=>$sender_town,
            'receiver_name'=>$receiver_name,
            'receiver_address'=>$receiver_address,
            'receiver_email'=>$receiver_email,
            'receiver_phone'=>$receiver_phone,
            'receiver_latitude'=>$receiver_latitude,
            'receiver_longitude'=>$receiver_longitude,
            'service_type'=>$service_type,
            'pickup_country'=>$pickup_country,
            'pickup_town'=>$pickup_town,
            'pickup_address'=>$pickup_address,
            'receiver_phone_alternative'=>$receiver_phone_alternative,
            'payment_type'=>2,
            'cash_on_delivery'=>$cash_on_delivery,
            'cash_on_delivery_amount'=>$cash_on_delivery_amount,
            'amount'=>$cash_on_delivery_amount,
            'order_status'=>'order_pending',

        ]);

        if($order_created){

            $inventory_product = false;
            $items = json_decode($items);
            foreach ($items as $item) {

                $item_weight = 0;
                if($item->weight != ""){
                    $item_weight = $item->weight;
                }

                $item_price = 0;
                if($item->price != ""){
                    $item_price = $item->price;
                }

                $order_item = new OrderItem();
                $item_created = $order_item->create([
                    'order_id'=>$order_created->id,
                    'inventory_product'=>$inventory_product,
                    'description'=>$item->description,
                    'weight'=>$item_weight,
                    'price'=>$item_price,
                    'quantity'=>$item->quantity,
                ]);
            }

            if($inventory_product == true){
                $update_order= DB::table('orders')
                    ->where('id', $order_created->id)
                    ->update([
                        'inventory'=>true,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $order_status = "order_pending";
            $order_log = new OrderLog();
            $item_log = $order_log->create([
                'order_id'=>$order_created->id,
                'status'=>$order_status,
            ]);
if($sender_name  != 'D.LIGHT LTD') {
    if($sender_name == 'WEAR YOUR BRAND'){
        $sender_email = 'marija@wearyourbrand.co';
    }

    //TODO Send email & sms
    $email_util = new EmailUtil();
    $send_email = $email_util->orderReceivedEmail($sender_name, $sender_email, $order_no, $receiver_address);

    $sms_message = "Hi $sender_name!, We have received your order request. We will notify you once it has been processed and dispatched";
    $sms_util = new SMSUtil();
    $sms_util->sendSMS($sender_phone, $sms_message);
}

            $json_array = array(
                'success' => 1,
            );

            $response = $json_array;
            return json_encode($response);

        }else{

            $json_array = array(
                'success' => 0,
            );

            $response = $json_array;
            return json_encode($response);

        }
    }

    public function shippingTrackOrder(Request $request){

        $order_no = $request->order_no;
        $order = DB::table('orders')
            ->where('order_no', $order_no)
            ->first();

        if($order){

            $service_type = "delivery";
            if($order->service_type == 4){

                // Pickup country
                $pickup_country_name = "";
                $country = DB::table('countries')
                    ->where('id', $order->pickup_country)
                    ->first();
                if($country){
                    $pickup_country_name = $country->name;
                }

                // Pickup town
                $pickup_town_name = "";
                $town = DB::table('towns')
                    ->where('id', $order->pickup_town)
                    ->first();
                if($town){
                    $pickup_town_name = $town->name;
                }

                $json_array = array(
                    'status' => 'success',
                    'order_no' => strtoupper($order->order_no),
                    'sender_name' => strtoupper($order->sender_name),
                    'receiver_name' => strtoupper($order->receiver_name),
                    'receiver_address' => strtoupper($order->receiver_address),
                    'receiver_latitude' => strtoupper($order->receiver_latitude),
                    'receiver_longitude' => strtoupper($order->receiver_longitude),
                    'order_status' => strtoupper($order->order_status),
                    'status_date' => strtoupper($order->status_date),
                    'special_instruction' => strtoupper($order->special_instruction),
                    'service_type' => 'pickup',
                    'pickup_country' => strtoupper($pickup_country_name),
                    'pickup_town' => strtoupper($pickup_town_name),
                    'pickup_address' => strtoupper($order->pickup_address),
                );

                $response = $json_array;
                return json_encode($response);

            }else{

                $json_array = array(
                    'status' => 'success',
                    'order_no' => strtoupper($order->order_no),
                    'sender_name' => strtoupper($order->sender_name),
                    'receiver_name' => strtoupper($order->receiver_name),
                    'receiver_address' => strtoupper($order->receiver_address),
                    'receiver_latitude' => strtoupper($order->receiver_latitude),
                    'receiver_longitude' => strtoupper($order->receiver_longitude),
                    'order_status' => strtoupper($order->order_status),
                    'status_date' => strtoupper($order->status_date),
                    'special_instruction' => strtoupper($order->special_instruction),
                    'service_type' => 'delivery',
                );

                $response = $json_array;
                return json_encode($response);
            }

        }else{

            $json_array = array(
                'status' => 'error',
                'message' => 'No order found',
            );

            $response = $json_array;
            return json_encode($response);

        }
    }


}
