<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class ReportAllMerchantExport implements FromView
{
    protected $orders;
    protected $merchant_id;
    public function __construct(array $orders, string $merchant_id)
    {
        $this->orders = $orders;
        $this->merchant_id = $merchant_id;


    }

    public function view(): View
    {
        $order_result = array();
        foreach ($this->orders as $order){

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

            $callcenter_agent = "";
            $call_agent = DB::table('call_agents')
                ->where('client_name', $order->agent)
                ->where('deleted_at', null)
                ->first();
            if($call_agent){

                $admin = DB::table('admins')
                    ->where('id', $call_agent->admin_id)
                    ->where('role', 4)
                    ->where('deleted_at', null)
                    ->first();

                if($admin){
                    $callcenter_agent = $admin->first_name . ' ' . $admin->last_name;
                }
            }

            else{
                $callcenter_agent = $order->agent;
            }

            $followup_agent = "";
            $call_agent = DB::table('call_agents')
                ->where('client_name', $order->agent)
                ->where('deleted_at', null)
                ->first();
            if($call_agent){

                $admin = DB::table('admins')
                    ->where('id', $call_agent->admin_id)
                    ->where('role', 3)
                    ->where('deleted_at', null)
                    ->first();

                if($admin){
                    $followup_agent = $admin->first_name . ' ' . $admin->last_name;
                }
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
                        'agent' => $order->agent,
                        'call_agent' => $callcenter_agent,
                        'followup_agent' => $followup_agent,
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

        $merchant = DB::table('merchants')
            ->where('id', $this->merchant_id)
            ->first();

        return view('admin.report.report-all-merchant-order-excel', [
            'merchant' => $merchant,
            'orders' => $order_result
        ]);
    }
}
