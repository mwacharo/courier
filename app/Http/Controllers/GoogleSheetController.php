<?php

namespace App\Http\Controllers;
use App\Order;
use App\OrderItem;
use App\Services\GoogleSheet;
use Illuminate\Support\Facades\DB;

class GoogleSheetController extends Controller
{
    public function importOrders($merchant_id, $spreadsheet){

        $google_sheet = new GoogleSheet("1LZOzyk6vdsEQUoqo3nbKNzv787k5Ohx0B0S0YKvkNO4", $spreadsheet);
        $rows = $google_sheet->readGoogleSheet();

        if($rows != null){

            foreach ($rows as $row)
            {
                $order_no = $row[0];
                $cash_on_delivery = $row[1];
                $receiver_name = $row[2];
                $receiver_address = $row[3];
                $receiver_phone = $row[4];
                $receiver_alternative_phone = $row[5];
                $receiver_country_name = $row[6];
                $receiver_town_name = $row[7];
                $item_sku = $row[8];
                $item_name = $row[9];
                $item_quantity = $row[10];
                $order_status = $row[11];
                $status_date = $row[12];
                $scheduled_date = $row[13];
                $destination_type = $row[14];

                if($order_no == ''){

                    $order_count = DB::table('orders')
                        ->count();
                    if($order_count > 0){
                        $order = DB::table('orders')
                            ->latest()
                            ->first();
                        if($order){
                            $order_id = $order->order_id + 1;
                            $order_no = "BX000".$order_id;
                        }
                    }else{
                        $order_no = "BX0001";
                    }

                }

                $order_no_count = DB::table('orders')
                    ->where('order_no', $order_no)
                    ->count();
                if($order_no_count == 0){

                    $sender_name= "";
                    $sender_address= "";
                    $sender_email= "";
                    $sender_phone= "";
                    $sender_country = "";
                    $sender_town = "";
                    $merchant = DB::table('merchants')
                        ->where('id', $merchant_id)
                        ->first();
                    if($merchant){
                        $merchant_id = $merchant->id;
                        $sender_name = $merchant->name;
                        $sender_address = $merchant->address;
                        $sender_phone = $merchant->phone_number;
                        $sender_email = $merchant->email;
                        $sender_country = $merchant->country_id;
                        $sender_town = $merchant->town_id;
                    }else{
                        echo "Merchant does not exist or incorrect";
                        return;
                    }

                    $receiver_country = "";
                    $country = DB::table('countries')
                        ->where('name', 'LIKE', "%{$receiver_country_name}%")
                        ->where('deleted_at', null)
                        ->first();
                    if($country){
                        $receiver_country = $country->id;
                    }else{
                        $country = DB::table('countries')
                            ->where('name', 'LIKE', "%Kenya%")
                            ->where('deleted_at', null)
                            ->first();
                        if($country){
                            $receiver_country = $country->id;
                        }
                    }

                    $receiver_town = "";
                    $town = DB::table('towns')
                        ->where('name', 'LIKE', "%{$receiver_town_name}%")
                        ->where('deleted_at', null)
                        ->first();

                    if($town){
                        $receiver_town = $town->id;
                    }else{
                        $town = DB::table('towns')
                            ->where('name', 'LIKE', "%Nairobi%")
                            ->where('deleted_at', null)
                            ->first();
                        if($town){
                            $receiver_town = $town->id;
                        }
                    }

                    $cash_on_delivery_amount = 0;
                    if($cash_on_delivery != ""){
                        $cash_on_delivery_amount = $cash_on_delivery;
                        $cash_on_delivery = true;
                    }


                    if($order_status == ''){
                        $order_status = 'order_pending';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                    }else{

                        if($order_status == 'PENDING'){

                            $order_status = 'order_pending';
                            $status_date = date('Y-m-d');
                            $scheduled_date = '';

                        }else if($order_status == 'SCHEDULED'){

                            $order_status = 'scheduled';
                            $status_date = date('Y-m-d');

                            if($scheduled_date == ''){
                                $this->message = "Scheduled date not added. Please check again";
                                return;
                            }

                        }else if($order_status == 'CANCELLED'){

                            $order_status = 'cancelled';
                            $status_date = date('Y-m-d');
                            $scheduled_date = '';

                        }
                    }

                    if(strtolower($destination_type) == 'outbound'){
                        $destination_type = 1;
                    }else{
                        $destination_type = 2;
                    }

                    $order_object = new Order();
                    $order_created = $order_object->create([
                        'order_no'=>$order_no,
                        'is_sender_merchant'=>true,
                        'destination_type'=>$destination_type,
                        'merchant_id'=>$merchant_id,
                        'sender_name'=>$sender_name,
                        'sender_address'=>$sender_address,
                        'sender_email'=>$sender_email,
                        'sender_phone'=>$sender_phone,
                        'sender_country'=>$sender_country,
                        'sender_town'=>$sender_town,
                        'receiver_name'=>$receiver_name,
                        'receiver_address'=>$receiver_address,
                        'receiver_phone'=>$receiver_phone,
                        'receiver_phone_alternative'=>$receiver_alternative_phone,
                        'receiver_country'=>$receiver_country,
                        'receiver_town'=>$receiver_town,
                        'payment_type'=>2,
                        'cash_on_delivery'=>$cash_on_delivery,
                        'cash_on_delivery_amount'=>$cash_on_delivery_amount,
                        'order_status'=>$order_status,
                        'status_date'=>$status_date,
                        'scheduled_date'=>$scheduled_date,
                    ]);

                    if($order_created){

                        $inventory_id = "";
                        $inventory = DB::table('inventories')
                            ->where('sku', $item_sku)
                            ->where('deleted_at', null)
                            ->first();
                        if($inventory){
                            $inventory_id = $inventory->id;
                        }

                        $inventory_product = false;
                        if($inventory_id != ""){
                            $inventory_product = true;
                        }

                        $order_item = new OrderItem();
                        $item_created = $order_item->create([
                            'order_id'=>$order_created->id,
                            'inventory_product'=>$inventory_product,
                            'inventory_id'=>$inventory_id,
                            'description'=>$item_name,
                            'quantity'=>$item_quantity,
                        ]);

                        if($inventory_product == true){
                            $update_order= DB::table('orders')
                                ->where('id', $order_created->id)
                                ->update([
                                    'inventory'=>true,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]);
                        }

                        echo "Order added!";

                    }

                }else{

                    $order = DB::table('orders')
                        ->where('order_no', $order_no)
                        ->first();

                    if($order){

                        $sender_name= "";
                        $sender_address= "";
                        $sender_email= "";
                        $sender_phone= "";
                        $sender_country = "";
                        $sender_town = "";
                        $merchant = DB::table('merchants')
                            ->where('id', $merchant_id)
                            ->first();
                        if($merchant){
                            $merchant_id = $merchant->id;
                            $sender_name = $merchant->name;
                            $sender_address = $merchant->address;
                            $sender_phone = $merchant->phone_number;
                            $sender_email = $merchant->email;
                            $sender_country = $merchant->country_id;
                            $sender_town = $merchant->town_id;
                        }else{
                            echo "Merchant does not exist or incorrect";
                            return;
                        }

                        $receiver_country = "";
                        $country = DB::table('countries')
                            ->where('name', 'LIKE', "%{$receiver_country_name}%")
                            ->where('deleted_at', null)
                            ->first();
                        if($country){
                            $receiver_country = $country->id;
                        }else{
                            $country = DB::table('countries')
                                ->where('name', 'LIKE', "%Kenya%")
                                ->where('deleted_at', null)
                                ->first();
                            if($country){
                                $receiver_country = $country->id;
                            }
                        }

                        $receiver_town = "";
                        $town = DB::table('towns')
                            ->where('name', 'LIKE', "%{$receiver_town_name}%")
                            ->where('deleted_at', null)
                            ->first();
                        if($town){
                            $receiver_town = $town->id;
                        }else{
                            $town = DB::table('towns')
                                ->where('name', 'LIKE', "%Nairobi%")
                                ->where('deleted_at', null)
                                ->first();
                            if($town){
                                $receiver_town = $town->id;
                            }
                        }

                        $cash_on_delivery_amount = 0;
                        if($cash_on_delivery != ""){
                            $cash_on_delivery_amount = $cash_on_delivery;
                            $cash_on_delivery = true;
                        }


                        if($order_status == ''){
                            $order_status = 'order_pending';
                            $status_date = date('Y-m-d');
                            $scheduled_date = '';

                        }else{

                            if($order_status == 'PENDING'){

                                $order_status = 'order_pending';
                                $status_date = date('Y-m-d');
                                $scheduled_date = '';

                            }else if($order_status == 'SCHEDULED'){

                                $order_status = 'scheduled';
                                $status_date = date('Y-m-d');

                                if($scheduled_date == ''){
                                    $this->message = "Scheduled date not added. Please check again";
                                    return;
                                }

                            }else if($order_status == 'CANCELLED'){

                                $order_status = 'cancelled';
                                $status_date = date('Y-m-d');
                                $scheduled_date = '';

                            }
                        }

                        $update = DB::table('orders')
                            ->where('id', $order->id)
                            ->update([
                                'order_no'=>$order_no,
                                'is_sender_merchant'=>true,
                                'merchant_id'=>$merchant_id,
                                'sender_name'=>$sender_name,
                                'sender_address'=>$sender_address,
                                'sender_email'=>$sender_email,
                                'sender_phone'=>$sender_phone,
                                'sender_country'=>$sender_country,
                                'sender_town'=>$sender_town,
                                'receiver_name'=>$receiver_name,
                                'receiver_address'=>$receiver_address,
                                'receiver_phone'=>$receiver_phone,
                                'receiver_phone_alternative'=>$receiver_alternative_phone,
                                'receiver_country'=>$receiver_country,
                                'receiver_town'=>$receiver_town,
                                'payment_type'=>2,
                                'cash_on_delivery'=>$cash_on_delivery,
                                'cash_on_delivery_amount'=>$cash_on_delivery_amount,
                                'order_status'=>$order_status,
                                'status_date'=>$status_date,
                                'scheduled_date'=>$scheduled_date,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);

                        if($update){

                            $inventory_id = "";
                            $inventory = DB::table('inventories')
                                ->where('sku', $item_sku)
                                ->where('deleted_at', null)
                                ->first();
                            if($inventory){
                                $inventory_id = $inventory->id;
                            }

                            $inventory_product = false;
                            if($inventory_id != ""){
                                $inventory_product = true;
                            }

                            $order_item = DB::table('order_items')
                                ->where('order_id', $order->id)
                                ->where('inventory_id', $inventory_id)
                                ->where('deleted_at', null)
                                ->first();

                            if($order_item){

                                $update_item = DB::table('order_items')
                                    ->where('id', $order_item->id)
                                    ->update([
                                        'inventory_product'=>$inventory_product,
                                        'inventory_id'=>$inventory_id,
                                        'description'=>$inventory->description,
                                        'quantity'=>$item_quantity,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);


                            }else{

                                $order_item = new OrderItem();
                                $item_created = $order_item->create([
                                    'order_id'=>$order->id,
                                    'inventory_product'=>$inventory_product,
                                    'inventory_id'=>$inventory_id,
                                    'description'=>$item_name,
                                    'quantity'=>$item_quantity,
                                ]);

                            }


                            if($inventory_product == true){
                                $update_order= DB::table('orders')
                                    ->where('id', $order->id)
                                    ->update([
                                        'inventory'=>true,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ]);
                            }

                        }

                    }

                }

            }
        }


    }

}
