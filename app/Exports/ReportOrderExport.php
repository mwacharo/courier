<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class ReportOrderExport implements FromView
{
    protected $orders;
    public function __construct(array $orders)
    {
        $this->orders = $orders;
    }

    public function view(): View
    {
        $order_result = [];
        foreach ($this->orders as $order){
            // Receiver town
            $receiver_town_name = "";
            $town = DB::table('towns')
                ->where('id', $order->receiver_town)
                ->first();
            if($town){
                $receiver_town_name = $town->name;
            }


            array_push($order_result,
                array(
                    'order_no' => $order->order_no,
                    'receiver_name' => $order->receiver_name,
                    'receiver_address' => $order->receiver_address,
                    'receiver_email' => $order->receiver_email,
                    'receiver_phone' => $order->receiver_phone,
                    'receiver_phone_alternative' => $order->receiver_phone_alternative,
                    'receiver_town' => $order->receiver_town,
                    'receiver_town_name' => $receiver_town_name,
               ));

        }

        return view('admin.report.order-report-excel', [
            'orders' => $order_result
        ]);
    }
}
