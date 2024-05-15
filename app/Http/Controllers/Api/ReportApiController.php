<?php

namespace App\Http\Controllers\Api;

use App\Admin;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\ReportSkuExport;
use App\Exports\ReportOrderExport;
use App\Exports\ReportRiderExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportDeliveryExport;
use App\Exports\ReportMerchantExport;
use App\Exports\ReportIntransitExport;
use App\Exports\ReportInventoryExport;
use App\Exports\ReportAllMerchantExport;
use App\Exports\ReportAllIntransitExport;
use App\Exports\ReportRiderOutscanExport;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use App\Exports\ReportAllRiderOutscanExport;
use App\Http\Controllers\Admin\LogController;
use App\Exports\ReportMerchantRemittanceExport;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;

class ReportApiController extends Controller
{

    var $order_result = array();

    public function reportOrder(Request $request)
    {
        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $client_type = $request->client_type;
        $destination_type = $request->destination_type;
        $service_type = $request->service_type;
        $order_status = $request->order_status;
        $payment_status = $request->payment_status;

        $query = Order::query();

        if ($order_date != 'all') {
            if ($order_date == 'today') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($order_date == 'current_week') {
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($order_date == 'last_week') {
                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $query->whereBetween('created_at', [$start_week, $end_week]);
            } elseif ($order_date == 'current_month') {
                $query->whereMonth('created_at', Carbon::now()->month);
            } elseif ($order_date == 'current_year') {
                $query->whereYear('created_at', Carbon::now()->year);
            } elseif ($order_date == 'custom_date') {
                $custom_date = date("Y-m-d", strtotime($custom_date));
                $query->whereDate('created_at', '=', $custom_date);
            } elseif ($order_date == 'custom_range') {
                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));
                $query->whereBetween('created_at', [$start_date, $end_date]);
            }
        }

        if ($client_type != 'all') {
            $query->where('is_sender_merchant', $client_type == 'merchant');
        }

        if ($destination_type != 'all') {
            $query->where('destination_type', $destination_type == 'inbound' ? 1 : 2);
        }

        if ($service_type != 'all') {
            $serviceTypes = [
                'same_day_delivery' => 1,
                'scheduled' => 2,
                'overnight' => 3,
                'pickup' => 4
            ];
            $query->where('service_type', $serviceTypes[$service_type]);
        }

        if ($order_status != 'all') {
            $query->where('order_status', $order_status);
        }

        if ($payment_status != 'all') {
            $query->where('payment_status', $payment_status == 'pending' ? 0 : 1);
        }

        $orders = $query->select('order_no','receiver_name','receiver_email','receiver_address','receiver_town','receiver_phone','receiver_phone_alternative')->get();



