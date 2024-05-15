<?php

namespace App\Http\Controllers\Api;

use App\Branch;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Util\SMSUtil;
use App\InventoryHistory;
use App\OrderItem;
use App\OrderLog;
use App\OrderScan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderScanApiController extends Controller
{

    public function orderInscan(Request $request){

        $return_partial_items = $request->return_partial_items;
        $order_scan_object = new OrderScan();
        $order_scan_created = $order_scan_object->create([
            'order_no'=>$request->order_no,
            'order_id'=>$request->order_id,
            'branch_id'=>$request->branch_id,
            'remarks'=>$request->remarks,
            'scan_type'=>1
        ]);

        if($order_scan_created){

            $log_controller = new LogController();
            $log_controller->orderInscanLog($request->causer_id, $order_scan_created->id, $request->order_no);

            // Update order
            $update_order = DB::table('orders')
                ->where('id', $request->order_id)
                ->update([
                    'return_amount'=>$request->return_amount,
                    'order_status'=>'returned',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $items = $request->items;
            $items = json_decode($items);
            foreach ($items as $item) {

                if($item->inventory_id != ""){

                    $quantity = $item->quantity;
                    if($return_partial_items == "true" || $return_partial_items == 1 ){
                        $quantity = $item->quantity_returned;
                    }

                    $update_item = DB::table('order_items')
                        ->where('id', $item->id)
                        ->update([
                            'quantity_returned'=>$quantity,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                    $inventory_id = $item->inventory_id;
                    $inventory= DB::table('inventories')
                        ->where('id', $inventory_id)
                        ->first();

                    if($inventory) {

                        $balance = $inventory->quantity + $item->quantity_returned;
                        $inventory_history_object = new InventoryHistory();
                        $inventory_history_created = $inventory_history_object->create([
                            'inventory_id' => $inventory_id,
                            'transaction_type' => 1,
                            'quantity' => $quantity,
                            'balance' => $balance,
                        ]);

                        $spoilt = $item->spoilt;
                        $spoilt_balance = $inventory->spoilt;
                        if($spoilt == 'true'){
                            $spoilt_balance = $inventory->spoilt + $item->quantity_returned;
                        }

                        $update_inventory = DB::table('inventories')
                            ->where('id', $inventory_id)
                            ->update([
                                'spoilt'=> $spoilt_balance,
                                'quantity'=> $balance,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                    }

                }

            }

            $order_log_object = new OrderLog();
            $order_log_created = $order_log_object->create([
                'admin_id'=>$request->causer_id,
                'order_id'=>$request->order_id,
                'status'=>'Order Returned',
            ]);

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.order.inscan')
            );

            $response = $json_array;
            return json_encode($response);

        }else{

            $json_array = array(
                'success' => 0,
            );

            $response = $json_array;
            return json_encode($response);

        }
    }

    public function orderOutscan(Request $request){

        $rider_id = $request->rider_id;
        $orders = json_decode($request->orders);
        foreach ($orders as $order) {

            $update = DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'rider_id'=>$rider_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $order_scan_object = new OrderScan();
            $order_scan_created = $order_scan_object->create([
                'rider_id'=>$rider_id,
                'order_no'=>$order->order_no,
                'order_id'=>$order->id,
                'branch_id'=>$request->branch_id,
                'scan_type'=>2
            ]);

            if($order_scan_created){

                $log_controller = new LogController();
                $log_controller->orderOutscanLog($request->causer_id, $order_scan_created->id, $order->order_no);

                // Get order items
                $order_items = DB::table('order_items')
                    ->where('order_id', $order->id)
                    ->get();

                foreach ($order_items as $order_item){

                    if($order_item->inventory_product == 1){

                        $item_quantity = $order_item->quantity;

                        $inventory_id = $order_item->inventory_id;
                        $inventory= DB::table('inventories')
                            ->where('id', $inventory_id)
                            ->first();

                        if($inventory) {

                            $balance = $inventory->quantity - $item_quantity;
                            $inventory_history_object = new InventoryHistory();
                            $inventory_history_created = $inventory_history_object->create([
                                'inventory_id' => $inventory_id,
                                'transaction_type' => 2,
                                'quantity' => $item_quantity,
                                'balance' => $balance,
                            ]);

                            $update_inventory = DB::table('inventories')
                                ->where('id', $inventory_id)
                                ->update([
                                    'quantity'=> $balance,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);

                        }

                    }
                }

                // Update dispatched on order
                $update_order = DB::table('orders')
                    ->where('id', $order->id)
                    ->update([
                        'order_status'=>'dispatched',
                        'status_date'=>date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $order_log_object = new OrderLog();
                $order_log_created = $order_log_object->create([
                    'admin_id'=>$request->causer_id,
                    'order_id'=>$order->id,
                    'status'=>'Order dispatched',
                ]);

                $order = DB::table('orders')
                    ->where('id', $order->id)
                    ->first();
                if($order){

                    $rider_name = "";
                    $rider_phone = "";
                    $rider = DB::table('riders')
                        ->where('id', $rider_id)
                        ->first();
                    if($rider){
                        $rider_phone = $rider->phone_number;
                        $rider_name = $rider->first_name. " " . $rider->last_name;
                    }

                    $receiver_name = $order->receiver_name;
                    $receiver_phone = $order->receiver_phone;
                    $sender_name = $order->sender_name;

                    if ($sender_name !== "D.LIGHT LTD") {
                        $sms_message = "Hi $receiver_name, your order has been dispatched from our warehouse. Our agent $rider_name will call you for more details through $rider_phone. Order No $order->order_no";
                        $sms_util = new SMSUtil();
                        $sms_util->sendSMS($receiver_phone, $sms_message);
                    }

                }


            }
        }

        $json_array = array(
            'success' => 1,
            'redirect' => route('admin.order.outscan')
        );

        $response = $json_array;
        return json_encode($response);

    }
}
