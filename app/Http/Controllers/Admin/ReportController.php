<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportMerchantRemittanceExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function orderReport()
    {
        return view('admin.report.order');
    }

    public function merchantReport()
    {
        return view('admin.report.merchant-report');
    }

    public function riderReport()
    {
        return view('admin.report.rider-report');
    }

    public function deliveryReport()
    {
        return view('admin.report.delivery-report');
    }

    public function inventoryReport()
    {
        return view('admin.report.inventory-report');
    }

    public function skuReport()
    {
        return view('admin.report.sku-report');
    }

    public function riderOutscan()
    {
        return view('admin.report.rider-report-outscan');
    }

    public function riderIntransit()
    {
        return view('admin.report.rider-report-intransit');
    }

    public function merchantRemittanceReport()
    {
        $merchants = DB::table('merchants')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get();

        $merchant_result = array();
        foreach ($merchants as $merchant){

            // Get country
            $country_name = "";
            $country = DB::table('countries')
                ->where('id', $merchant->country_id)
                ->first();
            if($country){
                $country_name = $country->name;
            }

            // Get town
            $town_name = "";
            $town = DB::table('towns')
                ->where('id', $merchant->town_id)
                ->first();
            if($town){
                $town_name = $town->name;
            }

            array_push($merchant_result,
                array(
                    'id' => $merchant->id,
                    'name' => $merchant->name,
                    'address' => $merchant->address,
                    'phone_number' => $merchant->phone_number,
                    'email' => $merchant->email,
                    'enable_cash_on_delivery_fee' => $merchant->enable_cash_on_delivery_fee,
                    'cash_on_delivery_fee' => $merchant->cash_on_delivery_fee,
                    'enable_delivery_fee_nairobi' => $merchant->enable_delivery_fee_nairobi,
                    'delivery_fee_nairobi' => $merchant->delivery_fee_nairobi,
                    'enable_delivery_fee_outbound' => $merchant->enable_delivery_fee_outbound,
                    'delivery_fee_outbound' => $merchant->delivery_fee_outbound,
                    'enable_returns_management_fee' => $merchant->enable_returns_management_fee,
                    'enable_warehousing_fee' => $merchant->enable_warehousing_fee,
                    'warehousing_fee' => $merchant->warehousing_fee,
                    'enable_packaging_fee' => $merchant->enable_packaging_fee,
                    'packaging_fee' => $merchant->packaging_fee,
                    'enable_call_centre_fee' => $merchant->enable_call_centre_fee,
                    'call_centre_fee' => $merchant->call_centre_fee,
                    'enable_label_printing_fee' => $merchant->enable_label_printing_fee,
                    'label_printing_fee' => $merchant->label_printing_fee,
                    'contract' => $merchant->contract,
                    'enabled' => $merchant->enabled,
                    'country_id' => $merchant->country_id,
                    'country_name' => $country_name,
                    'town_id' => $merchant->town_id,
                    'town_name' => $town_name,
                    'created_at' => $merchant->created_at,
                    'google_sheet' => $merchant->google_sheet,
                    'updated_at' => $merchant->updated_at,
                ));

        }

        $data = [
            'merchants' => $merchant_result
        ];

        return view('admin.report.merchant-remittance', $data);
    }

    public function reportMerchandiseRemittance(Request $request){

        $merchant_id = $request->id;
        $previous_week = strtotime("-1 week +1 day");
        $start_week = strtotime("last sunday midnight", $previous_week);
        $end_week = strtotime("next saturday",$start_week);
        $start_week = date("Y-m-d", $start_week);
        $end_week = date("Y-m-d", $end_week);

        $orders = DB::table('orders')
            ->where('merchant_id', $merchant_id)
            ->whereBetween('created_at', [$start_week, $end_week])
            ->where('deleted_at', null)
            ->latest()
            ->get();

        $order_result = array();
        foreach ($orders as $order){

            // Sender country
            $sender_country_name = "";
            $country = DB::table('countries')
                ->where('id', $order->sender_country)
                ->first();
            if($country){
                $sender_country_name = $country->name;
            }

            // Sender town
            $sender_town_name = "";
            $town = DB::table('towns')
                ->where('id', $order->sender_town)
                ->first();
            if($town){
                $sender_town_name = $town->name;
            }

            // Receiver country
            $receiver_country_name = "";
            $country = DB::table('countries')
                ->where('id', $order->receiver_country)
                ->first();
            if($country){
                $receiver_country_name = $country->name;
            }

            // Receiver town
            $receiver_town_name = "";
            $town = DB::table('towns')
                ->where('id', $order->receiver_town)
                ->first();
            if($town){
                $receiver_town_name = $town->name;
            }

            // Get branch
            $branch_name = "";
            $branch = DB::table('branches')
                ->where('id', $order->branch_id)
                ->first();
            if($branch){
                $branch_name = $branch->name;
            }

            // Get rider
            $rider_name = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if($rider){
                $rider_name = $rider->first_name ." ".$rider->last_name;
            }

            // Get admin
            $admin_name = "";
            $admin = DB::table('admins')
                ->where('id', $order->admin_id)
                ->first();
            if($admin){
                $admin_name = $admin->name;
            }

            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->where('inventory_product', 1)
                ->get();

            foreach ($items as $item){

                if($item->price == 0){

                    $unit_price = 0;
                    $inventory = DB::table('inventories')
                        ->where('id', $item->inventory_id)
                        ->first();
                    if($inventory){
                        $unit_price = $inventory->amount;
                    }

                }else{
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
                        'unit_price' => $unit_price,
                        'quantity' => $item->quantity,
                        'total_price' => $item->quantity * $unit_price,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ));
            }

        }

        $file_name = "";
        $merchant = DB::table('merchants')
            ->where('id', $merchant_id)
            ->first();
        if($merchant){
            $string = str_replace("-", " ", $merchant->name);
            $file_name = strtolower($string) . "-remittance-report.xls";
        }

        return Excel::download(new ReportMerchantRemittanceExport($order_result, $merchant_id), $file_name);
    }

}
