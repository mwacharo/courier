<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoogleSheetController;
use App\Imports\OrderImport;
use App\WaybillPrintLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {

        $log_controller = new LogController();
        $log_controller->orderListLog();

        $orders = DB::table('orders')
            ->where('deleted_at', null)
            ->latest()
            ->limit(30)
            ->get();

        $order_result = [];
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
                $admin_name = $admin->first_name . " " .$admin->last_name;
            }

            $print_count = DB::table('waybill_print_logs')
                ->where('order_id', $order->id)
                ->count();

            array_push($order_result,
                [
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
                    'follow_up_date' => $order->follow_up_date,
                    'delivery_date' => $order->delivery_date,
                    'delivered_date' => $order->delivered_date,
                    'scheduled_date' => $order->scheduled_date,
                    'admin_id' => $order->admin_id,
                    'admin_name' => $admin_name,
                    'print_count' => $print_count,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]);

        }

        $data = [
            'orders' => $order_result
        ];

        return view('admin.order.index',$data);
    }

    public function create()
    {
        return view('admin.order.order-create');
    }

    public function details(Request $request)
    {

        $order = DB::table('orders')
            ->where('id', $request->id)
            ->first();

        $log_controller = new LogController();
        $log_controller->orderDetailsLog($request->id, $order->order_no);


        $result_logs= [];
        $logs = DB::table('order_logs')
            ->where('order_id', $request->id)
            ->orderByDesc('created_at')
            ->get();

        foreach ($logs as $log){

            $admin_name = "";
            $admin = DB::table('admins')
                ->where('id', $log->admin_id)
                ->first();

            if($admin){
                $admin_name = $admin->first_name . " " . $admin->last_name;
            }

            array_push($result_logs,
                [
                    'id' => $log->id,
                    'admin_id' => $log->admin_id,
                    'admin_name' => $admin_name,
                    'order_id' => $log->order_id,
                    'status' => $log->status,
                    'created_at' => $log->created_at,
                ]);
        }


        $data = [
            'id' => $request->id,
            'logs' => $result_logs,
        ];
        return view('admin.order.order-details', $data);
    }

    public function reportExcel(){
        
        return Excel::download(new OrderExport, 'order-report-excel.xls');
    }

    public function reportPdf(){

        $orders = DB::table('orders')
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
                $admin_name = $admin->first_name . " " .$admin->last_name;
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

                    'follow_up_date' => $order->follow_up_date,
                    'delivery_date' => $order->delivery_date,
                    'delivered_date' => $order->delivered_date,
                    'scheduled_date' => $order->scheduled_date,
                    'admin_id' => $order->admin_id,
                    'admin_name' => $admin_name,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ));

        }

        $data = [
            'orders' => $order_result
        ];

        $pdf = PDF::loadView('admin.report.order-report-pdf', $data);

        return $pdf->stream('admin.report.order-report-pdf');
    }

    public function importExcel()
    {
        $merchants = DB::table('merchants')
            ->orderBy('name', 'ASC')
            ->get();
        $data = [
            'merchants' => $merchants
        ];
        return view('admin.order.order-import', $data);
    }

    public function importExcelUpload(Request $request)
    {
        $this->validate($request, [
            'select_file'  => 'required|mimes:xls,xlsx'
        ]);

        $path1 = $request->file('select_file')->store('temp');
        $path=storage_path('app').'/'.$path1;

        $merchant_id = $request->merchant_id;
        $import = new OrderImport($merchant_id);
        Excel::import($import, $path);

        if($import->getErrorMessage() == ''){
            return back()->with('success', 'Excel Data Imported successfully.' . $import->getRowCount() . ' rows imported');
        }else{
            return back()->with('error', 'Import Errors.' . $import->getErrorMessage());
        }

    }

    public function orderSchedule()
    {
        return view('admin.order.order-schedule');
    }

    public function orderAwaitingDispatch()
    {
        return view('admin.order.order-awaiting-dispatch');
    }

    public function orderDispatch()
    {
        return view('admin.order.order-dispatch');
    }

    public function orderUndispatch()
    {

      return view('admin.order.order-undispatch');
    }

    public function orderUndispatched()
    {
        return view('admin.order.order-undispatched');
    }



    public function orderInTransit()
    {

        return view('admin.order.order-intransit');

    }



    public function orderDuplicate()
    {
        return view('admin.order.order-duplicate');
    }

    public function orderFollowup()
    {
        return view('admin.order.order-followup');
    }

    public function orderPending()
    {

        return view('admin.order.order-pending');
    }

    public function orderDispatchPolicy()
    {
        return view('admin.order.order-dispatch-policy');
    }

    public function shippingCalculator()
    {
        return view('admin.order.shipping-calculator');
    }

    public function assignRider(Request $request)
    {
        $data = [
            'id' => $request->id,
        ];

        return view('admin.order.order-assign-rider', $data);
    }

    public function getOrderWaybill(Request $request){


        $order = DB::table('orders')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        if($order){

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
                $$admin_name = $admin->first_name . " " .$admin->last_name;
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
                'sender_country_name' => $sender_country_name,
                'sender_town' => $order->sender_town,
                'sender_town_name' => $sender_town_name,
                'receiver_name' => $order->receiver_name,
                 'receiver_address' => $order->receiver_address,

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

                'follow_up_date' => $order->follow_up_date,
                'delivery_date' => $order->delivery_date,
                'delivered_date' => $order->delivered_date,
                'scheduled_date' => $order->scheduled_date,
                'total_weight' => $order->total_weight,
                'admin_id' => $order->admin_id,
                'inventory' => $order->inventory,
                'admin_name' => $admin_name,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ];

            $order_items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();



            $data = [
                'order' => $json_array,
                'order_items' => $order_items
            ];

            $print_log_object = new WaybillPrintLog();
            $print_log_object->create([
                'admin_id'=>Auth::id(),
                'order_id'=>$order->id,
            ]);


            $file_name = $order->order_no."-waybill.pdf";


            $pdf = PDF::loadView('admin.report.order-waybill', $data,['format' => 'A4-L']);
            return $pdf->download($file_name);


        }

    }


    public function importGoogleExcel()
    {
        return view('admin.order.order-import-google');
    }

    public function importGoogleExcelSubmit()
    {

        $sheet_controller = new GoogleSheetController();
        $sheet_controller->importOrders("429b6260-bfc0-11ea-b839-e98a54f927bc", "FITSTALL");
        $sheet_controller->importOrders("6aa03330-136c-11eb-8959-4bf56aa0aa39", "SAWAH");
        $sheet_controller->importOrders("2c79b150-c773-11ea-b0b0-37614dfab44a", "BUCKS");
        $sheet_controller->importOrders("0ba8ada0-136c-11eb-b271-0d5565a4f190", "ZAWADI");
        $sheet_controller->importOrders("38d30890-cb65-11ea-a19e-4b40399ce4f8", "GADGETCENTRAL");
        $sheet_controller->importOrders("f29f3250-d57c-11ea-8f09-9f2b188ac95a", "SMARTCHOICE");
        $sheet_controller->importOrders("022bc7b0-e06e-11ea-9704-0d4dad23aa16", "SHAKARAA");
        $sheet_controller->importOrders("d1a69170-d55b-11ea-90cd-5122d029f241", "SHOPTTEMALL");
        $sheet_controller->importOrders("62a14e00-e91c-11ea-ba57-a78cec9e4149", "EXPRESS");
        $sheet_controller->importOrders("353cb550-019e-11eb-8b66-f33fad807bc1", "NATURALDEALS");
        $sheet_controller->importOrders("aa2fafc0-d57b-11ea-9b90-c7364684052f", "TUSHSTORE");
        $sheet_controller->importOrders("fe83a9c0-edc6-11ea-907e-0b76081e0606", "UCT");
        $sheet_controller->importOrders("f27fed80-cb32-11ea-ab61-0f5e78d4ffe6", "BELMOKADEM");
        $sheet_controller->importOrders("c3a8c8e0-dc7e-11ea-b3d5-a3b77dc5ba73", "KENYATRENDYZ");
        $sheet_controller->importOrders("85aeee70-eb66-11ea-a6eb-07c575f36088", "YEMSMALL");
        $sheet_controller->importOrders("17876c10-d584-11ea-82b9-53c2f6fe726a", "DELUXEHUB");
        $sheet_controller->importOrders("22de82c0-c76c-11ea-b41d-4fbb65ed34c8", "ZINUSTORE");
        $sheet_controller->importOrders("383a3160-c76f-11ea-81a9-d38f75e9cd69", "OMALICHA");
        $sheet_controller->importOrders("73465460-f8d2-11ea-a5c5-3d1453df4c32", "DTSTORES");
        $sheet_controller->importOrders("9c5cc0f0-ef68-11ea-99d4-85a330db41f8", "CITYGATE");
        $sheet_controller->importOrders("606261c0-d63c-11ea-aef1-bf1cbfe1af2d", "SOFVON");
        $sheet_controller->importOrders("98751290-ca8a-11ea-84cf-07b14e396a66", "OLAPETANVILLA");
        $sheet_controller->importOrders("b3d48510-d976-11ea-99fb-a740d62fe7e9", "ARROWDEALS");
        $sheet_controller->importOrders("ba581d90-c757-11ea-924e-715ffe560b97", "MAMABUSINESS");
        $sheet_controller->importOrders("c0ff3c00-04a4-11eb-a6b9-c3236b63e88c", "RELIANCE");
        $sheet_controller->importOrders("be61c9a0-ca94-11ea-bc02-b3307a75a03a", "BLAZEDTRAIL");
        $sheet_controller->importOrders("7ab10cc0-e798-11ea-8c65-5d041e2c20a4", "MATOLEO");
        $sheet_controller->importOrders("0ba8ada0-136c-11eb-b271-0d5565a4f190", "KNY");
        $sheet_controller->importOrders("ced4cbd0-ca89-11ea-afa3-c93539530f44", "Olssel");
        $sheet_controller->importOrders("841a7ee0-c76e-11ea-855c-5fd9452b8346", "JOLLOF");
        $sheet_controller->importOrders("cca26260-c771-11ea-9097-0d82c780ade6", "9ICESTORE");
        $sheet_controller->importOrders("e8b16b70-c76d-11ea-bb8b-2f76fdcb3591", "RHIONA");
        $sheet_controller->importOrders("e9655f40-2354-11eb-b63c-810814024dcb", "COMMYSTORES");
        return back()->with('success', 'Excel Data Imported successfully.');

    }

}
