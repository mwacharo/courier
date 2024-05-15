<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $id = Auth::id();
        $logs = DB::table('activity_log')
            ->where('causer_id', $id)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();

        // Pending orders
        $pending_orders = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('status_date', Carbon::now()->year)
            ->where('deleted_at', null)
            ->count();

        // Pending orders
        $scheduled_orders = DB::table('orders')
            ->where('order_status', 'scheduled')
            ->where('deleted_at', null)
            ->where('status_date', Carbon::now()->year)
            ->count();

        // undispatched orders
        $undispatched_orders = DB::table('orders')
            ->where('order_status', 'undispatched')
            ->where('deleted_at', null)
            ->where('status_date', Carbon::now()->year)
            ->count();
        //dispatched orders
        $dispatched_orders = DB::table('orders')
            ->where('order_status', 'dispatched')
            ->where('deleted_at', null)
            ->where('status_date', Carbon::now()->year)
            ->count();
        // Delivered orders
        $delivered_orders = DB::table('orders')
            ->where('order_status', 'delivered')
            ->where('deleted_at', null)
            ->where('status_date', Carbon::now()->year)
            ->count();

        // in transit orders
        $intransit_orders = DB::table('orders')
            ->where('order_status', 'in_transit')
            ->where('deleted_at', null)
            ->where('status_date', Carbon::now()->year)
            ->count();

        // in transit orders
        $cancelled_orders = DB::table('orders')
            ->where('order_status', 'cancelled')
            ->where('status_date', Carbon::now()->year)
            ->where('deleted_at', null)
            ->count();

        // Total orders
        $total_orders = DB::table('orders')
            ->where('deleted_at', null)
            ->count();

        // Total orders
        $total_riders = DB::table('riders')
            ->where('deleted_at', null)
            ->count();

        // Merchant total
        $total_merchants = DB::table('merchants')
            ->where('deleted_at', null)
            ->count();

        // Inventories total
        $total_inventories = DB::table('inventories')
            ->where('deleted_at', null)
            ->sum('quantity');

        // Recent in-scan
        $inventory_histories = DB::table('inventory_histories')
            ->where('deleted_at', null)
            ->latest()
            ->get();

        $inventory_histories_result = array();
        foreach ($inventory_histories as $inventory_history) {

            $inventory = DB::table('inventories')
                ->where('id', $inventory_history->inventory_id)
                ->first();

            $merchant_name = "";
            $merchant = DB::table('merchants')
                ->where('id', $inventory->merchant_id)
                ->first();
            if ($merchant) {
                $merchant_name = $merchant->name;
            }

            $transaction_type = "";
            if ($inventory_history->transaction_type == 1) {
                $transaction_type = "INSCAN";
            } elseif ($inventory_history->transaction_type == 2) {
                $transaction_type = "OUTSCAN";
            }
            array_push($inventory_histories_result,
                array(
                    'id' => $inventory_history->id,
                    'inventory_id' => $inventory_history->id,
                    'sku' => $inventory->sku,
                    'merchant_id' => $inventory->merchant_id,
                    'merchant_name' => $merchant_name,
                    'name' => $inventory->name,
                    'description' => $inventory->description,
                    'image' => $inventory->image,
                    'quantity' => $inventory->quantity,
                    'balance' => $inventory_history->balance,
                    'low_count' => $inventory->low_count,
                    'amount' => $inventory->amount,
                    'transaction_type' => $transaction_type,
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                ));

        }

        $inventories = DB::table('inventories')
            ->where('deleted_at', null)
            ->latest()
            ->get();

        $low_stock_result = array();
        $low_stocks = DB::table('inventories')
            ->where('deleted_at', null)
            ->inRandomOrder()
            ->limit(20)
            ->get();

        foreach ($low_stocks as $low_stock) {

            if ($low_stock->quantity < $low_stock->low_count) {
                array_push($low_stock_result, $low_stock);
            }

        }

        $orders = DB::table('orders')
            ->where('deleted_at', null)
            ->latest()
            ->limit(5)
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
                    //'receiver_gender' => $order->receiver_gender,
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
                    //   'upsell' => $order->upsell,
                    'cash_on_delivery' => $order->cash_on_delivery,
                    'cash_on_delivery_amount' => $order->cash_on_delivery_amount,
                    'amount' => $order->amount,
                    'service_type' => $order->service_type,
                    'insurance' => $order->insurance,
                    'order_status' => $order->order_status,
                    'status_reason' => $order->status_reason,
                    'payment_status' => $order->payment_status,
                    'zone_id' => $order->zone_id,
                    'rider_id' => $order->rider_id,
                    'rider_name' => $rider_name,
                    'branch_id' => $order->branch_id,
                    'branch_name' => $branch_name,
                    'booking_date' => $order->booking_date,
                    'delivery_date' => $order->delivery_date,
                    'admin_id' => $order->admin_id,
                    'admin_name' => $admin_name,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ));

        }

        // Total
        $date = Carbon::now();
        $label6 = $date->format('F');
        $label5 = $date->subMonth()->format('F');
        $label4 = $date->subMonth(1)->format('F');
        $label3 = $date->subMonth(1)->format('F');
        $label2 = $date->subMonth(1)->format('F');
        $label1 = $date->subMonth(1)->format('F');

        // Pending orders
        $pending_1 = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Delivered orders
        $delivered_1 = DB::table('orders')
            ->where('order_status', 'delivered')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Cancelles orders
        $cancelled_1 = DB::table('orders')
            ->where('order_status', 'cancelled')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Total orders
        $total_1 = DB::table('orders')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Pending orders
        $pending_2 = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(1)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Delivered orders
        $delivered_2 = DB::table('orders')
            ->where('order_status', 'delivered')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(1)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Cancelles orders
        $cancelled_2 = DB::table('orders')
            ->where('order_status', 'cancelled')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(1)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Total orders
        $total_2 = DB::table('orders')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(1)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Pending orders
        $pending_3 = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(2)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Delivered orders
        $delivered_3 = DB::table('orders')
            ->where('order_status', 'delivered')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(2)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Cancelles orders
        $cancelled_3 = DB::table('orders')
            ->where('order_status', 'cancelled')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(2)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Total orders
        $total_3 = DB::table('orders')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(2)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Pending orders
        $pending_4 = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(3)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Delivered orders
        $delivered_4 = DB::table('orders')
            ->where('order_status', 'delivered')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(3)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Cancelled orders
        $cancelled_4 = DB::table('orders')
            ->where('order_status', 'cancelled')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(3)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Total orders
        $total_4 = DB::table('orders')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(3)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Pending orders
        $pending_5 = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(4)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Delivered orders
        $delivered_5 = DB::table('orders')
            ->where('order_status', 'delivered')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(4)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Cancelles orders
        $cancelled_5 = DB::table('orders')
            ->where('order_status', 'cancelled')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(4)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Total orders
        $total_5 = DB::table('orders')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(4)->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Pending orders
        $pending_6 = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(5)->month)

            ->count();

        // Delivered orders
        $delivered_6 = DB::table('orders')
            ->where('order_status', 'delivered')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(5)->month)

            ->count();

        // Cancelles orders
        $cancelled_6 = DB::table('orders')
            ->where('order_status', 'cancelled')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(5)->month)

            ->count();

        // Total orders
        $total_6 = DB::table('orders')
            ->where('deleted_at', null)
            ->whereMonth('created_at', Carbon::now()->subMonth(5)->month)

            ->count();

        $data = [
            'scheduled_orders' => $scheduled_orders,
            'pending_orders' => $pending_orders,
            'intransit_orders' => $intransit_orders,
            'dispatched_orders' => $dispatched_orders,
            'undispatched_orders' => $undispatched_orders,
            'delivered_orders' => $delivered_orders,
            'total_riders' => $total_riders,
            'total_orders' => $total_orders,
            'total_merchants' => $total_merchants,
            'total_inventories' => $total_inventories,
            'orders' => $order_result,
            'logs' => $logs,
            'inventories' => $inventories,
            'inventory_histories' => $inventory_histories_result,
            'low_stocks' => $low_stock_result,

            'label1' => $label1,
            'label2' => $label2,
            'label3' => $label3,
            'label4' => $label4,
            'label5' => $label5,
            'label6' => $label6,

            'pending_1' => $pending_1,
            'pending_2' => $pending_2,
            'pending_3' => $pending_3,
            'pending_4' => $pending_4,
            'pending_5' => $pending_5,
            'pending_6' => $pending_6,

            'cancelled_1' => $cancelled_1,
            'cancelled_2' => $cancelled_2,
            'cancelled_3' => $cancelled_3,
            'cancelled_4' => $cancelled_4,
            'cancelled_5' => $cancelled_5,
            'cancelled_6' => $cancelled_6,

            'delivered_1' => $delivered_1,
            'delivered_2' => $delivered_2,
            'delivered_3' => $delivered_3,
            'delivered_4' => $delivered_4,
            'delivered_5' => $delivered_5,
            'delivered_6' => $delivered_6,

            'total_1' => $total_1,
            'total_2' => $total_2,
            'total_3' => $total_3,
            'total_4' => $total_4,
            'total_5' => $total_5,
            'total_6' => $total_6,

            'chat' => 'home',
        ];

        return view('admin.home.index', $data);
    }
}
