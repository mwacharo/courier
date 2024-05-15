<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\OrderLog;
use App\PasswordResetCode;
use App\RiderPasswordResetCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class MobileApiController extends Controller
{
    public function riderLogin(Request $request)
    {

        $password = $request->input('password');
        $email = $request->email;
        $rider =  DB::table('riders')
            ->where('email', $email)
            ->first();

        if($rider){

            if (Hash::check($password, $rider->password)) {

                $update_account = DB::table('riders')
                    ->where('id', $rider->id)
                    ->update([ 'firebase_token'=> $request->firebase_token]);

                $json_array = array(
                    'success' => 1,
                    'rider_id' => $rider->id,
                    'first_name' => $rider->first_name,
                    'last_name' => $rider->last_name,
                    'phone_number' => $rider->phone_number,
                    'profile_image' => $rider->profile_image,
                    'date_of_birth' => $rider->date_of_birth,
                    'email' => $rider->email,
                );

                $response = $json_array;
                return json_encode($response);

            } else {

                $json_array = array(
                    'success' => 0,
                );

                $response = $json_array;
                return json_encode($response);
            }
        }else{

            $json_array = array(
                'success' => 0,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function riderProfileDetails(Request $request)
    {
        $rider_id = $request->rider_id;
        $rider =  DB::table('riders')
            ->where('id', $rider_id)
            ->first();

        if($rider){

            $orders_assigned = DB::table('orders')
                ->where('rider_id', $rider_id)
                ->where('deleted_at', null)
                ->latest()
                ->count();


            $completed_tasks = DB::table('orders')
                ->where('rider_id', $rider_id)
                ->where('order_status', 'delivered')
                ->orWhere('order_status', 'returned')
                ->where('deleted_at', null)
                ->latest()
                ->count();

            $delivered_orders = DB::table('orders')
                ->where('rider_id', $rider_id)
                ->where('order_status', 'delivered')
                ->where('deleted_at', null)
                ->latest()
                ->count();

            $delivered_orders_count = $delivered_orders;
            if($orders_assigned!=0){
                $delivered_orders = ($delivered_orders / $orders_assigned) * 100;
            }

            $cancelled_orders = DB::table('orders')
                ->where('rider_id', $rider_id)
                ->where('order_status', 'cancelled')
                ->where('deleted_at', null)
                ->latest()
                ->count();

            $cancelled_orders_count = $cancelled_orders;
            if($orders_assigned!=0){
                $cancelled_orders = ($cancelled_orders / $orders_assigned) * 100;
            }

            $returned_orders = DB::table('orders')
                ->where('rider_id', $rider_id)
                ->where('order_status', 'returned')
                ->where('deleted_at', null)
                ->latest()
                ->count();

            $returned_orders_count = $returned_orders;
            if($orders_assigned!=0){
                $returned_orders = ($returned_orders / $orders_assigned) * 100;
            }

            $json_array = array(
                'rider_id' => $rider->id,
                'first_name' => $rider->first_name,
                'last_name' => $rider->last_name,
                'phone_number' => $rider->phone_number,
                'profile_image' => $rider->profile_image,
                'date_of_birth' => $rider->date_of_birth,
                'email' => $rider->email,
                'orders_assigned' => $orders_assigned,
                'completed_tasks' => $completed_tasks,
                'delivered_orders' => round($delivered_orders),
                'cancelled_orders' => round($cancelled_orders),
                'returned_orders' => round($returned_orders),

                'delivered_orders_count' => round($delivered_orders_count),
                'cancelled_orders_count' => round($cancelled_orders_count),
                'returned_orders_count' => round($returned_orders_count),
            );

            $response = $json_array;
            return json_encode($response);

        }

    }

    public function riderUpdateProfileImage(Request $request){

        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            ]);

            $image = $request->file('image');
            $file_name = 'rider'.time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/riders/');
            $image->move($destinationPath, $file_name);
            $image_name =  url('/').'/api/v1/get-rider/'.$file_name;

            $rider_id = $request->rider_id;
            $update_account = DB::table('riders')
                ->where('id', $rider_id)
                ->update([ 'profile_image'=> $image_name]);

            if($update_account){

                $json_array = array(
                    'success' => 1,
                    'profile_image' => $image_name,
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
        }else{

            $json_array = array(
                'success' => 0,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function riderForgotPassword(Request $request)
    {
        $email = $request->email;
        $rider =  DB::table('riders')
            ->where('email', $email)
            ->first();

        if($rider){

            $rider_id = $rider->id;
            $reset_code = rand(10000,99999);
            $password_reset_code = new RiderPasswordResetCode();
            $code_created = $password_reset_code->create([
                'rider_id'=>$rider_id,
                'code'=>$reset_code,
            ]);

            if($code_created){

                $email_util = new EmailUtil();
                $send_email = $email_util->passwordResetCode($rider->first_name, $rider->email, $reset_code);

                $sms_message = "Hi $rider->first_name !, Your reset code is $reset_code.";
                $sms_util = new SMSUtil();
                $sms_util->sendSMS($rider->phone_number, $sms_message);

                $json_array = array(
                    'success' => 1,
                    'rider_id' => $rider_id,
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

        } else {

            $json_array = array(
                'success' => 2,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function riderResetPassword(Request $request){

        $rider_id = $request->rider_id;
        $reset_code = $request->reset_code;

        $conditions = ['rider_id' => $rider_id, 'code' => $reset_code];
        $check_reset=  DB::table('rider_password_reset_codes')
            ->where($conditions)
            ->latest()
            ->first();

        if($check_reset) {

            $update_password = DB::table('riders')
                ->where('id', $rider_id)
                ->update([ 'password'=> bcrypt($request->password)]);

            if($update_password){

                $json_array = array(
                    'success' => 1,
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

        }else{

            $json_array = array(
                'success' => 2,
            );

            $response = $json_array;
            return json_encode($response);

        }

    }

    public function riderResetPasswordAdmin(Request $request){

        $rider_id = $request->rider_id;

        $update_password = DB::table('riders')
            ->where('id', $rider_id)
            ->update([ 'password'=> bcrypt($request->password)]);

        if($update_password){

            $json_array = array(
                'success' => 1,
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

    public function riderUpdateProfilePassword(Request $request)
    {
        $rider_id = $request->rider_id;
        $rider =  DB::table('riders')
            ->where('id', $rider_id)
            ->first();

        if($rider){

            $update_password = DB::table('riders')
                ->where('id', $rider_id)
                ->update([
                    'password'=> bcrypt($request->password),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ]);

            if($update_password){

                $json_array = array(
                    'success' => 1,
                    'rider_id' => $rider_id,
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

        } else {

            $json_array = array(
                'success' => 2,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function riderOrderHistory(Request $request){

        $orders = DB::table('orders')
            ->where('rider_id', $request->id)
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

        return json_encode($order_result);
    }

    public function riderOrderHistoryDetails(Request $request){

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
                'admin_id' => $order->admin_id,
                'inventory' => $order->inventory,
                'admin_name' => $admin_name,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            );

            $response = $json_array;
            return json_encode($response);
        }
    }

    public function riderActiveOrderHistory(Request $request){

        $orders = DB::table('orders')
            ->where('rider_id', $request->rider_id)
            ->where('order_status', 'dispatched')
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

//            if($order->inventory == 1){
//
//                if($order->order_status == 'dispatched'){
//
//                }elseif ($order->order_status == 'delivery_pending'){
//                    array_push($order_result,
//                        array(
//                            'id' => $order->id,
//                            'order_no' => $order->order_no,
//                            'destination_type' => $order->destination_type,
//                            'inbound_rate_type' => $order->inbound_rate_type,
//                            'delivery_distance' => $order->delivery_distance,
//                            'is_sender_merchant' => $order->is_sender_merchant,
//                            'sender_name' => $order->sender_name,
//                            'sender_address' => $order->sender_address,
//                            'sender_email' => $order->sender_email,
//                            'sender_phone' => $order->sender_phone,
//                            'sender_phone_alternative' => $order->sender_phone_alternative,
//                            'sender_country' => $order->sender_country,
//                            'sender_country_name' => $sender_country_name,
//                            'sender_town' => $order->sender_town,
//                            'sender_town_name' => $sender_town_name,
//                            'receiver_name' => $order->receiver_name,
//                             'receiver_address' => $order->receiver_address,
            //        'receiver_gender' => $order->receiver_gender,
//                            'receiver_email' => $order->receiver_email,
//                            'receiver_phone' => $order->receiver_phone,
//                            'receiver_phone_alternative' => $order->receiver_phone_alternative,
//                            'receiver_country' => $order->receiver_country,
//                            'receiver_country_name' => $receiver_country_name,
//                            'receiver_town' => $order->receiver_town,
//                            'receiver_town_name' => $receiver_town_name,
//                            'receiver_latitude' => $order->receiver_latitude,
//                            'receiver_longitude' => $order->receiver_longitude,
//                            'special_instruction' => $order->special_instruction,
//                            'payment_type' => $order->payment_type,
                       // 'upsell' => $order->upsell,
//                            'cash_on_delivery' => $order->cash_on_delivery,
//                            'cash_on_delivery_amount' => $order->cash_on_delivery_amount,
//                            'amount' => $order->amount,
//                            'service_type' => $order->service_type,
//                            'insurance' => $order->insurance,
//                            'order_status' => $order->order_status,
//                            'status_reason' => $order->status_reason,
//                            'payment_status' => $order->payment_status,
//                            'zone_id' => $order->zone_id,
//                            'rider_id' => $order->rider_id,
//                            'rider_name' => $rider_name,
//                            'branch_id' => $order->branch_id,
//                            'branch_name' => $branch_name,
//                            'booking_date' => $order->booking_date,
//                            'delivery_date' => $order->delivery_date,
//                            'admin_id' => $order->admin_id,
//                            'admin_name' => $admin_name,
//                            'created_at' => $order->created_at,
//                            'updated_at' => $order->updated_at,
//                        ));
//                }
//
//            }else{
//                if($order->order_status == 'scheduled'){
//                    array_push($order_result,
//                        array(
//                            'id' => $order->id,
//                            'order_no' => $order->order_no,
//                            'destination_type' => $order->destination_type,
//                            'inbound_rate_type' => $order->inbound_rate_type,
//                            'delivery_distance' => $order->delivery_distance,
//                            'is_sender_merchant' => $order->is_sender_merchant,
//                            'sender_name' => $order->sender_name,
//                            'sender_address' => $order->sender_address,
//                            'sender_email' => $order->sender_email,
//                            'sender_phone' => $order->sender_phone,
//                            'sender_phone_alternative' => $order->sender_phone_alternative,
//                            'sender_country' => $order->sender_country,
//                            'sender_country_name' => $sender_country_name,
//                            'sender_town' => $order->sender_town,
//                            'sender_town_name' => $sender_town_name,
//                            'receiver_name' => $order->receiver_name,
//                             'receiver_address' => $order->receiver_address,
                  //  'receiver_gender' => $order->receiver_gender,
//                            'receiver_email' => $order->receiver_email,
//                            'receiver_phone' => $order->receiver_phone,
//                            'receiver_phone_alternative' => $order->receiver_phone_alternative,
//                            'receiver_country' => $order->receiver_country,
//                            'receiver_country_name' => $receiver_country_name,
//                            'receiver_town' => $order->receiver_town,
//                            'receiver_town_name' => $receiver_town_name,
//                            'receiver_latitude' => $order->receiver_latitude,
//                            'receiver_longitude' => $order->receiver_longitude,
//                            'special_instruction' => $order->special_instruction,
//                            'payment_type' => $order->payment_type,
                     //   'upsell' => $order->upsell,
//                            'cash_on_delivery' => $order->cash_on_delivery,
//                            'cash_on_delivery_amount' => $order->cash_on_delivery_amount,
//                            'amount' => $order->amount,
//                            'service_type' => $order->service_type,
//                            'insurance' => $order->insurance,
//                            'order_status' => $order->order_status,
//                            'status_reason' => $order->status_reason,
//                            'payment_status' => $order->payment_status,
//                            'zone_id' => $order->zone_id,
//                            'rider_id' => $order->rider_id,
//                            'rider_name' => $rider_name,
//                            'branch_id' => $order->branch_id,
//                            'branch_name' => $branch_name,
//                            'booking_date' => $order->booking_date,
//                            'delivery_date' => $order->delivery_date,
//                            'admin_id' => $order->admin_id,
//                            'admin_name' => $admin_name,
//                            'created_at' => $order->created_at,
//                            'updated_at' => $order->updated_at,
//                        ));
//                }elseif ($order->order_status == 'delivery_pending'){
//                    array_push($order_result,
//                        array(
//                            'id' => $order->id,
//                            'order_no' => $order->order_no,
//                            'destination_type' => $order->destination_type,
//                            'inbound_rate_type' => $order->inbound_rate_type,
//                            'delivery_distance' => $order->delivery_distance,
//                            'is_sender_merchant' => $order->is_sender_merchant,
//                            'sender_name' => $order->sender_name,
//                            'sender_address' => $order->sender_address,
//                            'sender_email' => $order->sender_email,
//                            'sender_phone' => $order->sender_phone,
//                            'sender_phone_alternative' => $order->sender_phone_alternative,
//                            'sender_country' => $order->sender_country,
//                            'sender_country_name' => $sender_country_name,
//                            'sender_town' => $order->sender_town,
//                            'sender_town_name' => $sender_town_name,
//                            'receiver_name' => $order->receiver_name,
//                             'receiver_address' => $order->receiver_address,
                   // 'receiver_gender' => $order->receiver_gender,
//                            'receiver_email' => $order->receiver_email,
//                            'receiver_phone' => $order->receiver_phone,
//                            'receiver_phone_alternative' => $order->receiver_phone_alternative,
//                            'receiver_country' => $order->receiver_country,
//                            'receiver_country_name' => $receiver_country_name,
//                            'receiver_town' => $order->receiver_town,
//                            'receiver_town_name' => $receiver_town_name,
//                            'receiver_latitude' => $order->receiver_latitude,
//                            'receiver_longitude' => $order->receiver_longitude,
//                            'special_instruction' => $order->special_instruction,
//                            'payment_type' => $order->payment_type,
                       // 'upsell' => $order->upsell,
//                            'cash_on_delivery' => $order->cash_on_delivery,
//                            'cash_on_delivery_amount' => $order->cash_on_delivery_amount,
//                            'amount' => $order->amount,
//                            'service_type' => $order->service_type,
//                            'insurance' => $order->insurance,
//                            'order_status' => $order->order_status,
//                            'status_reason' => $order->status_reason,
//                            'payment_status' => $order->payment_status,
//                            'zone_id' => $order->zone_id,
//                            'rider_id' => $order->rider_id,
//                            'rider_name' => $rider_name,
//                            'branch_id' => $order->branch_id,
//                            'branch_name' => $branch_name,
//                            'booking_date' => $order->booking_date,
//                            'delivery_date' => $order->delivery_date,
//                            'admin_id' => $order->admin_id,
//                            'admin_name' => $admin_name,
//                            'created_at' => $order->created_at,
//                            'updated_at' => $order->updated_at,
//                        ));
//                }
//            }

        }

        return json_encode($order_result);
    }

    public function riderDeliveryHistory(Request $request){

        $orders = DB::table('orders')
            ->where('rider_id', $request->rider_id)
            ->where(function($q) {
                $q->where('order_status', 'delivered')
                    ->orWhere('order_status', 'cancelled');
            })
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

        return json_encode($order_result);
    }

    public function riderOrderHistoryStatus(Request $request)
    {
        $order_id = $request->order_id;
        $order =  DB::table('orders')
            ->where('id', $order_id)
            ->first();

        if($order){

            if($request->status_reason != 'null'){
                $update_order = DB::table('orders')
                    ->where('id', $order_id)
                    ->update([
                        'order_status'=> $request->status,
                        'status_reason'=> $request->status_reason,
                        'delivered_date'=>date('Y-m-d H:i:s'),
                        'updated_at'=>date('Y-m-d H:i:s'),
                    ]);
            }else{

                $update_order = DB::table('orders')
                    ->where('id', $order_id)
                    ->update([
                        'order_status'=> $request->status,
                        'delivered_date'=>date('Y-m-d H:i:s'),
                        'updated_at'=>date('Y-m-d H:i:s'),
                    ]);
            }


            if($update_order){

                $json_array = array(
                    'success' => 1,
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

        } else {

            $json_array = array(
                'success' => 2,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function riderOrderPaymentStatus(Request $request)
    {
        $order_id = $request->order_id;
        $order =  DB::table('orders')
            ->where('id', $order_id)
            ->first();

        if($order){

            $transaction_code = "";
            if($request->transaction_code != 'null'){
                $transaction_code = $request->transaction_code;
            }

            $cash_amount = "";
            if($request->cash_amount != 'null'){
                $cash_amount = $request->cash_amount;
            }

            $mpesa_amount = "";
            if($request->mpesa_amount != 'null'){
                $mpesa_amount = $request->mpesa_amount;
            }

            $cash_mpesa_amount = "";
            if($request->cash_mpesa_amount != 'null'){
                $cash_mpesa_amount = $request->cash_mpesa_amount;
            }

            $special_instruction = "";
            if($request->special_instruction != 'null'){
                $special_instruction = $request->special_instruction;
            }

            $update_order = DB::table('orders')
                ->where('id', $order_id)
                ->update([
                    'payment_status'=> $request->status,
                    'payment_method'=> $request->payment_method,
                    'cash_amount'=> $cash_amount,
                    'mpesa_amount'=> $mpesa_amount,
                    'cash_mpesa_amount'=> $cash_mpesa_amount,
                    'transaction_code'=> $transaction_code,
                    'special_instruction'=> $special_instruction,
                    'updated_at'=>date('Y-m-d H:i:s'),
                ]);


            if($update_order){

                $order_log_object = new OrderLog();
                $order_log_created = $order_log_object->create([
                    'order_id'=>$order->id,
                    'status'=>'Order delivered',
                ]);

                $update_order = DB::table('orders')
                    ->where('id', $order_id)
                    ->update([
                        'order_status'=> 'delivered',
                        'status_date'=> date('Y-m-d'),
                        'delivered_date'=>date('Y-m-d H:i:s'),
                        'updated_at'=>date('Y-m-d H:i:s'),
                    ]);

                $link = "https://boxleocourier.com/dashboard/api/v1/order-waybill/".$order->order_no;
                $sms_message = "Hi $order->receiver_name !, Your order has been delivered successfully. To see waybill click $link";
                $sms_util = new SMSUtil();
                $sms_util->sendSMS($order->receiver_phone, $sms_message);

                $json_array = array(
                    'success' => 1,
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

        } else {

            $json_array = array(
                'success' => 2,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function riderOrderWaybill(Request $request){

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

            if($order->merchant_id == '562d3960-4d04-11ec-a361-dd2ce8fdc183'){
                $pdf = PDF::loadView('admin.report.order-waybill-vital', $data,['format' => 'A4-L']);
            }else{
                $pdf = PDF::loadView('admin.report.order-waybill', $data,['format' => 'A4-L']);
            }
            return $pdf->stream('admin.report.order-waybill');

        }

    }
}




