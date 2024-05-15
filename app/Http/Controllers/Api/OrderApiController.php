<?php

namespace App\Http\Controllers\Api;

use App\Exports\ReportDispatchPolicyExport;
use App\Exports\ReportOrderExport;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\Notification;
use App\Order;
use App\OrderItem;
use App\OrderLog;
use App\WaybillPrintLog;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class OrderApiController extends Controller
{
     private $api_key;
    private $partner_id;

 
    public function getOrderList()
    {

        $orders = Order::with(['merchant', 'rider', 'branch', 'admin', 'order_items', 'waybill_print_logs'])
            ->where('deleted_at', null)
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->latest()
            ->limit(500)
            ->get();

        $order_result = [];

        foreach ($orders as $order) {

            // Sender country
            $sender_country_name = "";
            if ($order->sender_country_item) {
                $sender_country_name = $order->sender_country_item->name;
            }

            $sender_town_name = "";
            if ($order->sender_town_item) {
                $sender_town_name = $order->sender_town_item->name;
            }

            $receiver_country_name = "";
            if ($order->receiver_country_item) {
                $receiver_country_name = $order->receiver_country_item->name;
            }

            $receiver_town_name = "";
            if ($order->receiver_town_item) {
                $receiver_town_name = $order->receiver_town_item->name;
            }

            // Get branch
            $branch_name = "";
            if ($order->branch) {
                $branch_name = $order->branch->name;
            }

            // Get rider
            $rider_name = "";
            if ($order->rider) {
                $rider_name = $order->rider->first_name . " " . $order->rider->last_name;
            }

            $admin_name = "";
            if ($order->admin) {
                $admin_name = $order->admin->first_name . " " . $order->admin->last_name;
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

            $print_count = 0;
            if ($order->waybill_print_logs) {
                $print_count = count($order->waybill_print_logs);
            }

            array_push($order_result,
                [
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
                    'status_reason' => $order->status_reason,
                    'custom_reason' => $order->custom_reason,
                    'order_status' => $order->order_status,
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
                    'items' => $order->order_items,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]);

        }

        return json_encode($order_result);

    }

    public function fetchOrders($perpage)
    {
        return Order::with('order_items')->paginate($perpage);

    }

    public function getOrderFollowupList()
    {

        $orders = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('deleted_at', null)
            ->latest()
            ->limit(500)
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
                    'status_reason' => $order->status_reason,
                    'custom_reason' => $order->custom_reason,
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

    public function getOrderPendingList()
    {

        $orders = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('status_date', null)
            ->where('deleted_at', null)
            ->limit(500)
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
                    'status_reason' => $order->status_reason,
                    'custom_reason' => $order->custom_reason,
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
                    'print_count' => $print_count,
                    'agent' => $order->agent,
                    'agent_name' => $agent_name,
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
            ->where('deleted_at', null)
            ->get();
        return json_encode($order_items);

    }

    public function createOrderDetails(Request $request)
    {

        $order_no = $request->order_no;
        $destination_type = $request->destination_type;
        $delivery_distance = $request->delivery_distance;
        $service_type = $request->service_type;
        $inbound_rate_type = $request->inbound_rate_type;
        $cash_on_delivery = $request->cash_on_delivery;
        $cash_on_delivery_amount = $request->cash_on_delivery_amount;
        $is_sender_merchant = $request->is_sender_merchant;
        $merchant_id = $request->merchant_id;
        $sender_name = $request->sender_name;
        $sender_address = $request->sender_address;
        $sender_email = $request->sender_email;
        $sender_phone = $request->sender_phone;
        $sender_phone_alternative = $request->sender_phone_alternative;
        $sender_country = $request->sender_country;
        $sender_town = $request->sender_town;
        $receiver_name = $request->receiver_name;
        $receiver_address = $request->receiver_address;
        $receiver_email = $request->receiver_email;
        $receiver_phone = $request->receiver_phone;
        $receiver_phone_alternative = $request->receiver_phone_alternative;
        $receiver_country = $request->receiver_country;
        $receiver_town = $request->receiver_town;
        $receiver_latitude = $request->receiver_latitude;
        $receiver_longitude = $request->receiver_longitude;
        $delivery_date = $request->delivery_date;
        $special_instruction = $request->special_instruction;
        $payment_type = $request->payment_type;
        $insurance = $request->insurance;
        $order_status = 'order_pending';
        $status_reason = $request->status_reason;
        $payment_method = $request->payment_method;
        $payment_status = $request->payment_status;
        $rider_id = $request->rider_id;
        $zone_id = $request->zone_id;
        $total_weight = $request->total_weight;
        $amount = $request->amount;
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

        if ($is_sender_merchant == "true" || $is_sender_merchant == 1) {
            $is_sender_merchant = true;
        } else {
            $is_sender_merchant = false;
        }

        if ($service_type == "") {
            $service_type = 0;
        }

        if ($destination_type == "") {
            $destination_type = 0;
        }

        if ($inbound_rate_type == "") {
            $inbound_rate_type = 0;
        }

        if ($cash_on_delivery == "") {
            $cash_on_delivery = false;
        }

        if ($payment_status == "") {
            $payment_status = 0;
        }

        if ($is_sender_merchant) {

            $merchant = DB::table('merchants')
                ->where('id', $merchant_id)
                ->first();
            if ($merchant) {
                $sender_name = $merchant->name;
                $sender_address = $merchant->address;
                $sender_email = $merchant->email;
                $sender_phone = $merchant->phone_number;
            }
        }

        if ($insurance == "true" || $insurance == 1) {
            $insurance = true;
        } else {
            $insurance = false;
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

        if ($destination_type == 1) {

            $order_object = new Order();
            $order_created = $order_object->create([
                'order_no' => $order_no,
                'destination_type' => $destination_type,
                'service_type' => $service_type,
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
                'receiver_country' => $receiver_country,
                'receiver_town' => $receiver_town,
                'receiver_latitude' => $receiver_latitude,
                'receiver_longitude' => $receiver_longitude,
                'special_instruction' => $special_instruction,
                'payment_type' => $payment_type,
                'cash_on_delivery' => $cash_on_delivery,
                'cash_on_delivery_amount' => $cash_on_delivery_amount,
                'amount' => $amount,
                'total_weight' => $total_weight,
                'order_status' => $order_status,
                'insurance' => $insurance,
                'rider_id' => $rider_id,
            ]);

            if ($order_created) {

                if ($is_sender_merchant == false) {
                    $message = "A new order has been added. Order no $order_no";
                    $notification_object = new Notification();
                    $notification_object_created = $notification_object->create([
                        'message' => $message,
                        'is_read' => false,
                    ]);
                }

                // TODO Log branch created
                $log_controller = new LogController();
                $log_controller->orderCreateLog($request->causer_id, $order_created->id, $order_created->order_no);

                $order_log_object = new OrderLog();
                $order_log_created = $order_log_object->create([
                    'admin_id' => $request->causer_id,
                    'order_id' => $order_created->id,
                    'status' => 'Order created',
                ]);

                $items = json_decode($items);
                foreach ($items as $item) {

                    $inventory_product = false;
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

                    $order_item = new OrderItem();
                    $item_created = $order_item->create([
                        'order_id' => $order_created->id,
                        'inventory_product' => $inventory_product,
                        'inventory_id' => $item->inventory_id,
                        'description' => $item->description,
                        'weight' => $item_weight,
                        'price' => $item_price,
                        'quantity' => $item->quantity,
                    ]);
                }

                if ($inventory_product == true) {
                    $update_order = DB::table('orders')
                        ->where('id', $item->id)
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
                if ($sender_name != 'D.LIGHT LTD') {

                    //TODO Send email & sms
                    $email_util = new EmailUtil();
                    if ($sender_name == 'WEAR YOUR BRAND') {
                        $sender_email = 'marija@wearyourbrand.co';
                    }
                    $send_email = $email_util->orderReceivedEmail($sender_name, $sender_email, $order_no, $receiver_address);

                    $sms_message = "Hi $sender_name!, We have received your order request. We will notify you once it has been processed and dispatched";


                    // Create an instance of SMSUtil
                    $sms_util = new SMSUtil();

                    // Send the SMS
                    $response = $sms_util->sendSMS($sender_phone, $sms_message);

                }

                $json_array = array(
                    'success' => 1,
                    'redirect' => route('admin.order'),
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

        } elseif ($destination_type == 2) {

            $order_object = new Order();
            $order_created = $order_object->create([
                'order_no' => $order_no,
                'destination_type' => $destination_type,
                'inbound_rate_type' => $inbound_rate_type,
                'service_type' => $service_type,
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
                'receiver_country' => $receiver_country,
                'receiver_town' => $receiver_town,
                'receiver_latitude' => $receiver_latitude,
                'receiver_longitude' => $receiver_longitude,
                'special_instruction' => $special_instruction,
                'payment_type' => $payment_type,
                'cash_on_delivery' => $cash_on_delivery,
                'cash_on_delivery_amount' => $cash_on_delivery_amount,
                'amount' => $amount,
                'total_weight' => $total_weight,
                'insurance' => $insurance,
                'order_status' => $order_status,
                'status_reason' => $status_reason,
                'payment_status' => $payment_status,
                'delivery_date' => $delivery_date,
                'delivery_distance' => $delivery_distance,
                'rider_id' => $rider_id,
                'zone_id' => $zone_id,
            ]);

            if ($order_created) {

                $message = "A new order has been added. Order no $order_no";
                $notification_object = new Notification();
                $notification_object_created = $notification_object->create([
                    'message' => $message,
                    'is_read' => false,
                ]);

                // TODO Log branch created
                $log_controller = new LogController();
                $log_controller->orderCreateLog($request->causer_id, $order_created->id, $order_created->order_no);

                $inventory_product = false;
                $items = json_decode($items);
                foreach ($items as $item) {

                    $inventory_product = false;
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

                    $order_item = new OrderItem();
                    $item_created = $order_item->create([
                        'order_id' => $order_created->id,
                        'inventory_product' => $inventory_product,
                        'inventory_id' => $item->inventory_id,
                        'description' => $item->description,
                        'weight' => $item_weight,
                        'price' => $item_price,
                        'quantity' => $item->quantity,
                    ]);
                }

                if ($inventory_product == true) {
                    $update_order = DB::table('orders')
                        ->where('id', $item->id)
                        ->update([
                            'inventory' => true,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                $order_log_object = new OrderLog();
                $order_log_created = $order_log_object->create([
                    'admin_id' => $request->causer_id,
                    'order_id' => $order_created->id,
                    'status' => $order_status,
                ]);

                $json_array = array(
                    'success' => 1,
                    'redirect' => route('admin.order'),
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

    public function editOrderDetails(Request $request)
    {

        $order_no = $request->order_no;
        $destination_type = $request->destination_type;
        $delivery_distance = $request->delivery_distance;
        $service_type = $request->service_type;
        $inbound_rate_type = $request->inbound_rate_type;
        $upsell = $request->upsell;
        $cash_on_delivery = $request->cash_on_delivery;
        $cash_on_delivery_amount = $request->cash_on_delivery_amount;
        $is_sender_merchant = $request->is_sender_merchant;
        $merchant_id = $request->merchant_id;
        $sender_name = $request->sender_name;
        $sender_address = $request->sender_address;
        $sender_email = $request->sender_email;
        $sender_phone = $request->sender_phone;
        $sender_phone_alternative = $request->sender_phone_alternative;
        $sender_country = $request->sender_country;
        $sender_town = $request->sender_town;
        $receiver_name = $request->receiver_name;
        $receiver_address = $request->receiver_address;
        $receiver_email = $request->receiver_email;
        $receiver_phone = $request->receiver_phone;
        $receiver_phone_alternative = $request->receiver_phone_alternative;
        $receiver_gender = $request->receiver_gender;
        $receiver_country = $request->receiver_country;
        $receiver_town = $request->receiver_town;
        $receiver_latitude = $request->receiver_latitude;
        $receiver_longitude = $request->receiver_longitude;
        $delivery_date = $request->delivery_date;
        $special_instruction = $request->special_instruction;
        $payment_type = $request->payment_type;
        $insurance = $request->insurance;
        $order_status = $request->order_status;
        $scheduled_date = $request->scheduled_date;
        $status_reason = $request->status_reason;
        $payment_status = $request->payment_status;
        $payment_method = $request->payment_method;
        $cash_amount = $request->cash_amount;
        $mpesa_amount = $request->mpesa_amount;
        $cash_mpesa_amount = $request->cash_mpesa_amount;
        $transaction_code = $request->transaction_code;
        $rider_id = $request->rider_id;
        $zone_id = $request->zone_id;
        $total_weight = $request->total_weight;
        $amount = $request->amount;
        $items = $request->items;

        if ($destination_type == 'undefined') {
            $destination_type = 0;
        }

        if ($service_type == 'undefined') {
            $service_type = 0;
        }

        $format_phone_number_util = new FormatPhoneNumberUtil();
        if ($cash_on_delivery == "true" || $cash_on_delivery == 1) {
            $cash_on_delivery = true;
        } else {
            $cash_on_delivery = false;
        }

        if ($upsell == "true" || $upsell == 1) {
            $upsell = true;
        } else {
            $upsell = false;
        }

        if ($is_sender_merchant == "true" || $is_sender_merchant == 1) {
            $is_sender_merchant = true;
        } else {
            $is_sender_merchant = false;
        }

        if ($is_sender_merchant == "true" || $is_sender_merchant == 1) {

            $merchant = DB::table('merchants')
                ->where('id', $merchant_id)
                ->first();
            if ($merchant) {
                $sender_name = $merchant->name;
                $sender_address = $merchant->address;
                $sender_email = $merchant->email;
                $sender_phone = $merchant->phone_number;
            }
        }

        if ($insurance == "true" || $insurance == 1) {
            $insurance = true;
        } else {
            $insurance = false;
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

        if ($rider_id == 'null') {
            $rider_id = null;
        }

        if ($destination_type == 1) {

            $update = DB::table('orders')
                ->where('id', $request->id)
                ->update([
                    'order_no' => $order_no,
                    'destination_type' => $destination_type,
                    'service_type' => $service_type,
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
                    'receiver_gender' => $receiver_gender,
                    'receiver_country' => $receiver_country,
                    'receiver_town' => $receiver_town,
                    'receiver_latitude' => $receiver_latitude,
                    'receiver_longitude' => $receiver_longitude,
                    'special_instruction' => $special_instruction,
                    'payment_type' => $payment_type,
                    'upsell' => $upsell,
                    'cash_on_delivery' => $cash_on_delivery,
                    'cash_on_delivery_amount' => $cash_on_delivery_amount,
                    'amount' => $amount,
                    'total_weight' => $total_weight,
                    'insurance' => $insurance,
                    'order_status' => $order_status,
                    'scheduled_date' => $scheduled_date,
                    'status_reason' => $status_reason,
                    'payment_status' => $payment_status,
                    'payment_method' => $payment_method,
                    'cash_amount' => $cash_amount,
                    'mpesa_amount' => $mpesa_amount,
                    'cash_mpesa_amount' => $cash_mpesa_amount,
                    'transaction_code' => $transaction_code,
                    'delivery_date' => $delivery_date,
                    'rider_id' => $rider_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if ($update) {

                // TODO Log branch created
                $order = DB::table('orders')
                    ->where('id', $request->id)
                    ->first();

                $log_controller = new LogController();
                $log_controller->orderEditLog($request->causer_id, $order->id, $order->order_no);

                $inventory_product = false;
                $items = json_decode($items);
                foreach ($items as $item) {

                    if ($item->id == "") {

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
                            'order_id' => $order->id,
                            'inventory_product' => $inventory_product,
                            'inventory_id' => $inventory_id,
                            'sku' => $item->sku,
                            'description' => $item->description,
                            'weight' => $item_weight,
                            'price' => $item_price,
                            'quantity' => $item->quantity,
                        ]);

                    } else {

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

                        $update_item = DB::table('order_items')
                            ->where('id', $item->id)
                            ->update([
                                'inventory_product' => $inventory_product,
                                'inventory_id' => $inventory_id,
                                'description' => $item->description,
                                'sku' => $item->sku,
                                'weight' => $item_weight,
                                'price' => $item_price,
                                'quantity' => $item->quantity,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                    }

                }

                $order_log_object = new OrderLog();
                $order_log_created = $order_log_object->create([
                    'admin_id' => $request->causer_id,
                    'order_id' => $request->id,
                    'status' => $order_status,
                ]);

                $json_array = array(
                    'success' => 1,
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

        } elseif ($destination_type == 2) {

            $update = DB::table('orders')
                ->where('id', $request->id)
                ->update([
                    'order_no' => $order_no,
                    'destination_type' => $destination_type,
                    'inbound_rate_type' => $inbound_rate_type,
                    'service_type' => $service_type,
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
                    'receiver_gender' => $receiver_gender,
                    'receiver_country' => $receiver_country,
                    'receiver_town' => $receiver_town,
                    'receiver_latitude' => $receiver_latitude,
                    'receiver_longitude' => $receiver_longitude,
                    'special_instruction' => $special_instruction,
                    'payment_type' => $payment_type,
                    'upsell' => $upsell,
                    'cash_on_delivery' => $cash_on_delivery,
                    'cash_on_delivery_amount' => $cash_on_delivery_amount,
                    'amount' => $amount,
                    'total_weight' => $total_weight,
                    'insurance' => $insurance,
                    'order_status' => $order_status,
                    'status_reason' => $status_reason,
                    'payment_status' => $payment_status,
                    'payment_method' => $payment_method,
                    'cash_amount' => $cash_amount,
                    'mpesa_amount' => $mpesa_amount,
                    'cash_mpesa_amount' => $cash_mpesa_amount,
                    'transaction_code' => $transaction_code,
                    'delivery_date' => $delivery_date,
                    'delivery_distance' => $delivery_distance,
                    'rider_id' => $rider_id,
                    'zone_id' => $zone_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            if ($update) {

                // TODO Log order created
                $order = DB::table('orders')
                    ->where('id', $request->id)
                    ->first();

                $log_controller = new LogController();
                $log_controller->orderEditLog($request->causer_id, $order->id, $order->order_no);

                $items = json_decode($items);
                foreach ($items as $item) {

                    if ($item->id == "") {

                        $inventory_product = false;
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
                            'order_id' => $order->id,
                            'inventory_product' => $inventory_product,
                            'inventory_id' => $inventory_id,
                            'description' => $item->description,
                            'weight' => $item_weight,
                            'price' => $item_price,
                            'quantity' => $item->quantity,
                        ]);

                    } else {

                        $inventory_product = false;
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

                        $update_item = DB::table('order_items')
                            ->where('id', $item->id)
                            ->update([
                                'inventory_product' => $inventory_product,
                                'inventory_id' => $inventory_id,
                                'description' => $item->description,
                                'weight' => $item_weight,
                                'price' => $item_price,
                                'quantity' => $item->quantity,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                    }

                }

                $order_log_object = new OrderLog();
                $order_log_created = $order_log_object->create([
                    'admin_id' => $request->causer_id,
                    'order_id' => $request->id,
                    'status' => $order_status,
                ]);

                $json_array = array(
                    'success' => 1,
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

    public function integrationOrderDelete(Request $request)
    {

        $delete = DB::table('orders')
            ->where('order_no', 'LIKE', "%{$request->order_no}%")
            ->where('order_status', 'order_pending')
            ->delete();

        if ($delete) {

            $json_array = array(
                'success' => 1,
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

    public function integrationOrderDeleteMultiple(Request $request)
    {

        $delete = Order::query()
            ->whereIn('order_no', [
                '6177-36198',
                '6176-11449',
                '6177-36260',
                '6177-36285',
                '6176-11462',
                '6176-11469',
                '6177-36257',
                '6176-11472',
                '6176-11397',
                '6176-11375',
                '6176-11346',
                '6176-11000',
                '6176-11357',
                '6176-11295',
            ])
            ->where('order_status', 'order_pending')
            ->delete();

        if ($delete) {

            $json_array = array(
                'success' => 1,
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

    public function deleteOrderDetails(Request $request)
    {

        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            // TODO Log order created
            $order = DB::table('orders')
                ->where('id', $request->id)
                ->first();

            $log_controller = new LogController();
            $log_controller->orderDeleteLog($request->causer_id, $order->id, $order->order_no);

            $order_log_object = new OrderLog();
            $order_log_created = $order_log_object->create([
                'admin_id' => $request->causer_id,
                'order_id' => $request->id,
                'status' => 'Order deleted',
            ]);

            // TODO - Redirect route after delete
            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.order'),
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

    public function deleteOrderItemDetails(Request $request)
    {

        $delete = DB::table('order_items')
            ->where('id', $request->id)
            ->delete();

        if ($delete) {

            // TODO - Redirect route after delete
            $json_array = array(
                'success' => 1,
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

    public function searchOrder(Request $request)
    {

        $order = DB::table('orders')
            ->where('order_no', $request->order_no)
            ->where('deleted_at', null)
            ->first();

        if ($order) {

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
            $rider_phone = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if ($rider) {
                $rider_name = $rider->first_name . " " . $rider->last_name;
                $rider_phone = $rider->phone_number;
            }

            // Get rider
            $rider_name = "";
            $rider_phone = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if ($rider) {
                $rider_name = $rider->first_name . " " . $rider->last_name;
                $rider_phone = $rider->phone_number;
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

            $enable_returns_management_fee = 0;
            if ($order->is_sender_merchant == 1) {

                $merchant = DB::table('merchants')
                    ->where('id', $order->merchant_id)
                    ->first();
                if ($merchant) {
                    if ($merchant->enable_returns_management_fee == 1) {
                        $enable_returns_management_fee = 1;
                    };
                }
            }

            $json_array = array(
                'id' => $order->id,
                'order_no' => $order->order_no,
                'destination_type' => $order->destination_type,
                'inbound_rate_type' => $order->inbound_rate_type,
                'delivery_distance' => $order->delivery_distance,
                'is_sender_merchant' => $order->is_sender_merchant,
                'merchant_id' => $order->merchant_id,
                'returns_management_fee' => $enable_returns_management_fee,
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
                'status_reason' => $order->status_reason,
                'custom_reason' => $order->custom_reason,
                'payment_status' => $order->payment_status,
                'zone_id' => $order->zone_id,
                'rider_id' => $order->rider_id,
                'rider_name' => $rider_name,
                'rider_phone' => $rider_phone,
                'branch_id' => $order->branch_id,
                'branch_name' => $branch_name,
                'booking_date' => $order->booking_date,
                'delivery_date' => $order->delivery_date,
                'delivered_date' => $order->delivered_date,
                'scheduled_date' => $order->scheduled_date,
                'admin_id' => $order->admin_id,
                'inventory' => $order->inventory,
                'admin_name' => $admin_name,
                'agent' => $order->agent,
                'agent_name' => $agent_name,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            );

            $response = $json_array;
            return json_encode($response);
        } else {

            $json_array = array();
            $response = $json_array;
            return json_encode($response);
        }

    }

    public function assignRider(Request $request)
    {

        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'rider_id' => $request->rider_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.order'),
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

    public function updateOrderStatus(Request $request)
    {

        $order_status = $request->order_status;
        $scheduled_date = $request->scheduled_date;
        $status_reason = $request->status_reason;
        $custom_reason = $request->custom_reason;
        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'order_status' => $order_status,
                'status_date' => date('Y-m-d'),
                'scheduled_date' => $scheduled_date,
                'status_reason' => $status_reason,
                'custom_reason' => $custom_reason,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $order = DB::table('orders')
                ->where('id', $request->id)
                ->first();

            if ($order_status === 'scheduled') {

                $sms_message = "Thank you $order->receiver_name!\nYour order $order->order_no costing KES $order->amount has been successfully scheduled for delivery on $order->scheduled_date in $order->receiver_address by Boxleo Courier and Fulfillment Services Ltd.";
               // Create an instance of SMSUtil
               $sms_util = new SMSUtil();

               // Send the SMS
               $response = $sms_util->sendSMS($order->receiver_phone, $sms_message);


            } else if ($order_status === 'order_pending') {

                $admin = DB::table('admins')
                    ->where('id', $request->causer_id)
                    ->first();
                $sms_message = "Hello $order->receiver_name!,\nThis is $admin->first_name from Boxleo Courier. We received the order you placed online reference $order->order_no at KES $order->amount. We tried contacting you but there was no positive response. We would like to deliver it to you.\n
Kindly contact us on +254 791 960 533 or +254 792 166 814 to confirm availability for delivery process to be made successfully.";
               // Create an instance of SMSUtil
               $sms_util = new SMSUtil();

               // Send the SMS
               $response = $sms_util->sendSMS($order->receiver_phone, $sms_message);

            }

            $order_log_object = new OrderLog();
            $order_log_created = $order_log_object->create([
                'admin_id' => $request->causer_id,
                'order_id' => $request->id,
                'status' => $order_status,
            ]);

            $json_array = array(
                'success' => 1,
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

    public function updateReceiverDetails(Request $request)
    {

        $receiver_name = $request->receiver_name;
        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'receiver_name' => $receiver_name,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
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

    public function updateUpsellDetails(Request $request)
    {

        $upsell = $request->upsell;
        if ($upsell == "true" || $upsell == 1) {
            $upsell = true;
        } else {
            $upsell = false;
        }

        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'upsell' => $upsell,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
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

    public function updateSpecialInstruction(Request $request)
    {

        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'special_instruction' => $request->special_instruction,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
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

    public function updateScheduledDate(Request $request)
    {

        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'status_date' => date('Y-m-d'),
                'scheduled_date' => $request->scheduled_date,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
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

    public function updateStatusDate(Request $request)
    {

        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'status_date' => $request->status_date,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
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

    public function updateLocationDetails(Request $request)
    {

        $destination_type = $request->destination_type;
        $delivery_distance = $request->delivery_distance;
        $service_type = $request->service_type;
        $inbound_rate_type = $request->inbound_rate_type;
        $receiver_country = $request->receiver_country;
        $receiver_town = $request->receiver_town;
        $receiver_address = $request->receiver_address;
        $pickup_country = $request->pickup_country;
        $pickup_town = $request->pickup_town;
        $pickup_address = $request->pickup_address;

        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'destination_type' => $destination_type,
                'inbound_rate_type' => $inbound_rate_type,
                'delivery_distance' => $delivery_distance,
                'pickup_country' => $pickup_country,
                'pickup_town' => $pickup_town,
                'pickup_address' => $pickup_address,
                'service_type' => $service_type,
                'receiver_address' => $receiver_address,
                'receiver_country' => $receiver_country,
                'receiver_town' => $receiver_town,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $order_log_object = new OrderLog();
            $order_log_created = $order_log_object->create([
                'admin_id' => $request->causer_id,
                'order_id' => $request->id,
                'status' => 'Update Location details',
            ]);

            $json_array = array(
                'success' => 1,
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

    public function scheduledOrder(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $merchant_id = $request->merchant_id;

        $orders = DB::table('orders')
            ->where('is_sender_merchant', 1)
            ->where('order_status', 'scheduled');

        if ($order_date != 'all') {

            if ($order_date == 'today') {

                $orders->whereDate('scheduled_date', Carbon::today());

            } elseif ($order_date == 'current_week') {

                $orders->whereBetween('scheduled_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            } elseif ($order_date == 'last_week') {

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $orders->whereBetween('scheduled_date', [$start_week, $end_week]);

            } elseif ($order_date == 'current_month') {

                $orders->whereMonth('scheduled_date', Carbon::now()->month);

            } elseif ($order_date == 'current_year') {

                $orders->whereYear('scheduled_date', Carbon::now()->year);

            } elseif ($order_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $orders->whereDate('scheduled_date', '=', $custom_date);

            } elseif ($order_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));

                $orders->whereBetween('scheduled_date', [$start_date, $end_date]);
            }

        }

        if ($merchant_id != 'all') {
            $orders->where('merchant_id', $merchant_id);
        }

        $orders = $orders->get();
        return json_encode($orders);

    }

    public function inTransitOrder(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $rider_id = $request->rider_id;

        $orders = DB::table('orders')
            ->where('order_status', 'in_transit');

        if ($order_date != 'all') {

            if ($order_date == 'today') {

                $orders->whereDate('status_date', Carbon::today());

            } elseif ($order_date == 'current_week') {

                $orders->whereBetween('status_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            } elseif ($order_date == 'last_week') {

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $orders->whereBetween('status_date', [$start_week, $end_week]);

            } elseif ($order_date == 'current_month') {

                $orders->whereMonth('status_date', Carbon::now()->month);

            } elseif ($order_date == 'current_year') {

                $orders->whereYear('status_date', Carbon::now()->year);

            } elseif ($order_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $orders->whereDate('status_date', '=', $custom_date);

            } elseif ($order_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));

                $orders->whereBetween('status_date', [$start_date, $end_date]);
            }

        }

        if ($rider_id != 'all') {
            $orders->where('rider_id', $rider_id);
        }

        $orders = $orders->get();
        return json_encode($orders);

    }

    public function updateAgentDetails(Request $request)
    {

        $agent = $request->agent;
        $update = DB::table('orders')
            ->where('id', $request->id)
            ->update([
                'agent' => $agent,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
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

    public function dispatchPolicyOrderDefault(Request $request)
    {

        $orders = DB::table('orders')
            ->where('order_status', 'dispatched')
            ->whereDate('status_date', '<', Carbon::now()->subDays(7))
            ->latest()
            ->get();

        return json_encode($orders);

    }

    public function dispatchPolicyOrder(Request $request)
    {

        $merchant_id = $request->merchant_id;
        $orders = DB::table('orders')
            ->where('order_status', 'dispatched')
            ->whereDate('status_date', '<', Carbon::now()->subDays(7));

        if ($merchant_id != 'all') {
            $orders->where('merchant_id', $merchant_id);
        }

        $orders = $orders->get();
        return json_encode($orders);

    }

    public function dispatchPolicyOrderGenerateExcel(Request $request)
    {

        $orders = json_decode($request->orders);
        return Excel::download(new ReportDispatchPolicyExport($orders), 'dispatch-policy-excel.xls');

    }

    //undispatch an order

    public function undispatchOrder(Request $request)
    {

        $order_no = $request->order_no;
        $scheduled_date = $request->scheduled_date;
        $custom_reason = $request->custom_reason;
        $admin_id = $request->causer_id;
        $selected_rider_id = $request->selected_rider_id;

        if ($selected_rider_id) {

            $update = DB::table('orders')
                ->where('order_no', $order_no)
                ->update([
                    'order_status' => "dispatched",
                    'rider_id' => $selected_rider_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'admin_id' => $admin_id,
                ]);

            if ($update) {
                $json_array = array(
                    'status' => 'successs',
                    'message' => 'Order Undispatched successfully',
                );

                $response = $json_array;
                return json_encode($response);

            } else {

                $json_array = array(
                    'status' => 'error',
                    'message' => 'Failed to undispatch order',
                );

                $response = $json_array;
                return json_encode($response);
            }

        }

        if ($scheduled_date) {
            $custom_reason = 'Reschedule for' . " " . $scheduled_date;

            $status = DB::table('orders')
                ->where('order_no', $order_no)
                ->update([
                    'custom_reason' => $custom_reason,
                    'order_status' => "scheduled",
                    'scheduled_date' => $scheduled_date,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'admin_id' => $admin_id,
                ]);

            if ($status) {
                $json_array = array(
                    'status' => 'successs',
                    'message' => 'Order rescheduled successfully',
                );

                $response = $json_array;
                return json_encode($response);

            } else {

                $json_array = array(
                    'status' => 'error',
                    'message' => 'Failed to reschedule order',
                );

                $response = $json_array;
                return json_encode($response);
            }
        }

        $status = DB::table('orders')
            ->where('order_no', $order_no)
            ->update([
                'custom_reason' => $custom_reason,
                'order_status' => "undispatched",
                'updated_at' => date('Y-m-d H:i:s'),
                'admin_id' => $admin_id,
            ]);

        if ($status) {
            $json_array = array(
                'status' => 'successs',
                'message' => 'Order Undispatched successfully',
            );

            $response = $json_array;
            return json_encode($response);

        } else {

            $json_array = array(
                'status' => 'error',
                'message' => 'Failed to undispatch order',
            );

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function unDispatchedOrder(Request $request)
    {

        $order_date = $request->order_date;
        $order_no = $request->order_no;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $merchant_id = $request->merchant_id;

        $orders = DB::table('orders')
            ->where('is_sender_merchant', 1)
            ->where('order_status', 'undispatched')
            ->orderBy('updated_at', 'DESC');

        if ($order_date != 'all') {

            if ($order_date == 'today') {

                $orders->whereDate('updated_at', Carbon::today());

            } elseif ($order_date == 'current_week') {

                $orders->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            } elseif ($order_date == 'last_week') {

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $orders->whereBetween('updated_at', [$start_week, $end_week]);

            } elseif ($order_date == 'current_month') {

                $orders->whereMonth('updated_at', Carbon::now()->month);

            } elseif ($order_date == 'current_year') {

                $orders->whereYear('updated-at', Carbon::now()->year);

            } elseif ($order_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $orders->whereDate('updated_at', '=', $custom_date);

            } elseif ($order_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));

                $orders->whereBetween('updated_at', [$start_date, $end_date]);
            }

        }

        if ($merchant_id != 'all') {
            $orders->where('merchant_id', $merchant_id);
        }

        if ($order_no) {
            $orders->where('order_no', $order_no);
        }

        $orders = $orders->get();
        return json_encode($orders);

    }

    public function dispatchedOrder(Request $request)
    {

        $order_date = $request->order_date;
        $order_no = $request->order_no;

        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $rider_id = $request->rider_id;
        $rider_name = "";

        // Get rider
        $rider = DB::table('riders')
            ->where('id', $rider_id)
            ->first();
        if ($rider) {
            $rider_name = $rider->first_name . " " . $rider->last_name;
        }

        $orders = DB::table('orders')
            ->where('order_status', 'dispatched');

        if ($order_date != 'all') {

            if ($order_date == 'today') {

                $orders->whereDate('updated_at', Carbon::today());

            } elseif ($order_date == 'current_week') {

                $orders->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            } elseif ($order_date == 'last_week') {

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $orders->whereBetween('updated_at', [$start_week, $end_week]);

            } elseif ($order_date == 'current_month') {

                $orders->whereMonth('updated_at', Carbon::now()->month);

            } elseif ($order_date == 'current_year') {

                $orders->whereYear('updated_at', Carbon::now()->year);

            } elseif ($order_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $orders->whereDate('updated_at', '=', $custom_date);

            } elseif ($order_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));

                $orders->whereBetween('updated_at', [$start_date, $end_date]);
            }

        }

        if ($rider_id != 'all') {
            $orders->where('rider_id', $rider_id);
        }

        if ($order_no) {
            $orders->where('order_no', $order_no);
        }

        $orders = $orders->get();

        return json_encode($orders);

    }

    public function undispatchedOrderGenerateExcel(Request $request)
    {

        $orders = json_decode($request->orders);
        return Excel::download(new ReportOrderExport($orders), 'undispatched-orders-excel.xls');
    }

    public function awaitingOrder(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $merchant_id = $request->merchant_id;

        $orders = DB::table('orders')
            ->where('is_sender_merchant', 1)
            ->where('order_status', 'awaiting_dispatch');

        if ($order_date != 'all') {

            if ($order_date == 'today') {

                $orders->whereDate('scheduled_date', Carbon::today());

            } elseif ($order_date == 'current_week') {

                $orders->whereBetween('scheduled_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            } elseif ($order_date == 'last_week') {

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $orders->whereBetween('scheduled_date', [$start_week, $end_week]);

            } elseif ($order_date == 'current_month') {

                $orders->whereMonth('scheduled_date', Carbon::now()->month);

            } elseif ($order_date == 'current_year') {

                $orders->whereYear('scheduled_date', Carbon::now()->year);

            } elseif ($order_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $orders->whereDate('scheduled_date', '=', $custom_date);

            } elseif ($order_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));

                $orders->whereBetween('scheduled_date', [$start_date, $end_date]);
            }

        }

        if ($merchant_id != 'all') {
            $orders->where('merchant_id', $merchant_id);
        }

        $orders = $orders->get();
        return json_encode($orders);

    }

    public function awaitingOrderGenerateExcel(Request $request)
    {

        $orders = json_decode($request->orders);
        return Excel::download(new ReportOrderExport($orders), 'awaiting-dispatch-order-excel.xls');

    }

    public function scheduledOrderGenerateExcel(Request $request)
    {

        $log_controller = new LogController();
        $log_controller->reportOrderGenerateLog($request->causer_id);

        $orders = json_decode($request->orders);
        return Excel::download(new ReportOrderExport($orders), 'scheduled-order-excel.xls');

    }

    public function scheduledOrderBulkPrint(Request $request)
    {

        $causer_id = $request->causer_id;

        $log_controller = new LogController();
        $log_controller->reportOrderGenerateLog($request->admin_id);

        $pdfMerger = PDFMerger::init();

        $orders = json_decode($request->orders);

        foreach ($orders as $order) {

            // Sender town
            $sender_town_name = "";
            $town = DB::table('towns')
                ->where('id', $order->sender_town)
                ->first();
            if ($town) {
                $sender_town_name = $town->name;
            }

            // Receiver town
            $receiver_town_name = "";
            $town = DB::table('towns')
                ->where('id', $order->receiver_town)
                ->first();
            if ($town) {
                $receiver_town_name = $town->name;
            }

            // Get rider
            $rider_name = "";
            $rider_phone = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if ($rider) {
                $rider_name = $rider->first_name . " " . $rider->last_name;
                $rider_phone = $rider->phone_number;
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
                    $admin_name = $order->agent . "-" . $admin->first_name;
                }

            }

            $enable_returns_management_fee = 0;
            if ($order->is_sender_merchant == 1) {

                $merchant = DB::table('merchants')
                    ->where('id', $order->merchant_id)
                    ->first();
                if ($merchant) {
                    if ($merchant->enable_returns_management_fee == 1) {
                        $enable_returns_management_fee = 1;
                    };
                }
            }

            $json_array = [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'destination_type' => $order->destination_type,
                'inbound_rate_type' => $order->inbound_rate_type,
                'delivery_distance' => $order->delivery_distance,
                'is_sender_merchant' => $order->is_sender_merchant,
                'merchant_id' => $order->merchant_id,
                'returns_management_fee' => $enable_returns_management_fee,
                'sender_name' => $order->sender_name,
                'sender_address' => $order->sender_address,
                'sender_email' => $order->sender_email,
                'sender_phone' => $order->sender_phone,
                'sender_phone_alternative' => $order->sender_phone_alternative,
                'sender_country' => $order->sender_country,

                'sender_town' => $order->sender_town,
                'sender_town_name' => $sender_town_name,
                'receiver_name' => $order->receiver_name,
                'receiver_address' => $order->receiver_address,
                'receiver_email' => $order->receiver_email,
                'receiver_phone' => $order->receiver_phone,
                'receiver_phone_alternative' => $order->receiver_phone_alternative,
                'receiver_country' => $order->receiver_country,

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
                'status_reason' => $order->status_reason,
                'custom_reason' => $order->custom_reason,
                'payment_status' => $order->payment_status,
                'zone_id' => $order->zone_id,
                'rider_id' => $order->rider_id,
                'rider_name' => $rider_name,
                'rider_phone' => $rider_phone,
                'branch_id' => $order->branch_id,

                'booking_date' => $order->booking_date,
                'delivery_date' => $order->delivery_date,
                'delivered_date' => $order->delivered_date,
                'scheduled_date' => $order->scheduled_date,
                'total_weight' => $order->total_weight,
                'admin_id' => $order->admin_id,
                'inventory' => $order->inventory,
                'admin_name' => $admin_name,
                'agent' => $order->agent,
                'agent_name' => $agent_name,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ];

            $order_items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            $data = [
                'order' => $json_array,
                'order_items' => $order_items,
            ];

            // Generate the PDF for this order and save it to a temporary file.
            $pdf = PDF::loadView('admin.report.order-waybill', $data, ['format' => 'A4-L']);

            //The file name for each file
            $file_name = str_replace("/", "-", $order->order_no);
            $file_name = 'storage/waybills/' . $file_name . ".pdf";

            //save the generated pdf to the above file name
            $pdf->save($file_name);

            //merge the pdf to a single document
            $pdfMerger->addPDF($file_name, 'all');

            $pring_log_object = new WaybillPrintLog();
            $pring_log_object->create([
                'admin_id' => $causer_id,
                'order_id' => $order->id,
            ]);

            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'order_status' => 'awaiting_dispatch',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

        }

        $pdfMerger->merge();

        //create an instance of filesystem
        $fs = new Filesystem;

        //clear the waybills after download
        $fs->cleanDirectory('storage/waybills');

        //save the generated merged to document to storage folder
        $pdfMerger->save($_SERVER['DOCUMENT_ROOT'] . 'dashboard/storage/uploads/order-waybills-printout.pdf');
        // get the path of the merged PDF file
        $mergedPdfFilePath = $_SERVER['DOCUMENT_ROOT'] . 'dashboard/storage/uploads/order-waybills-printout.pdf';

        // create a BinaryFileResponse with appropriate headers
        $response = new BinaryFileResponse($mergedPdfFilePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'merged-pdf-file.pdf'
        );

        return $response;
    }

    public function transitOrder(Request $request)
    {

        $admin_id = $request->causer_id;
        $orders = json_decode($request->orders);

        foreach ($orders as $order) {
            $update = DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'order_status' => 'in_transit',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'admin_id' => $admin_id,
                ]);
        }

        if ($update) {
            $json_array = array(
                'status' => 'success',
                'message' => 'Orders Transit Inititated',
            );

            $response = $json_array;
            return json_encode($response);

        } else {

            $json_array = array(
                'status' => 'error',
                'message' => 'Failed to Transit Orders',
            );

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function duplicateOrder(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $merchant_id = $request->merchant_id;

        $orders = DB::table('orders')
            ->where('is_sender_merchant', 1)
            ->where('order_status', 'order_pending');

        if ($order_date != 'all') {

            if ($order_date == 'today') {

                $orders->whereDate('created_at', Carbon::today());

            } elseif ($order_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $orders->whereDate('created_at', '=', $custom_date);

            } elseif ($order_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));

                $orders->whereBetween('created_at', [$start_date, $end_date]);
            }

        }

        if ($merchant_id != 'all') {
            $orders->where('merchant_id', $merchant_id);
        }

        $orders = $orders->whereIn('receiver_phone', function ($query) {
            $query->select('receiver_phone')->from('orders')->groupBy('receiver_phone')->havingRaw('count(*) > 1');
        })->orderBy('receiver_phone')->get();

        $order_result = array();
        foreach ($orders as $order) {

            $date_created = date_create($order->created_at);
            $duplicate_orders = DB::table('orders')
                ->where('merchant_id', $merchant_id)
                ->where('is_sender_merchant', 1)
                ->where('receiver_phone', $order->receiver_phone)
                ->where('order_status', 'order_pending')
                ->whereDate('created_at', date_format($date_created, "Y-m-d"))
                ->latest()
                ->get();

            foreach ($duplicate_orders as $duplicate_order) {

                if (count($duplicate_orders) > 1) {
                    array_push($order_result, $duplicate_order);
                }

            }

        }

        return json_encode($order_result);

    }

    public function duplicateOrderGenerateExcel(Request $request)
    {

        $log_controller = new LogController();
        $log_controller->reportOrderGenerateLog($request->causer_id);

        $orders = json_decode($request->orders);
        return Excel::download(new ReportOrderExport($orders), 'duplicate-order-excel.xls');

    }

    public function duplicateOrderGeneratePdf(Request $request)
    {

        $causer_id = $request->causer_id;
        $log_controller = new LogController();
        $log_controller->reportOrderGenerateLog($request->causer_id);

        $pdfMerger = PDFMerger::init();
        $orders = json_decode($request->orders);
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
            $rider_phone = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if ($rider) {
                $rider_name = $rider->first_name . " " . $rider->last_name;
                $rider_phone = $rider->phone_number;
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

            $enable_returns_management_fee = 0;
            if ($order->is_sender_merchant == 1) {

                $merchant = DB::table('merchants')
                    ->where('id', $order->merchant_id)
                    ->first();
                if ($merchant) {
                    if ($merchant->enable_returns_management_fee == 1) {
                        $enable_returns_management_fee = 1;
                    };
                }
            }

            $json_array = array(
                'id' => $order->id,
                'order_no' => $order->order_no,
                'destination_type' => $order->destination_type,
                'inbound_rate_type' => $order->inbound_rate_type,
                'delivery_distance' => $order->delivery_distance,
                'is_sender_merchant' => $order->is_sender_merchant,
                'merchant_id' => $order->merchant_id,
                'returns_management_fee' => $enable_returns_management_fee,
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
                'status_reason' => $order->status_reason,
                'custom_reason' => $order->custom_reason,
                'payment_status' => $order->payment_status,
                'zone_id' => $order->zone_id,
                'rider_id' => $order->rider_id,
                'rider_name' => $rider_name,
                'rider_phone' => $rider_phone,
                'branch_id' => $order->branch_id,
                'branch_name' => $branch_name,
                'booking_date' => $order->booking_date,
                'delivery_date' => $order->delivery_date,
                'delivered_date' => $order->delivered_date,
                'scheduled_date' => $order->scheduled_date,
                'total_weight' => $order->total_weight,
                'admin_id' => $order->admin_id,
                'inventory' => $order->inventory,
                'admin_name' => $admin_name,
                'agent' => $order->agent,
                'agent_name' => $agent_name,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            );

            $order_items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            $data = [
                'order' => $json_array,
                'order_items' => $order_items,
            ];

            $file_name = str_replace("/", "-", $order->order_no);
            $file_name = "storage/pdf/" . $file_name . ".pdf";

            if ($order->merchant_id == '562d3960-4d04-11ec-a361-dd2ce8fdc183') {
                $pdf = PDF::loadView('admin.report.order-waybill-vital', $data, ['format' => 'A4-L']);
                $pdf->save($file_name);
            } else {
                $pdf = PDF::loadView('admin.report.order-waybill', $data, ['format' => 'A4-L']);
                $pdf->save($file_name);
            }
            $pdfMerger->addPDF($file_name, 'all');

            $pring_log_object = new WaybillPrintLog();
            $pring_log_object_created = $pring_log_object->create([
                'admin_id' => $causer_id,
                'order_id' => $order->id,
            ]);

        }

        $pdfMerger->merge();
        $file = new Filesystem;
        $file->cleanDirectory('storage/pdf');
        return $pdfMerger->save("order-waybills.pdf", "download");

    }

    public function followupOrder(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;

        $orders = DB::table('orders')
            ->where('is_sender_merchant', 1)
            ->where('order_status', 'order_pending');

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

        $orders = $orders->get();
        return json_encode($orders);

    }

    public function pendingOrder(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;

        $orders = DB::table('orders')
            ->where('is_sender_merchant', 1)
            ->where('updated_at', 'order_pending');

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

        $orders = $orders->get();
        return json_encode($orders);

    }

    public function followupOrderGenerateExcel(Request $request)
    {

        $log_controller = new LogController();
        $log_controller->reportOrderGenerateLog($request->causer_id);

        $orders = json_decode($request->orders);
        return Excel::download(new ReportOrderExport($orders), 'followup-order-excel.xls');

    }

    public function followupOrderGeneratePdf(Request $request)
    {

        $causer_id = $request->causer_id;
        $log_controller = new LogController();
        $log_controller->reportOrderGenerateLog($request->causer_id);

        $pdfMerger = PDFMerger::init();
        $orders = json_decode($request->orders);
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
            $rider_phone = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if ($rider) {
                $rider_name = $rider->first_name . " " . $rider->last_name;
                $rider_phone = $rider->phone_number;
            }

            // Get rider
            $rider_name = "";
            $rider_phone = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if ($rider) {
                $rider_name = $rider->first_name . " " . $rider->last_name;
                $rider_phone = $rider->phone_number;
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

            $enable_returns_management_fee = 0;
            if ($order->is_sender_merchant == 1) {

                $merchant = DB::table('merchants')
                    ->where('id', $order->merchant_id)
                    ->first();
                if ($merchant) {
                    if ($merchant->enable_returns_management_fee == 1) {
                        $enable_returns_management_fee = 1;
                    };
                }
            }

            $json_array = array(
                'id' => $order->id,
                'order_no' => $order->order_no,
                'destination_type' => $order->destination_type,
                'inbound_rate_type' => $order->inbound_rate_type,
                'delivery_distance' => $order->delivery_distance,
                'is_sender_merchant' => $order->is_sender_merchant,
                'merchant_id' => $order->merchant_id,
                'returns_management_fee' => $enable_returns_management_fee,
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
                'status_reason' => $order->status_reason,
                'custom_reason' => $order->custom_reason,
                'payment_status' => $order->payment_status,
                'zone_id' => $order->zone_id,
                'rider_id' => $order->rider_id,
                'rider_name' => $rider_name,
                'rider_phone' => $rider_phone,
                'branch_id' => $order->branch_id,
                'branch_name' => $branch_name,
                'booking_date' => $order->booking_date,
                'delivery_date' => $order->delivery_date,
                'delivered_date' => $order->delivered_date,
                'scheduled_date' => $order->scheduled_date,
                'total_weight' => $order->total_weight,
                'admin_id' => $order->admin_id,
                'inventory' => $order->inventory,
                'admin_name' => $admin_name,
                'agent' => $order->agent,
                'agent_name' => $agent_name,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            );

            $order_items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            $data = [
                'order' => $json_array,
                'order_items' => $order_items,
            ];

            $file_name = "storage/pdf/" . $order->order_no . ".pdf";
            if ($order->merchant_id == '562d3960-4d04-11ec-a361-dd2ce8fdc183') {
                $pdf = PDF::loadView('admin.report.order-waybill-vital', $data, ['format' => 'A4-L']);
                $pdf->save($file_name);
            } else {
                $pdf = PDF::loadView('admin.report.order-waybill', $data, ['format' => 'A4-L']);
                $pdf->save($file_name);
            }
            $pdfMerger->addPDF($file_name, 'all');

            $pring_log_object = new WaybillPrintLog();
            $pring_log_object_created = $pring_log_object->create([
                'admin_id' => $causer_id,
                'order_id' => $order->id,
            ]);

        }

        $pdfMerger->merge();
        $file = new Filesystem;
        $file->cleanDirectory('storage/pdf');
        return $pdfMerger->save("order-waybills.pdf", "download");

    }

    public function integrationEditOrder(Request $request)
    {

        $order_id = $request->order_id;
        $special_instruction = $request->special_instruction;
        $order_status = $request->order_status;
        $scheduled_date = $request->scheduled_date;
        $status_reason = $request->status_reason;
        $status_date = $request->status_date;
        $receiver_address = $request->receiver_address;
        $receiver_country = $request->receiver_country;
        $receiver_town = $request->receiver_town;

        $update = DB::table('orders')
            ->where('id', $order_id)
            ->update([
                'receiver_address' => $receiver_address,
                'receiver_country' => $receiver_country,
                'receiver_town' => $receiver_town,
                'special_instruction' => $special_instruction,
                'order_status' => $order_status,
                'scheduled_date' => $scheduled_date,
                'status_reason' => $status_reason,
                'status_date' => $status_date,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'status' => 'success',
                'message' => 'Order edited successfully',
            );

            $response = $json_array;
            return json_encode($response);

        } else {

            $json_array = array(
                'status' => 'error',
                'message' => 'Failed to edit order',
            );

            $response = $json_array;
            return json_encode($response);
        }
    }

    public function integrationOrderDetails(Request $request)
    {

        $order = DB::table('orders')
            ->where('order_no', $request->order_no)
            ->first();

        if ($order) {

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

            $json_array = array(
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
                'pickup_country' => $order->pickup_country,
                'pickup_town' => $order->pickup_town,
                'pickup_address' => $order->pickup_address,
                'return_amount' => $order->return_amount,
                'payment_method' => $order->payment_method,
                'cash_amount' => $order->cash_amount,
                'mpesa_amount' => $order->mpesa_amount,
                'cash_mpesa_amount' => $order->cash_mpesa_amount,
                'transaction_code' => $order->transaction_code,
                'booking_time' => $order->booking_time,
                'follow_up_date' => $order->follow_up_date,
                'special_instruction' => $order->special_instruction,
                'payment_type' => $order->payment_type,
                'upsell' => $order->upsell,
                'cash_on_delivery' => $order->cash_on_delivery,
                'cash_on_delivery_amount' => $order->cash_on_delivery_amount,
                'amount' => $order->amount,
                'service_type' => $order->service_type,
                'insurance' => $order->insurance,
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
            );

            $response = $json_array;
            return json_encode($response);

        } else {

            $json_array = array(
                'status' => 'error',
                'message' => 'No order found',
            );

            $response = $json_array;
            return json_encode($response);

        }
    }

    public function integrationOrderDetailsPhone(Request $request)
    {

        $orders = DB::table('orders')
            ->where('receiver_phone', $request->phone_number)
            ->latest()
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
                    'pickup_country' => $order->pickup_country,
                    'pickup_town' => $order->pickup_town,
                    'pickup_address' => $order->pickup_address,
                    'return_amount' => $order->return_amount,
                    'payment_method' => $order->payment_method,
                    'cash_amount' => $order->cash_amount,
                    'mpesa_amount' => $order->mpesa_amount,
                    'cash_mpesa_amount' => $order->cash_mpesa_amount,
                    'transaction_code' => $order->transaction_code,
                    'booking_time' => $order->booking_time,
                    'follow_up_date' => $order->follow_up_date,
                    'special_instruction' => $order->special_instruction,
                    'payment_type' => $order->payment_type,
                    'upsell' => $order->upsell,
                    'cash_on_delivery' => $order->cash_on_delivery,
                    'cash_on_delivery_amount' => $order->cash_on_delivery_amount,
                    'amount' => $order->amount,
                    'service_type' => $order->service_type,
                    'insurance' => $order->insurance,
                    'status_reason' => $order->status_reason,
                    'custom_reason' => $order->custom_reason,
                    'status_reason' => $order->status_reason,
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

    public function intergrationSms(Request $request)
    {

        $format_phone_number_util = new FormatPhoneNumberUtil();
        $phone_number = $format_phone_number_util->formatPhoneNumber($request->phone_number);
        $sms_message = $request->message;

        // Create an instance of SMSUtil
        $sms_util = new SMSUtil();

        // Send the SMS

        $sms_util->sendSMS($phone_number, $sms_message);
    }
}
