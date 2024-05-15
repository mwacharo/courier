<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class MerchantOrderExport implements FromView
{
    protected $merchant_id;
    public function __construct(array $merchant_id)
    {
        $this->merchant_id = $merchant_id;
    }

    public function view(): View
    {

        $orders = DB::table('orders')
            ->where('merchant_id', $this->merchant_id)
            ->where('deleted_at', null)
            ->latest()
            ->limit(10)
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
                    'order_status' => $order->order_status,
                    'status_reason' => $order->status_reason,
                    'status_date' => $order->status_date,
                    'scheduled_date' => $order->scheduled_date,
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

        return view('merchant.report.order-report-excel', [
            'orders' => $order_result
        ]);
    }
}
