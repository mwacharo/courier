<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\Notification;
use App\Order;
use App\Http\Controllers\Controller;
use App\OrderItem;
use App\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MerchantOrderApiController extends Controller
{

    public function getOrderList(Request $request)
    {
        $orders = DB::table('orders')
            ->where('merchant_id', $request->id)
            ->where('deleted_at', null)
            ->latest()
            ->limit(100)
            ->get();

        $order_result = array();
        foreach ($orders as $order) {

            // Sender country
            $sender_country_name = "";
            $country = DB::table('countries')
                ->where('id', $order->sender_country)
                ->first();
            if ($country) {
                $sender_country_name = $country->name;
            }

            // Sender town
            $sender_town_name = "";
            $town = DB::table('towns')
                ->where('id', $order->sender_town)
                ->first();
            if ($town) {
                $sender_town_name = $town->name;
            }

            // Receiver country
            $receiver_country_name = "";
            $country = DB::table('countries')
                ->where('id', $order->receiver_country)
                ->first();
            if ($country) {
                $receiver_country_name = $country->name;
            }

            // Receiver town
            $receiver_town_name = "";
            $town = DB::table('towns')
                ->where('id', $order->receiver_town)
                ->first();
            if ($town) {
                $receiver_town_name = $town->name;
            }

            // Get branch
            $branch_name = "";
            $branch = DB::table('branches')
                ->where('id', $order->branch_id)
                ->first();
            if ($branch) {
                $branch_name = $branch->name;
            }

            // Get rider
            $rider_name = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if ($rider) {
                $rider_name = $rider->first_name . " " . $rider->last_name;
            }

            // Get admin
            $admin_name = "";
            $admin = DB::table('admins')
                ->where('id', $order->admin_id)
                ->first();
            if ($admin) {
                $admin_name = $admin->name;
            }

            // Get agent
            $agent_name = "";
            $call_agent = DB::table('call_agents')
                ->where('client_name', $order->agent)
                ->first();

            if ($call_agent) {

                $admin = DB::table('admins')
                    ->where('id', $call_agent->admin_id)
                    ->first();
                if ($admin) {
                    $agent_name = $order->agent . "-" . $admin->first_name;
                }

            }


            $print_count = DB::table('waybill_print_logs')
                ->where('order_id', $order->id)
                ->count();

            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            array_push($order_result,
                array(
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'destination_type' => $order->destination_type,
                    'inbound_rate_type' => $order->inbound_rate_type,
                    'delivery_distance' => $order->delivery_distance,
                    'is_sender_merchant' => $order->is_sender_merchant,
                    'merchant_id' => $order->merchant_id,
                    'sender_name' => $order->sender_name,
                    'sender_address' => $order->sender_address,
                    'sender_email' => $order->sender_email,
                    'sender_phone' => $order->sender_phone,
                    'sender_phone_alternative' => $order->sender_phone_alternative,
                    'sender_country' => $order->sender_country,
                    'sender_country_name' => $sender_country_name,
                    'sender_town' => $order->sender_town,
                    'sender_town_name' => $sender_town_name,
                    'receiver_name' => $order->receiver_name,
                    'receiver_address' => $order->receiver_address,
                    'receiver_gender' => $order->receiver_gender,
                    'receiver_email' => $order->receiver_email,
                    'receiver_phone' => $order->receiver_phone,
                    'receiver_phone_alternative' => $order->receiver_phone_alternative,
                    'receiver_country' => $order->receiver_country,
                    'receiver_country_name' => $receiver_country_name,
                    'receiver_town' => $order->receiver_town,
                    'receiver_town_name' => $receiver_town_name,
                    'receiver_latitude' => $order->receiver_latitude,
                    'receiver_longitude' => $order->receiver_longitude,
                    'special_instruction' => $order->special_instruction,
                    'payment_type' => $order->payment_type,
                    'upsell' => $order->upsell,
                    'cash_on_delivery' => $order->cash_on_delivery,
                    'cash_on_delivery_amount' => $order->cash_on_delivery_amount,
                    'amount' => $order->amount,
                    'service_type' => $order->service_type,
                    'insurance' => $order->insurance,
                    'order_status' => $order->order_status,
                    'status_reason' => $order->status_reason,
                    'custom_reason' => $order->custom_reason,
                    'payment_status' => $order->payment_status,
                    'zone_id' => $order->zone_id,
                    'rider_id' => $order->rider_id,
                    'rider_name' => $rider_name,
                    'branch_id' => $order->branch_id,
                    'branch_name' => $branch_name,
                    'booking_date' => $order->booking_date,
                    'status_date' => $order->status_date,
                    'delivery_date' => $order->delivery_date,
                    'delivered_date' => $order->delivered_date,
                    'scheduled_date' => $order->scheduled_date,
                    'admin_id' => $order->admin_id,
                    'admin_name' => $admin_name,
                    'agent' => $order->agent,
                    'agent_name' => $agent_name,
                    'print_count' => $print_count,
                    'items' => $items,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ));

        }

        return json_encode($order_result);

    }

    public function searchOrder(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $merchant_id = $request->merchant_id;
        $recipient_name = $request->recipient_name;
        $recipient_phone = $request->recipient_phone;
        $order_status = $request->order_status;
        $payment_status = $request->payment_status;
        $order_no = $request->order_no;
        $town_id = $request->town_id;
        $destination_type = $request->destination_type;
        $agent_id = $request->agent_id;

        $orders = DB::table('orders')
            ->where('is_sender_merchant', 1);

        if ($order_no != '') {
            $order_no = strtoupper($order_no);
            $orders->where('order_no', 'LIKE', "%{$order_no}%");
        }

        if ($merchant_id != 'all') {
            $orders->where('merchant_id', $merchant_id);
        }

        if ($agent_id != 'all') {
            $orders->where('agent', $agent_id);
        }

        if ($recipient_name != '') {
            $recipient_name = strtoupper($recipient_name);
            $orders->where('receiver_name', 'LIKE', "%{$recipient_name}%");
        }

        if ($recipient_phone != '') {

            $format_phone_number_util = new FormatPhoneNumberUtil();
            $recipient_phone = $format_phone_number_util->formatPhoneNumber($recipient_phone);
            $orders->where('receiver_phone', 'LIKE', "%{$recipient_phone}%");
        }

        if ($order_date != 'all') {

            if ($order_date == 'today') {

                $orders->whereDate('created_at', Carbon::today());

            } elseif ($order_date == 'current_week') {

                $orders->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            } elseif ($order_date == 'last_week') {

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $orders->whereBetween('created_at', [$start_week, $end_week]);

            } elseif ($order_date == 'current_month') {

                $orders->whereMonth('created_at', Carbon::now()->month);

            } elseif ($order_date == 'current_year') {

                $orders->whereYear('created_at', Carbon::now()->year);

            } elseif ($order_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $orders->whereDate('created_at', '=', $custom_date);

            } elseif ($order_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));

                $orders->whereBetween('created_at', [$start_date, $end_date]);
            }

        }

        if ($destination_type != 'all') {
            $orders->where('destination_type', $destination_type);
        }

        if ($town_id != 'all') {
            $orders->where('receiver_town', $town_id);
        }


        if ($order_status != 'all') {

            if ($order_status == 'order_pending') {
                $orders->where('order_status', 'order_pending');
            } else if ($order_status == 'scheduled') {
                $orders->where('order_status', 'scheduled');
            } else if ($order_status == 'dispatched') {
                $orders->where('order_status', 'dispatched');
            } else if ($order_status == 'overnight') {
                $orders->where('order_status', 'delivery_pending');
            }else if ($order_status == 'in_transit') {
                $orders->where('order_status', 'in_transit');
            }else if ($order_status == 'undispatched') {
                $orders->where('order_status', 'undispatched');
            } else if ($order_status == 'delivered') {
                $orders->where('order_status', 'delivered');
            } else if ($order_status == 'returned') {
                $orders->where('order_status', 'returned');
            } else if ($order_status == 'cancelled') {
                $orders->where('order_status', 'cancelled');
            }
        }

        if ($payment_status != 'all') {

            if ($payment_status == 'pending') {
                $orders->where('payment_status', 0);
            } else if ($payment_status == 'paid') {
                $orders->where('payment_status', 1);
            }
        }

        $orders = $orders->latest()->get();
        $order_result = array();
        foreach ($orders as $order) {

            // Sender country
            $sender_country_name = "";
            $country = DB::table('countries')
                ->where('id', $order->sender_country)
                ->first();
            if ($country) {
                $sender_country_name = $country->name;
            }

            // Sender town
            $sender_town_name = "";
            $town = DB::table('towns')
                ->where('id', $order->sender_town)
                ->first();
            if ($town) {
                $sender_town_name = $town->name;
            }

            // Receiver country
            $receiver_country_name = "";
            $country = DB::table('countries')
                ->where('id', $order->receiver_country)
                ->first();
            if ($country) {
                $receiver_country_name = $country->name;
            }

            // Receiver town
            $receiver_town_name = "";
            $town = DB::table('towns')
                ->where('id', $order->receiver_town)
                ->first();
            if ($town) {
                $receiver_town_name = $town->name;
            }

            // Get branch
            $branch_name = "";
            $branch = DB::table('branches')
                ->where('id', $order->branch_id)
                ->first();
            if ($branch) {
                $branch_name = $branch->name;
            }

            // Get rider
            $rider_name = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if ($rider) {
                $rider_name = $rider->first_name . " " . $rider->last_name;
            }

            // Get admin
            $admin_name = "";
            $admin = DB::table('admins')
                ->where('id', $order->admin_id)
                ->first();
            if ($admin) {
                $admin_name = $admin->first_name . " " . $admin->last_name;
            }

            // Get agent
            $agent_name = "";
            $call_agent = DB::table('call_agents')
                ->where('client_name', $order->agent)
                ->first();

            if ($call_agent) {

                $admin = DB::table('admins')
                    ->where('id', $call_agent->admin_id)
                    ->first();
                if ($admin) {
                    $agent_name = $order->agent . "-" . $admin->first_name;
                }

            }

            $print_count = DB::table('waybill_print_logs')
                ->where('order_id', $order->id)
                ->count();

            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            array_push($order_result,
                array(
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'destination_type' => $order->destination_type,
                    'inbound_rate_type' => $order->inbound_rate_type,
                    'delivery_distance' => $order->delivery_distance,
                    'is_sender_merchant' => $order->is_sender_merchant,
                    'merchant_id' => $order->merchant_id,
                    'sender_name' => $order->sender_name,
                    'sender_address' => $order->sender_address,
                    'sender_email' => $order->sender_email,
                    'sender_phone' => $order->sender_phone,
                    'sender_phone_alternative' => $order->sender_phone_alternative,
                    'sender_country' => $order->sender_country,
                    'sender_country_name' => $sender_country_name,
                    'sender_town' => $order->sender_town,
                    'sender_town_name' => $sender_town_name,
                    'receiver_name' => $order->receiver_name,
                    'receiver_address' => $order->receiver_address,
                    'receiver_gender' => $order->receiver_gender,
                    'receiver_email' => $order->receiver_email,
                    'receiver_phone' => $order->receiver_phone,
                    'receiver_phone_alternative' => $order->receiver_phone_alternative,
                    'receiver_country' => $order->receiver_country,
                    'receiver_country_name' => $receiver_country_name,
                    'receiver_town' => $order->receiver_town,
                    'receiver_town_name' => $receiver_town_name,
                    'receiver_latitude' => $order->receiver_latitude,
                    'receiver_longitude' => $order->receiver_longitude,
                    'special_instruction' => $order->special_instruction,
                    'payment_type' => $order->payment_type,
                    'upsell' => $order->upsell,
                    'cash_on_delivery' => $order->cash_on_delivery,
                    'cash_on_delivery_amount' => $order->cash_on_delivery_amount,
                    'amount' => $order->amount,
                    'service_type' => $order->service_type,
                    'insurance' => $order->insurance,
                    'order_status' => $order->order_status,
                    'status_reason' => $order->status_reason,
                    'custom_reason' => $order->custom_reason,
                    'payment_status' => $order->payment_status,
                    'zone_id' => $order->zone_id,
                    'rider_id' => $order->rider_id,
                    'rider_name' => $rider_name,
                    'branch_id' => $order->branch_id,
                    'branch_name' => $branch_name,
                    'booking_date' => $order->booking_date,
                    'status_date' => $order->status_date,
                    'delivery_date' => $order->delivery_date,
                    'delivered_date' => $order->delivered_date,
                    'scheduled_date' => $order->scheduled_date,
                    'admin_id' => $order->admin_id,
                    'admin_name' => $admin_name,
                    'agent' => $order->agent,
                    'agent_name' => $agent_name,
                    'print_count' => $print_count,
                    'items' => $items,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ));

        }

        return json_encode($order_result);

    }

    public function getOrderDetails(Request $request)
    {

        $order = DB::table('orders')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($order);

    }

    public function getOrderItems(Request $request)
    {

        $order_items = DB::table('order_items')
            ->where('order_id', $request->id)
            ->get();
        return json_encode($order_items);

    }

    public function createOrderDetails(Request $request)
    {

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
        if ($merchant) {
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
        $order_status = $request->order_status;
        $total_weight = $request->total_weight;
        $amount = 0;
        $items = $request->items;

        $format_phone_number_util = new FormatPhoneNumberUtil();

        if ($order_no == '') {

            $order_count = DB::table('orders')
                ->count();
            if ($order_count > 0) {
                $order = DB::table('orders')
                    ->latest()
                    ->first();
                if ($order) {
                    $order_id = $order->order_id + 1;
                    $order_no = "BX000" . $order_id;
                }
            } else {
                $order_no = "BX0001";
            }

        }

        $order_no_count = DB::table('orders')
            ->where('order_no', $order_no)
            ->count();
        if ($order_no_count > 0) {

            $json_array = array(
                'success' => 3,
                'message' => 'Order no already exists',
            );

            $response = $json_array;
            return json_encode($response);
        }


        if ($cash_on_delivery == "true" || $cash_on_delivery == 1) {
            $cash_on_delivery = true;
        } else {
            $cash_on_delivery = false;
        }

        $sender_phone = $format_phone_number_util->formatPhoneNumber($sender_phone);
        if ($sender_phone_alternative != "") {
            $sender_phone_alternative = $format_phone_number_util->formatPhoneNumber($sender_phone_alternative);
        }

        $receiver_phone = $format_phone_number_util->formatPhoneNumber($receiver_phone);
        if ($receiver_phone_alternative != "") {
            $receiver_phone_alternative = $format_phone_number_util->formatPhoneNumber($receiver_phone_alternative);
        }

        if ($cash_on_delivery_amount == "null") {
            $cash_on_delivery_amount = 0;
        }

        $order_object = new Order();
        $order_created = $order_object->create([
            'order_no' => $order_no,
            'is_sender_merchant' => $is_sender_merchant,
            'merchant_id' => $merchant_id,
            'sender_name' => $sender_name,
            'sender_address' => $sender_address,
            'sender_email' => $sender_email,
            'sender_phone' => $sender_phone,
            'sender_phone_alternative' => $sender_phone_alternative,
            'sender_country' => $sender_country,
            'sender_town' => $sender_town,
            'receiver_name' => $receiver_name,
            'receiver_address' => $receiver_address,
            'receiver_email' => $receiver_email,
            'receiver_phone' => $receiver_phone,
            'receiver_phone_alternative' => $receiver_phone_alternative,
            'payment_type' => 2,
            'cash_on_delivery' => $cash_on_delivery,
            'cash_on_delivery_amount' => $cash_on_delivery_amount,
            'amount' => $cash_on_delivery_amount,
            'total_weight' => $total_weight,
            'order_status' => 'order_pending',

        ]);

        if ($order_created) {

            $message = "A new order has been added. Order no $order_no";
            $notification_object = new Notification();
            $notification_object_created = $notification_object->create([
                'message' => $message,
                'is_read' => false,
            ]);

            $inventory_product = false;
            $items = json_decode($items);
            foreach ($items as $item) {

                if ($item->inventory_id != "") {
                    $inventory_product = true;
                }

                $item_weight = 0;
                if ($item->weight != "") {
                    $item_weight = $item->weight;
                }

                $item_price = 0;
                if ($item->price != "") {
                    $item_price = $item->price;
                }

                $inventory_id = null;
                if ($item->inventory_id != '') {
                    $inventory_id = $item->inventory_id;
                }

                $order_item = new OrderItem();
                $item_created = $order_item->create([
                    'order_id' => $order_created->id,
                    'inventory_product' => $inventory_product,
                    'inventory_id' => $inventory_id,
                    'description' => $item->description,
                    'weight' => $item_weight,
                    'price' => $item_price,
                    'quantity' => $item->quantity,
                ]);
            }

            if ($inventory_product == true) {
                $update_order = DB::table('orders')
                    ->where('id', $order_created->id)
                    ->update([
                        'inventory' => true,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $order_log = new OrderLog();
            $item_log = $order_log->create([
                'order_id' => $order_created->id,
                'status' => $order_status,
            ]);

if($sender_name  != 'D.LIGHT LTD') {
    if($sender_name == 'WEAR YOUR BRAND'){
        $sender_email = 'marija@wearyourbrand.co';
    }

    //TODO Send email & sms
    $email_util = new EmailUtil();
    $email_util->orderReceivedEmail($sender_name, $sender_email, $order_no, $receiver_address);

    $sms_message = "Hi $sender_name!, We have received your order request. We will notify you once it has been processed and dispatched";
    $sms_util = new SMSUtil();
    $sms_util->sendSMS($sender_phone, $sms_message);
}

            $json_array = array(
                'success' => 1,
                'redirect' => route('merchant.order')
            );

            $response = $json_array;
            return json_encode($response);

        } else {

            $json_array = array(
                'success' => 0,
            );

            $response = $json_array;
            return json_encode($response);

        }


    }

}