        return json_encode($orders);
    }


    public function reportOrderGenerate(Request $request)
    {



        $log_controller = new LogController();
        $log_controller->reportOrderGenerateLog($request->causer_id);


        $orders = json_decode($request->orders);



        return Excel::download(new ReportOrderExport($orders), 'orders.csv');

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
            $orders->where('order_status', $order_status);
        }

        if ($payment_status != 'all') {

            if ($payment_status == 'pending') {
                $orders->where('payment_status', 0);
            } else if ($payment_status == 'paid') {
                $orders->where('payment_status', 1);
            }
        }

        $orders = $orders->get();
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
                $admin_name = $admin->first_name." ".$admin->last_name;
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

    public function searchFollowOrder(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $merchant_id = $request->merchant_id;
        $recipient_name = $request->recipient_name;
        $order_status = $request->order_status;
        $payment_status = $request->payment_status;
        $order_no = $request->order_no;
        $town_id = $request->town_id;
        $destination_type = $request->destination_type;

        $orders = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('is_sender_merchant', 1);

        if ($order_no != '') {
            $order_no = strtoupper($order_no);
            $orders->where('order_no', 'LIKE', "%{$order_no}%");
        }

        if ($merchant_id != 'all') {
            $orders->where('merchant_id', $merchant_id);
        }

        if ($recipient_name != '') {
            $recipient_name = strtoupper($recipient_name);
            $orders->where('receiver_name', 'LIKE', "%{$recipient_name}%");
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
            } else if ($order_status == 'dispatched') {
                $orders->where('order_status', 'dispatched');
            } else if ($order_status == 'overnight') {
                $orders->where('delivery_pending', 'delivery_pending');
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

        $orders = $orders->limit(500)->get();
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

            $print_count = DB::table('waybill_print_logs')
                ->where('order_id', $order->id)
                ->count();

            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            $print_count_log = 0;
            $order_status = $order->order_status;
            if ($order_status == 'scheduled') {

                $print_count_log = DB::table('waybill_print_logs')
                    ->where('order_id', $order->id)
                    ->whereDate('created_at', Carbon::today())
                    ->count();
            }

            if ($print_count_log == 0) {

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
                        'print_count' => $print_count,
                        'items' => $items,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ));
            }

        }

        return json_encode($order_result);

    }

    public function searchPendingOrder(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $merchant_id = $request->merchant_id;
        $recipient_name = $request->recipient_name;
        $order_status = $request->order_status;
        $payment_status = $request->payment_status;
        $order_no = $request->order_no;
        $town_id = $request->town_id;
        $destination_type = $request->destination_type;

        $orders = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('status_date', null)
            ->where('is_sender_merchant', 1);

        if ($order_no != '') {
            $order_no = strtoupper($order_no);
            $orders->where('order_no', 'LIKE', "%{$order_no}%");
        }

        if ($merchant_id != 'all') {
            $orders->where('merchant_id', $merchant_id);
        }

        if ($recipient_name != '') {
            $recipient_name = strtoupper($recipient_name);
            $orders->where('receiver_name', 'LIKE', "%{$recipient_name}%");
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
            } else if ($order_status == 'dispatched') {
                $orders->where('order_status', 'dispatched');
            } else if ($order_status == 'overnight') {
                $orders->where('delivery_pending', 'delivery_pending');
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

        $orders = $orders->limit(500)->get();
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

            $print_count = DB::table('waybill_print_logs')
                ->where('order_id', $order->id)
                ->count();

            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            $print_count_log = 0;
            $order_status = $order->order_status;
            if ($order_status == 'scheduled') {

                $print_count_log = DB::table('waybill_print_logs')
                    ->where('order_id', $order->id)
                    ->whereDate('created_at', Carbon::today())
                    ->count();
            }

            if ($print_count_log == 0) {

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
                        'print_count' => $print_count,
                        'items' => $items,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ));
            }

        }

        return json_encode($order_result);

    }


    public function reportRider(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $rider_id = $request->rider_id;
        $destination_type = $request->destination_type;
        $service_type = $request->service_type;
        $order_status = $request->order_status;
        $payment_status = $request->payment_status;

        $orders = DB::table('orders')
            ->where('rider_id', $rider_id);

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

            if ($destination_type == 'inbound') {
                $orders->where('destination_type', 1);
            } else if ($destination_type == 'outbound') {
                $orders->where('destination_type', 2);
            }
        }

        if ($service_type != 'all') {

            if ($service_type == 'same_day_delivery') {
                $orders->where('service_type', 1);
            } else if ($service_type == 'scheduled') {
                $orders->where('service_type', 2);
            } else if ($service_type == 'overnight') {
                $orders->where('service_type', 3);
            } else if ($service_type == 'pickup') {
                $orders->where('service_type', 4);
            }
        }

        if ($order_status != 'all') {

            if ($order_status == 'order_pending') {
                $orders->where('order_status', 'order_pending');
            } else if ($order_status == 'dispatched') {
                $orders->where('order_status', 'dispatched');
            } else if ($order_status == 'overnight') {
                $orders->where('delivery_pending', 'delivery_pending');
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

        $orders = $orders->get();
        return json_encode($orders);

    }

    public function reportRiderGenerate(Request $request)
    {

        $log_controller = new LogController();
        $log_controller->reportRiderGenerateLog($request->causer_id);

        $rider_id = $request->rider_id;
        $orders = json_decode($request->orders);
        return Excel::download(new ReportRiderExport($orders, $rider_id), 'rider-report-excel.xls');

    }

    public function reportRiderOutscan(Request $request)
    {
        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $rider_id = $request->rider_id;

        $order_scans = DB::table('order_scans')
            ->where('scan_type', 2);

        if ($rider_id != 'all') {
            $order_scans->where('rider_id', $rider_id);
        }


        if ($order_date !== 'all') {
            $now = Carbon::now();
            switch ($order_date) {
                case 'today':
                    $order_scans->whereDate('created_at', $now->toDateString());
                    break;
                case 'current_week':
                    $order_scans->whereBetween('created_at', [
                        $now->startOfWeek()->toDateTimeString(),
                        $now->endOfWeek()->toDateTimeString()
                    ]);
                    break;
                case 'last_week':
                    $previous_week = $now->copy()->subWeek();
                    $order_scans->whereBetween('created_at', [
                        $previous_week->startOfWeek()->toDateTimeString(),
                        $previous_week->endOfWeek()->toDateTimeString()
                    ]);
                    break;
                case 'current_month':
                    $order_scans->whereMonth('created_at', $now->month);
                    break;
                case 'current_year':
                    $order_scans->whereYear('created_at', $now->year);
                    break;
                case 'custom_date':
                    $custom_date = Carbon::parse($custom_date);
                    $order_scans->whereDate('created_at', $custom_date->toDateString());
                    break;
                case 'custom_range':
                    $start_date = Carbon::parse($custom_start_date);
                    $end_date = Carbon::parse($custom_end_date);
                    $order_scans->whereBetween('created_at', [
                        $start_date->toDateString(),
                        $end_date->toDateString()
                    ]);
                    break;
            }
        }


        $order_scans = $order_scans->get();


        $order_result = array();
        foreach ($order_scans as $order_scan) {

            $order = DB::table('orders')
                ->where('id', $order_scan->order_id)
                ->first();

                if(!$order) {
                    Log::debug('********');
                    Log::debug($order_scan->order_id);
                    Log::debug('********');
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
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if ($rider) {
                $rider_name = $rider->first_name . " " . $rider->last_name;
            }


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
                    'sender_town' => $order->sender_town,
                    'receiver_name' => $order->receiver_name,
                    'receiver_address' => $order->receiver_address,
                    'receiver_gender' => $order->receiver_gender,
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
                    'order_status' => $order->order_status,
                    'status_reason' => $order->status_reason,
                    'custom_reason' => $order->custom_reason,
                    'payment_status' => $order->payment_status,
                    'zone_id' => $order->zone_id,
                    'rider_id' => $order->rider_id,
                    'rider_name' => $rider_name,
                    'branch_id' => $order->branch_id,
                    'booking_date' => $order->booking_date,
                    'status_date' => $order->status_date,
                    'delivery_date' => $order->delivery_date,
                    'delivered_date' => $order->delivered_date,
                    'scheduled_date' => $order->scheduled_date,
                    'items' => $items,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ));
        }

        return json_encode($order_result);

    }
    public function reportRiderIntransit(Request $request)
    {
        $order_date = $request->order_date;
        $destination_type = $request->destination_type;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $rider_id = $request->rider_id;

        $orders = DB::table('orders')
            ->where('order_status', 'in_transit');

        if($order_date != 'all'){

            switch ($order_date) {
                case 'today':
                    $orders->whereDate('status_date', Carbon::today());
                    break;

                case 'current_week':
                    $orders->whereBetween('status_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;

                case 'last_week':
                    $previous_week = strtotime("-1 week +1 day");
                    $start_week = strtotime("last sunday midnight", $previous_week);
                    $end_week = strtotime("next saturday", $start_week);
                    $start_week = date("Y-m-d", $start_week);
                    $end_week = date("Y-m-d", $end_week);
                    $orders->whereBetween('status_date', [$start_week, $end_week]);
                    break;

                case 'current_month':
                    $orders->whereMonth('status_date', Carbon::now()->month);
                    break;

                case 'current_year':
                    $orders->whereYear('status_date', Carbon::now()->year);
                    break;

                case 'custom_date':
                    $custom_date = date("Y-m-d", strtotime($custom_date));
                    $orders->whereDate('status_date', '=', $custom_date);
                    break;

                case 'custom_range':
                    $start_date = date("Y-m-d", strtotime($custom_start_date));
                    $end_date = date("Y-m-d", strtotime($custom_end_date));
                    $orders->whereBetween('status_date', [$start_date, $end_date]);
                    break;

                default:
                    // do nothing
                    break;
            }


        }

        if ($destination_type != 'all') {

            if ($destination_type == 'inbound') {
                $orders->where('destination_type', 1);
            } else if ($destination_type == 'outbound') {
                $orders->where('destination_type', 2);
            }
            else if ($destination_type == 'unspecified') {
                $orders->where('destination_type', 0);
            }
        }


        if ($rider_id != 'all') {
            $orders->where('rider_id', $rider_id);
        }

        $orders = $orders->get();

        $order_result = [];
        foreach ($orders as $order) {



            // Receiver town
            $receiver_town_name = "";
            $town = DB::table('towns')
                ->where('id', $order->id)
                ->first();
            if ($town) {
                $receiver_town_name = $town->name;
            }

            // Get rider
            $rider_name = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if ($rider) {
                $rider_name = $rider->first_name . " " . $rider->last_name;
            }


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
                    'sender_town' => $order->sender_town,
                    'receiver_name' => $order->receiver_name,
                    'receiver_address' => $order->receiver_address,
                    'receiver_gender' => $order->receiver_gender,
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
                    'order_status' => $order->order_status,
                    'status_reason' => $order->status_reason,
                    'custom_reason' => $order->custom_reason,
                    'payment_status' => $order->payment_status,
                    'zone_id' => $order->zone_id,
                    'rider_id' => $order->rider_id,
                    'rider_name' => $rider_name,
                    'branch_id' => $order->branch_id,
                    'booking_date' => $order->booking_date,
                    'status_date' => $order->status_date,
                    'delivery_date' => $order->delivery_date,
                    'delivered_date' => $order->delivered_date,
                    'scheduled_date' => $order->scheduled_date,
                    'items' => $items,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ));
        }

        return json_encode($order_result);
    }

    public function reportRiderOutscanGenerate(Request $request)
    {
        $log_controller = new LogController();
        $log_controller->reportMerchantGenerateLog($request->causer_id);


        $rider_id = $request->rider_id;



        if($rider_id == 'all')
        {


            $orders = json_decode($request->orders);
          return Excel::download(new ReportAllRiderOutscanExport($orders, $rider_id), 'rider-report-excel.xls');

        }

        else{

            $orders = json_decode($request->orders);
            return Excel::download(new ReportRiderOutscanExport($orders, $rider_id), 'rider-report-excel.xls');

        }



    }

    public function reportRiderIntransitGenerate(Request $request)
    {

        $log_controller = new LogController();
        $log_controller->reportMerchantGenerateLog($request->causer_id);


        $rider_id = $request->rider_id;



        if($rider_id == 'all')
        {
            $orders = json_decode($request->orders);
          return Excel::download(new ReportAllIntransitExport($orders), 'intransit-report-excel.xls');

        }

        else{


            $orders = json_decode($request->orders);
            return Excel::download(new ReportIntransitExport($orders, $rider_id), 'intransit-report-excel.xls');

        }

    }

    public function reportRiderDispatchedGenerate(Request $request)
    {

        $log_controller = new LogController();
        $log_controller->reportRiderGenerateLog($request->causer_id);



        $rider_id = $request->rider_id;
        $orders = json_decode($request->orders);
        return Excel::download(new ReportIntransitExport($orders, $rider_id), 'rider-report-excel.xls');
    }

    public function reportRiderOutscanPdfGenerate(Request $request)
    {

        $log_controller = new LogController();
        $log_controller->reportRiderGenerateLog($request->causer_id);

        $rider_id = $request->rider_id;
        $rider = DB::table('riders')
            ->where('id', $rider_id)
            ->where('deleted_at', null)
            ->first();

        $orders = json_decode($request->orders);
        $data = [
            'orders' => $orders,
            'rider' => $rider,
            'date' => $request->date
        ];

        $pdf = PDF::loadView('admin.report.rider-report-outscan-pdf', $data);
        return $pdf->stream('admin.report.rider-report-outscan-pdf');

    }

    public function reportDelivery(Request $request)
    {

        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $destination_type = $request->destination_type;
        $order_status = $request->order_status;
        $merchant_id = $request->merchant_id;

        $orders = DB::table('orders');

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

        if ($merchant_id != 'all') {
            $orders->where('merchant_id', $merchant_id);
        }

        if ($destination_type != 'all') {

            if ($destination_type == 'inbound') {
                $orders->where('destination_type', 1);
            } else if ($destination_type == 'outbound') {
                $orders->where('destination_type', 2);
            }
        }


        if ($order_status != 'all') {

            if ($order_status == 'delivery_pending') {
                $orders->where('order_status', 'delivery_pending');
            } else if ($order_status == 'delivered') {
                $orders->where('order_status', 'delivered');
            } else if ($order_status == 'returned') {
                $orders->where('order_status', 'returned');
            }
        }

        $orders = $orders->get();
        return json_encode($orders);

    }

    public function reportDeliveryGenerate(Request $request)
    {

        $log_controller = new LogController();
        $log_controller->reportDeliveryGenerateLog($request->causer_id);

        $orders = json_decode($request->orders);
        return Excel::download(new ReportDeliveryExport($orders), 'delivery-report-excel.xls');

    }

    public function reportInventory(Request $request)
    {

        $inventory_date = $request->inventory_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $merchant_id = $request->merchant_id;
        $low_count = $request->low_count;

        $inventories = DB::table('inventories');

        if ($inventory_date != 'all') {

            if ($inventory_date == 'today') {

                $inventories->whereDate('created_at', Carbon::today());

            } elseif ($inventory_date == 'current_week') {

                $inventories->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            } elseif ($inventory_date == 'last_week') {

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $inventories->whereBetween('created_at', [$start_week, $end_week]);

            } elseif ($inventory_date == 'current_month') {

                $inventories->whereMonth('created_at', Carbon::now()->month);

            } elseif ($inventory_date == 'current_year') {

                $inventories->whereYear('created_at', Carbon::now()->year);

            } elseif ($inventory_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $inventories -> whereDate('created_at', '=', $custom_date);

            } elseif ($inventory_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));

                $inventories->whereBetween('created_at', [$start_date, $end_date]);
            }

        }

        if ($merchant_id != '') {
            $inventories->where('merchant_id', $merchant_id);
        }

        if ($low_count != '') {
            $inventories->where('low_count', '>', $low_count);
        }

        $inventories = $inventories->get();
        $inventory_result = array();
        foreach ($inventories as $inventory) {

            // Get merchant
            $merchant_name = "";
            $merchant = DB::table('merchants')
                ->where('id', $inventory->merchant_id)
                ->first();
            if ($merchant) {
                $merchant_name = $merchant->name;
            }

            array_push($inventory_result,
                array(
                    'id' => $inventory->id,
                    'sku' => $inventory->sku,
                    'merchant_id' => $inventory->merchant_id,
                    'merchant_name' => $merchant_name,
                    'name' => $inventory->name,
                    'description' => $inventory->description,
                    'image' => $inventory->image,
                    'quantity' => $inventory->quantity,
                    'low_count' => $inventory->low_count,
                    'amount' => $inventory->amount,
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                ));

        }

        return json_encode($inventories);

    }

    public function reportInventoryGenerate(Request $request)
    {

        $log_controller = new LogController();
        $log_controller->reportInventoryGenerateLog($request->causer_id);

        $inventories = json_decode($request->inventories);
        return Excel::download(new ReportInventoryExport($inventories), 'inventory-report-excel.xls');

    }

    public function reportSku(Request $request)
    {

        $merchant_id = $request->merchant_id;

        $inventories = DB::table('inventories');

        if ($merchant_id != '') {
            $inventories->where('merchant_id', $merchant_id);
        }

        $inventories = $inventories->get();
        $inventory_result = array();
        foreach ($inventories as $inventory) {

            // Get merchant
            $merchant_name = "";
            $merchant = DB::table('merchants')
                ->where('id', $inventory->merchant_id)
                ->first();
            if ($merchant) {
                $merchant_name = $merchant->name;
            }

            array_push($inventory_result,
                array(
                    'id' => $inventory->id,
                    'sku' => $inventory->sku,
                    'merchant_id' => $inventory->merchant_id,
                    'merchant_name' => $merchant_name,
                    'name' => $inventory->name,
                    'description' => $inventory->description,
                    'image' => $inventory->image,
                    'quantity' => $inventory->quantity,
                    'low_count' => $inventory->low_count,
                    'amount' => $inventory->amount,
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                ));

        }

        return json_encode($inventory_result);

    }

    public function reportSkuGenerate(Request $request)
    {

        $log_controller = new LogController();
        $log_controller->reportInventoryGenerateLog($request->causer_id);

        $inventories = json_decode($request->inventories);
        return Excel::download(new ReportSkuExport($inventories), 'sku-report-excel.xls');

    }

    public function reportMerchandiseRemittance(Request $request)
    {

        $merchant_id = $request->id;
        $previous_week = strtotime("-1 week +1 day");
        $start_week = strtotime("last sunday midnight", $previous_week);
        $end_week = strtotime("next saturday", $start_week);
        $start_week = date("Y-m-d", $start_week);
        $end_week = date("Y-m-d", $end_week);

        $orders = DB::table('orders')
            ->where('merchant_id', $merchant_id)
            ->whereBetween('created_at', [$start_week, $end_week])
            ->where('deleted_at', null)
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

            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->where('inventory_product', 1)
                ->get();

            foreach ($items as $item) {

                if ($item->price == 0) {

                    $unit_price = 0;
                    $inventory = DB::table('inventories')
                        ->where('id', $item->inventory_id)
                        ->first();
                    if ($inventory) {
                        $unit_price = $inventory->amount;
                    }

                } else {
                    $unit_price = $item->price;
                }

                array_push($order_result,
                    array(
                        'id' => $order->id,
                        'order_no' => $order->order_no,
                        'destination_type' => $order->destination_type,
                        'inbound_rate_type' => $order->inbound_rate_type,
                        'delivery_distance' => $order->delivery_distance,
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
                        'item_name' => $item->description,
                        'item_weight' => $item->weight,
                        'unit_price' => $unit_price,
                        'quantity' => $item->quantity,
                        'total_price' => $item->quantity * $unit_price,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ));
            }

        }

        return Excel::download(new ReportMerchantRemittanceExport($order_result, $merchant_id), 'merchant-remittance-report-excel.xls');
    }

    public function reportMerchantRemittance(Request $request)
    {

        $merchant_id = $request->merchant_id;
        $order_date = $request->order_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;

        $query = Order::query()
            ->where('merchant_id', $merchant_id)
            ->whereIn('order_status', ['delivered', 'returned', 'cancelled'])
            ->where('deleted_at', null);

        if ($order_date != 'all') {

            if ($order_date == 'today') {

                $query->whereDate('status_date', Carbon::today());

            } elseif ($order_date == 'current_week') {

                $query->whereBetween('status_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            
            } elseif ($order_date == 'last_week') {

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $query->whereBetween('status_date', [$start_week, $end_week]);

            } elseif ($order_date == 'current_month') {

                $query->whereMonth('status_date', Carbon::now()->month);

            } elseif ($order_date == 'current_year') {

                $query->whereYear('status_date', Carbon::now()->year);

            } elseif ($order_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $query->whereDate('status_date', '=activity_log', $custom_date);

            } elseif ($order_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));
                $query->whereDate('status_date', '>=', $start_date);
                $query->whereDate('status_date', '<=', $end_date);

            }

        }

        $order_result = array();
        $orders = $query->orderBy('status_date', 'DESC')->get();

        foreach ($orders as $order) {

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

            $item = DB::table('order_items')
                ->where('order_id', $order->id)
                ->first();

            if ($item) {

                if ($item->price == 0) {

                    $unit_price = 0;
                    $inventory = DB::table('inventories')
                        ->where('id', $item->inventory_id)
                        ->first();
                    if ($inventory) {
                        $unit_price = $inventory->amount;
                    }

                } else {
                    $unit_price = $item->price;
                }

                array_push($order_result,
                    array(
                        'id' => $order->id,
                        'order_no' => $order->order_no,
                        'destination_type' => $order->destination_type,
                        'inbound_rate_type' => $order->inbound_rate_type,
                        'delivery_distance' => $order->delivery_distance,
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
                        'item_name' => $item->description,
                        'item_weight' => $item->weight,
                        'unit_price' => $unit_price,
                        'quantity' => $item->quantity,
                        'total_price' => $item->quantity * $unit_price,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ));
            }

        }

        return json_encode($order_result);

    }

    public function reportMerchantRemittanceGenerate(Request $request)
    {

        $merchant_id = $request->merchant_id;
        $order_result = json_decode($request->orders);
        return Excel::download(new ReportMerchantRemittanceExport($order_result, $merchant_id), 'merchant-remittance-report-excel.xls');

    }

    public function reportMerchant(Request $request)
    {

        $order_date = $request->order_date;
        $upload_date = $request->upload_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $merchant_id = $request->merchant_id;
        $destination_type = $request->destination_type;
        $service_type = $request->service_type;
        $order_status = $request->order_status;
        $payment_status = $request->payment_status;


        $query = Order::query()
        ->with('order_items') // Eager load the order items relationship
        ->where('deleted_at', null);


        if($merchant_id != 'all'){
            $query->where('merchant_id', $merchant_id);
        }


        if ($upload_date != 'all') {

            if ($upload_date == 'today') {

                $query->whereDate('created_at', Carbon::today());

            } elseif ($upload_date == 'current_week') {

                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            } elseif ($upload_date == 'last_week') {

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $query->whereBetween('created_at', [$start_week, $end_week]);

            } elseif ($upload_date == 'current_month') {

                $query->whereMonth('created_at', Carbon::now()->month);

            } elseif ($upload_date == 'current_year') {

                $query->whereYear('created_at', Carbon::now()->year);

            } elseif ($upload_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $query->whereDate('created_at', '=', $custom_date);

            } elseif ($upload_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));
                $query->whereDate('created_at', '>=', $start_date);
                $query->whereDate('created_at', '<=', $end_date);

            }

        }

        if ($order_date != 'all') {

            if ($order_date == 'today') {

                $query->whereDate('status_date', Carbon::today());

            } elseif ($order_date == 'current_week') {

                $query->whereBetween('status_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            } elseif ($order_date == 'last_week') {

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday", $start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $query->whereBetween('status_date', [$start_week, $end_week]);

            } elseif ($order_date == 'current_month') {

                $query->whereMonth('status_date', Carbon::now()->month);

            } elseif ($order_date == 'current_year') {

                $query->whereYear('status_date', Carbon::now()->year);

            } elseif ($order_date == 'custom_date') {

                $custom_date = date("Y-m-d", strtotime($custom_date));
                $query->whereDate('status_date', '=', $custom_date);

            } elseif ($order_date == 'custom_range') {

                $start_date = date("Y-m-d", strtotime($custom_start_date));
                $end_date = date("Y-m-d", strtotime($custom_end_date));
                $query->whereDate('status_date', '>=', $start_date);
                $query->whereDate('status_date', '<=', $end_date);

            }

        }

        if ($destination_type != 'all') {

            if ($destination_type == 'inbound') {
                $query->where('destination_type', 1);
            } else if ($destination_type == 'outbound') {
                $query->where('destination_type', 2);
            }
        }

        if ($service_type != 'all') {

            if ($service_type == 'same_day_delivery') {
                $query->where('service_type', 1);
            } else if ($service_type == 'scheduled') {
                $query->where('service_type', 2);
            } else if ($service_type == 'overnight') {
                $query->where('service_type', 3);
            } else if ($service_type == 'pickup') {
                $query->where('service_type', 4);
            }
        }

        if ($order_status != 'all') {
            $query->where('order_status', $order_status);

        }

        if ($payment_status != 'all') {

            if ($payment_status == 'pending') {
                $query->where('payment_status', 0);
            } else if ($payment_status == 'paid') {
                $query->where('payment_status', 1);
            }
        }
        $orders = $query->orderBy('status_date', 'DESC')->get();


        return json_encode($orders);

    }

    public function reportMerchantGenerate(Request $request)
    {
        // $log_controller = new LogController();
        // $log_controller->reportMerchantGenerateLog($request->causer_id);


        $merchant_id = $request->merchant_id;
        if($merchant_id == 'all')
        {

          $orders = json_decode($request->orders);
          return Excel::download(new ReportAllMerchantExport($orders, $merchant_id), 'merchant-multiple-report-excel.xls');

        }

        else{

            $orders = json_decode($request->orders);
             return Excel::download(new ReportMerchantExport($orders, $merchant_id), 'merchant-report-excel.xls');

        }


    }



    public function adminActivity(Request $request)
    {
        $admin_id = $request->admin_id;
        $activity_date = $request->activity_date;
        $department_id = $request->department_id;
        $status = $request->status;

        $query = Admin::query()->where('deleted_at', null);

        if ($admin_id !== 'all') {
            $query->where('id', $admin_id);
        }

        if ($activity_date !== 'all') {
            $query->whereDate('login_date', $activity_date);
        }

        if ($department_id !== 'all') {
            $query->where('role', $department_id);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $activities = $query->get();

        // Format the last_login and login_time properties using Carbon
        $formattedActivities = $activities->map(function ($activity) {
            $activity->last_login = $activity->last_login ? Carbon::parse($activity->last_login)->diffForHumans() : NULL;
            $activity->login_time = $activity->login_time ? Carbon::parse($activity->login_time)->diffForHumans() : NULL;

            // Map department_id to department name
            if ($activity->role === 1) {
                $activity->role = 'Super admin';
            } elseif ($activity->role === 2) {
                $activity->role = 'Administrator';
            } elseif ($activity->role === 3) {
                $activity->role = 'Operations';
            }elseif ($activity->role === 4) {
                $activity->role = 'call center';
            }elseif ($activity->role === 5) {
                $activity->role = 'Finance';
            }elseif ($activity->role === 6) {
                $activity->role = 'IT';
            }elseif ($activity->role === 7) {
                $activity->role = 'Transport';
            }


            return $activity;
        });

        return response()->json($formattedActivities);
    }






}
