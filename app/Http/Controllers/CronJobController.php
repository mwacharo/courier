<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\Notification;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class CronJobController extends Controller
{

    public function sendScheduledOrderGeneratePdf(){

        $orders = DB::table('orders')
            ->where('order_status', 'scheduled')
            ->whereDate('scheduled_date', Carbon::tomorrow())
            ->get();

        $pdfMerger = PDFMerger::init();
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
            $rider_phone = "";
            $rider = DB::table('riders')
                ->where('id', $order->rider_id)
                ->first();
            if($rider){
                $rider_name = $rider->first_name ." ".$rider->last_name;
                $rider_phone = $rider->phone_number;
            }

            // Get admin
            $admin_name = "";
            $admin = DB::table('admins')
                ->where('id', $order->admin_id)
                ->first();
            if($admin){
                $admin_name = $admin->name;
            }

            $enable_returns_management_fee = 0;
            if($order->is_sender_merchant == 1){

                $merchant = DB::table('merchants')
                    ->where('id', $order->merchant_id)
                    ->first();
                if($merchant){
                    if($merchant->enable_returns_management_fee == 1){
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
                'order_status' => $order->order_status,
                'status_reason' => $order->status_reason,
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
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            );

            $order_items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            $data = [
                'order' => $json_array,
                'order_items' => $order_items
            ];

            $file_name = "storage/pdf/".$order->order_no .".pdf";

            if($order->merchant_id == '562d3960-4d04-11ec-a361-dd2ce8fdc183'){
                $pdf = PDF::loadView('admin.report.order-waybill-vital', $data,['format' => 'A4-L']);
            }else{
                $pdf = PDF::loadView('admin.report.order-waybill', $data,['format' => 'A4-L']);
            }

            $pdf->save($file_name);
            $pdfMerger->addPDF($file_name, 'all');

        }

        $pdfMerger->merge();
        $file = new Filesystem;
        $file->cleanDirectory('storage/pdf');

        $file_name = "waybill-printout-" . date('Y-m-d');
        $pdfMerger->save(base_path('public/pdfs/' . $file_name));

        $order_scheduled_count = DB::table('orders')
            ->where('order_status', 'scheduled')
            ->whereDate('scheduled_date', Carbon::today())
            ->where('deleted_at', null)
            ->count();

        if($order_scheduled_count > 0){

            $url_link = "https://boxleocourier.com/dashboard/api/v1/waybill-scheduled-orders/waybill-printout-" . date('Y-m-d');
            $message = "Hi operations, there are $order_scheduled_count scheduled orders that need follow up actions. Use the link to download $url_link";
            $notification_object = new Notification();
            $notification_object_created = $notification_object->create([
                'message'=>$message,
                'is_read'=>false,
            ]);

        }

    }

    public function pendingOrderNotification(){

        $order_pending_count = DB::table('orders')
            ->where('order_status', 'order_pending')
            ->where('deleted_at', null)
            ->count();

        if($order_pending_count > 0){

            $message = "Hi, there are $order_pending_count pending orders that need follow up actions.";
            $notification_object = new Notification();
            $notification_object_created = $notification_object->create([
                'message'=>$message,
                'is_read'=>false,
            ]);

            $email_util = new EmailUtil();
            $send_email = $email_util->pendingOrderNotification($message);
        }
    }
}
