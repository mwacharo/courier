<?php

namespace App\Http\Controllers\Api;

use App\Exports\OrderExport;
use App\Exports\ReportMerchantExport;
use App\Exports\ReportOrderExport;
use App\Exports\ReportRiderExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MerchantReportApiController extends Controller
{

    public function reportMerchant(Request $request){

        $status_date = $request->status_date;
        $upload_date = $request->upload_date;
        $custom_date = $request->custom_date;
        $custom_start_date = $request->custom_start_date;
        $custom_end_date = $request->custom_end_date;
        $merchant_id = $request->merchant_id;
        $destination_type = $request->destination_type;
        $service_type = $request->service_type;
        $order_status = $request->order_status;
        $payment_status = $request->payment_status;


        $orders = DB::table('orders')
            ->where('is_sender_merchant', 1)
            ->where('merchant_id', $merchant_id);

        if($status_date != 'all'){

            if($status_date == 'today'){

                $orders->whereDate('status_date', Carbon::today());

            }elseif($status_date == 'current_week'){

                $orders->whereBetween('status_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            }elseif($status_date == 'last_week'){

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday",$start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $orders->whereBetween('created_at', [$start_week, $end_week]);

            }elseif($status_date == 'current_month'){

                $orders->whereMonth('status_date', Carbon::now()->month);

            }elseif($status_date == 'current_year'){

                $orders->whereYear('status_date', Carbon::now()->year);

            }elseif($status_date == 'custom_date'){

                $custom_date = date("Y-m-d",strtotime($custom_date));
                $orders->whereDate('status_date', '=', $custom_date);

            }elseif($status_date == 'custom_range'){

                $start_date = date("Y-m-d",strtotime($custom_start_date));
                $end_date = date("Y-m-d",strtotime($custom_end_date));

                $orders->whereBetween('status_date', [$start_date, $end_date]);
            }

        }

        if($upload_date != 'all'){

            if($upload_date == 'today'){

                $orders->whereDate('created_at', Carbon::today());

            }elseif($upload_date == 'current_week'){

                $orders->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

            }elseif($upload_date == 'last_week'){

                $previous_week = strtotime("-1 week +1 day");
                $start_week = strtotime("last sunday midnight", $previous_week);
                $end_week = strtotime("next saturday",$start_week);
                $start_week = date("Y-m-d", $start_week);
                $end_week = date("Y-m-d", $end_week);
                $orders->whereBetween('created_at', [$start_week, $end_week]);

            }elseif($upload_date == 'current_month'){

                $orders->whereMonth('created_at', Carbon::now()->month);

            }elseif($upload_date == 'current_year'){

                $orders->whereYear('created_at', Carbon::now()->year);

            }elseif($upload_date == 'custom_date'){

                $custom_date = date("Y-m-d",strtotime($custom_date));
                $orders->whereDate('created_at', '=', $custom_date);

            }elseif($upload_date == 'custom_range'){

                $start_date = date("Y-m-d",strtotime($custom_start_date));
                $end_date = date("Y-m-d",strtotime($custom_end_date));

                $orders->whereBetween('created_at', [$start_date, $end_date]);
            }

        }



        if($destination_type != 'all'){

            if($destination_type == 'inbound'){
                $orders->where('destination_type', 1);
            }else if($destination_type == 'outbound'){
                $orders->where('destination_type', 2);
            }
        }

        if($service_type != 'all'){

            if($service_type == 'same_day_delivery'){
                $orders->where('service_type', 1);
            }else if($service_type == 'scheduled'){
                $orders->where('service_type', 2);
            }else if($service_type == 'overnight'){
                $orders->where('service_type', 3);
            }else if($service_type == 'pickup'){
                $orders->where('service_type', 4);
            }
        }

        if($order_status != 'all'){

            if($order_status == 'order_pending'){
                $orders->where('order_status', 'order_pending');
            }

              else if($order_status == 'scheduled'){
                $orders->where('order_status', 'scheduled');
            }

            else if($order_status == 'awaiting_dispatch'){
                $orders->where('order_status', 'awaiting_dispatch');

            }

             else if($order_status == 'dispatched'){
                $orders->where('order_status', 'dispatched');

            }
            else if($order_status == 'undispatched'){
                $orders->where('order_status', 'undispatched');

            }
            else if($order_status == 'in_transit'){
                $orders->where('order_status', 'in_transit');

            }
            else if($order_status == 'not_dispatched'){
                $orders->where('order_status', 'not_dispatched');

            }else if($order_status == 'overnight'){
                $orders->where('delivery_pending', 'delivery_pending');
            }else if($order_status == 'delivered'){
                $orders->where('order_status', 'delivered');
            }else if($order_status == 'returned'){
                $orders->where('order_status', 'returned');
            }else if($order_status == 'cancelled'){
                $orders->where('order_status', 'cancelled');
            }
        }

        if($payment_status != 'all'){

            if($payment_status == 'pending'){
                $orders->where('payment_status', 0);
            }else if($payment_status == 'paid'){
                $orders->where('payment_status', 1);
            }
        }

        $orders = $orders->get();
        return json_encode($orders);

    }

    public function reportMerchantGenerate(Request $request){

        $merchant_id = $request->merchant_id;
        $orders = json_decode($request->orders);
        return Excel::download(new ReportMerchantExport($orders, $merchant_id), 'merchant-report-excel.xls');

    }
}
