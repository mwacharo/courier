<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\PasswordGeneratorUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MerchantApiController extends Controller
{
    public function getMerchantList(Request $request)
    {

        $merchants = DB::table('merchants')
            ->where('deleted_at', null)
            ->orderBy('name')
            ->get();

        return json_encode($merchants);

    }

    public function getMerchantDetails(Request $request)
    {

        $merchant = DB::table('merchants')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($merchant);

    }

    public function createMerchantDetails(Request $request)
    {
        $format_phone_number_util = new FormatPhoneNumberUtil();
        $phone_number = $format_phone_number_util->formatPhoneNumber($request->phone_number);

        //Check if phone exists
        $phone_number_check = DB::table('merchants')->where('phone_number', $phone_number)->first();
        if ($phone_number_check) {

            $json_array = array(
                'success' => 3,
                'message' => "Phone number already exist",
            );

            $response = $json_array;
            return json_encode($response);
        }

        if ($request->merchant_prefix != '') {
            $email_check = DB::table('merchants')->where('merchant_prefix', $request->merchant_prefix)->first();
            if ($email_check) {

                $json_array = array(
                    'success' => 3,
                    'message' => "Merchant prefix already exist",
                );

                $response = $json_array;
                return json_encode($response);
            }
        }

        //Check if email exists
        if ($request->email != '') {
            $email_check = DB::table('merchants')->where('email', $request->email)->first();
            if ($email_check) {

                $json_array = array(
                    'success' => 3,
                    'message' => "Email address already exist",
                );

                $response = $json_array;
                return json_encode($response);
            }
        }

        $file_name = "";
        if ($request->hasFile('contract')) {
            $this->validate($request, [
                'contract' => 'required|mimes:pdf|max:30720',
            ]);

            $contract = $request->file('contract');
            $file_name = 'contract' . time() . '.' . $contract->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/documents/');
            $contract->move($destinationPath, $file_name);
//            $contract_name = url('/') . '/api/v1/get-document/' . $file_name;
        }

        $cash_on_delivery_fee = 0;
        $enable_cash_on_delivery_fee = false;
        if($request->enable_cash_on_delivery_fee == "true"){
            $cash_on_delivery_fee = $request->cash_on_delivery_fee;
            $enable_cash_on_delivery_fee = true;
        }

        $delivery_fee_nairobi = 0;
        $enable_delivery_fee_nairobi = false;
        if($request->enable_delivery_fee_nairobi == "true"){
            $delivery_fee_nairobi = $request->delivery_fee_nairobi;
            $enable_delivery_fee_nairobi = true;
        }

        $delivery_fee_outbound = 0;
        $enable_delivery_fee_outbound = false;
        if($request->enable_delivery_fee_outbound == "true"){
            $delivery_fee_outbound = $request->delivery_fee_outbound;
            $enable_delivery_fee_outbound = true;
        }

        $enable_returns_management_fee = false;
        if($request->enable_returns_management_fee == "true"){
            $enable_returns_management_fee = true;
        }

        $warehousing_fee = 0;
        $enable_warehousing_fee = false;
        if($request->enable_warehousing_fee == "true"){
            $warehousing_fee = $request->warehousing_fee;
            $enable_warehousing_fee = true;
        }

        $packaging_fee = 0;
        $enable_packaging_fee = false;
        if($request->enable_packaging_fee == "true"){
            $packaging_fee = $request->packaging_fee;
            $enable_packaging_fee = true;
        }

        $call_centre_fee = 0;
        $enable_call_centre_fee = false;
        if($request->enable_call_centre_fee == "true"){
            $call_centre_fee = $request->call_centre_fee;
            $enable_call_centre_fee = true;
        }

        $label_printing_fee = 0;
        $enable_label_printing_fee = false;
        if($request->enable_label_printing_fee == "true"){
            $label_printing_fee = $request->label_printing_fee;
            $enable_label_printing_fee = true;
        }

        $enabled = false;
        if($request->enabled == "true"){
            $enabled = true;
        }

        $password_generator = new PasswordGeneratorUtil();
        $password = $password_generator->generatePassword();

        $merchant_object = new Merchant();
        $merchant_created = $merchant_object->create([
            'name' => $request->name,
            'merchant_type' => $request->merchant_type,
            'address' => $request->address,
            'phone_number' => $phone_number,
            'email' => $request->email,
            'country_id' => $request->country_id,
            'town_id' => $request->town_id,
            'enable_cash_on_delivery_fee' => $enable_cash_on_delivery_fee,
            'cash_on_delivery_fee' => $cash_on_delivery_fee,
            'enable_delivery_fee_nairobi' => $enable_delivery_fee_nairobi,
            'delivery_fee_nairobi' => $delivery_fee_nairobi,
            'enable_delivery_fee_outbound' => $enable_delivery_fee_outbound,
            'delivery_fee_outbound' => $delivery_fee_outbound,
            'enable_returns_management_fee' => $enable_returns_management_fee,
            'enable_warehousing_fee' => $enable_warehousing_fee,
            'warehousing_fee' => $warehousing_fee,
            'enable_packaging_fee' => $enable_packaging_fee,
            'packaging_fee' => $packaging_fee,
            'enable_call_centre_fee' => $enable_call_centre_fee,
            'call_centre_fee' => $call_centre_fee,
            'enable_label_printing_fee' => $enable_label_printing_fee,
            'label_printing_fee' => $label_printing_fee,
            'admin_id' => $request->admin_id,
            'merchant_prefix' => $request->merchant_prefix,
            'contract' => $file_name,
            'enabled' => $enabled,
            'password' => bcrypt($password),
        ]);

        if ($merchant_created) {

            // TODO Log merchant created
            $log_controller = new LogController();
            $log_controller->merchantCreateLog($request->causer_id, $merchant_created->id, $merchant_created->name);

            $email_util = new EmailUtil();
            $send_email = $email_util->merchantWelcomeEmail($merchant_created->name, $merchant_created->email, $password);

            $sms_message = "Hi $merchant_created->name!, You have been added as a merchant. We have sent the login credentials to your email";
            $sms_util = new SMSUtil();
            $sms_util->sendSMS($phone_number, $sms_message);

            // TODO Log merchant created
            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.merchant')
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

    }

    public function editMerchantDetails(Request $request)
    {
        $merchant_id = $request->id;
        $format_phone_number_util = new FormatPhoneNumberUtil();
        $phone_number = $format_phone_number_util->formatPhoneNumber($request->phone_number);

        //Check if phone exists
        $phone_number_check = DB::table('merchants')
            ->where('phone_number', $phone_number)
            ->first();
        if ($phone_number_check) {

            if ($phone_number_check->id != $merchant_id) {

                $json_array = array(
                    'success' => 3,
                    'message' => "Phone number already exist",
                );

                $response = $json_array;
                return json_encode($response);
            }

        }

        //Check if email exists
        if ($request->email != '') {
            $email_check = DB::table('merchants')->where('email', $request->email)->first();
            if ($email_check) {

                if ($email_check->id != $merchant_id) {

                    $json_array = array(
                        'success' => 3,
                        'message' => "Email address already exist",
                    );

                    $response = $json_array;
                    return json_encode($response);
                }

            }
        }

        //Check if email exists
        if ($request->merchant_prefix != '') {
            $email_check = DB::table('merchants')->where('merchant_prefix', $request->merchant_prefix)->first();
            if ($email_check) {

                if ($email_check->id != $merchant_id) {

                    $json_array = array(
                        'success' => 3,
                        'message' => "Merchant prefix already exist",
                    );

                    $response = $json_array;
                    return json_encode($response);
                }

            }
        }


        $contract_name = "";
        if ($request->hasFile('contract')) {
            $this->validate($request, [
                'contract' => 'required|mimes:pdf|max:30720',
            ]);

            $contract = $request->file('contract');
            $file_name = 'contract' . time() . '.' . $contract->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/documents/');
            $contract->move($destinationPath, $file_name);
            $contract_name = $file_name;
//            $contract_name = url('/') . '/api/v1/get-document/' . $file_name;
        }

        $cash_on_delivery_fee = 0;
        $enable_cash_on_delivery_fee = false;
        if($request->enable_cash_on_delivery_fee == "true" || $request->enable_cash_on_delivery_fee == 1){
            $cash_on_delivery_fee = $request->cash_on_delivery_fee;
            $enable_cash_on_delivery_fee = true;
        }

        $delivery_fee_nairobi = 0;
        $enable_delivery_fee_nairobi = false;
        if($request->enable_delivery_fee_nairobi == "true" || $request->enable_delivery_fee_nairobi == 1){
            $delivery_fee_nairobi = $request->delivery_fee_nairobi;
            $enable_delivery_fee_nairobi = true;
        }

        $delivery_fee_outbound = 0;
        $enable_delivery_fee_outbound = false;
        if($request->enable_delivery_fee_outbound == "true" || $request->enable_delivery_fee_outbound == 1){
            $delivery_fee_outbound = $request->delivery_fee_outbound;
            $enable_delivery_fee_outbound = true;
        }

        $enable_returns_management_fee = false;
        if($request->enable_returns_management_fee == "true" || $request->enable_returns_management_fee == 1){
            $enable_returns_management_fee = true;
        }

        $warehousing_fee = 0;
        $enable_warehousing_fee = false;
        if($request->enable_warehousing_fee == "true" || $request->enable_warehousing_fee == 1){
            $warehousing_fee = $request->warehousing_fee;
            $enable_warehousing_fee = true;
        }

        $packaging_fee = 0;
        $enable_packaging_fee = false;
        if($request->enable_packaging_fee == "true" || $request->enable_packaging_fee == 1){
            $packaging_fee = $request->packaging_fee;
            $enable_packaging_fee = true;
        }

        $call_centre_fee = 0;
        $enable_call_centre_fee = false;
        if($request->enable_call_centre_fee == "true" || $request->enable_call_centre_fee == 1){
            $call_centre_fee = $request->call_centre_fee;
            $enable_call_centre_fee = true;
        }

        $label_printing_fee = 0;
        $enable_label_printing_fee = false;
        if($request->enable_label_printing_fee == "true" || $request->enable_label_printing_fee == 1){
            $label_printing_fee = $request->label_printing_fee;
            $enable_label_printing_fee = true;
        }

        $enabled = false;
        if($request->enabled == "true" || $request->enabled == 1){
            $enabled = true;
        }

        if($contract_name == ""){

            $update = DB::table('merchants')
                ->where('id', $request->id)
                ->update([
                    'name' => $request->name,
                    'merchant_type' => $request->merchant_type,
                    'address' => $request->address,
                    'phone_number' => $phone_number,
                    'email' => $request->email,
                    'country_id' => $request->country_id,
                    'town_id' => $request->town_id,
                    'enable_cash_on_delivery_fee' => $enable_cash_on_delivery_fee,
                    'cash_on_delivery_fee' => $cash_on_delivery_fee,
                    'enable_delivery_fee_nairobi' => $enable_delivery_fee_nairobi,
                    'delivery_fee_nairobi' => $delivery_fee_nairobi,
                    'enable_delivery_fee_outbound' => $enable_delivery_fee_outbound,
                    'delivery_fee_outbound' => $delivery_fee_outbound,
                    'enable_returns_management_fee' => $enable_returns_management_fee,
                    'enable_warehousing_fee' => $enable_warehousing_fee,
                    'warehousing_fee' => $warehousing_fee,
                    'enable_packaging_fee' => $enable_packaging_fee,
                    'packaging_fee' => $packaging_fee,
                    'enable_call_centre_fee' => $enable_call_centre_fee,
                    'call_centre_fee' => $call_centre_fee,
                    'enable_label_printing_fee' => $enable_label_printing_fee,
                    'label_printing_fee' => $label_printing_fee,
                    'admin_id' => $request->admin_id,
                    'merchant_prefix' => $request->merchant_prefix,
                    'enabled' => $enabled,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }else{

            $update = DB::table('merchants')
                ->where('id', $request->id)
                ->update([
                    'name' => $request->name,
                    'merchant_type' => $request->merchant_type,
                    'address' => $request->address,
                    'phone_number' => $phone_number,
                    'email' => $request->email,
                    'country_id' => $request->country_id,
                    'town_id' => $request->town_id,
                    'enable_cash_on_delivery_fee' => $enable_cash_on_delivery_fee,
                    'cash_on_delivery_fee' => $cash_on_delivery_fee,
                    'enable_delivery_fee_nairobi' => $enable_delivery_fee_nairobi,
                    'delivery_fee_nairobi' => $delivery_fee_nairobi,
                    'enable_delivery_fee_outbound' => $enable_delivery_fee_outbound,
                    'delivery_fee_outbound' => $delivery_fee_outbound,
                    'enable_returns_management_fee' => $enable_returns_management_fee,
                    'enable_warehousing_fee' => $enable_warehousing_fee,
                    'warehousing_fee' => $warehousing_fee,
                    'enable_packaging_fee' => $enable_packaging_fee,
                    'packaging_fee' => $packaging_fee,
                    'enable_call_centre_fee' => $enable_call_centre_fee,
                    'call_centre_fee' => $call_centre_fee,
                    'enable_label_printing_fee' => $enable_label_printing_fee,
                    'label_printing_fee' => $label_printing_fee,
                    'admin_id' => $request->admin_id,
                    'merchant_prefix' => $request->merchant_prefix,
                    'contract' => $contract_name,
                    'enabled' => $enabled,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }



        if ($update) {

            $merchant = DB::table('merchants')
                ->where('id', $request->id)
                ->first();

            // TODO Log merchant update
            $log_controller = new LogController();
            $log_controller->merchantEditLog($request->causer_id, $merchant->id, $merchant->name);

            $json_array = array(
                'success' => 1,
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

    }

    public function deleteMerchantDetails(Request $request)
    {

        $update = DB::table('merchants')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $merchant = DB::table('merchants')
                ->where('id', $request->id)
                ->first();

            // TODO Log merchant update
            $log_controller = new LogController();
            $log_controller->merchantDeleteLog($request->causer_id, $merchant->id, $merchant->name);

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.merchant')
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

    }

    public function deleteProfileImage(Request $request)
    {

        $update = DB::table('merchants')
            ->where('id', $request->id)
            ->update([
                'profile_image' => '',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
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

    }

    public function deleteMerchantContract(Request $request)
    {

        $update = DB::table('merchants')
            ->where('id', $request->id)
            ->update([
                'contract' => '',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
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

    }

    public function resetPassword(Request $request)
    {

        $password_generator = new PasswordGeneratorUtil();
        $password = $password_generator->generatePassword();

        $update = DB::table('merchants')
            ->where('id', $request->id)
            ->update([
                'password' => bcrypt($password),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            // TODO - Send email with new credentials
            $merchant = DB::table('merchants')
                ->where('id', $request->id)
                ->first();


            $email_util = new EmailUtil();
            $send_email = $email_util->passwordResetEmail($merchant->name, $merchant->email, $password);

            $sms_message = "Hi $merchant->name !, Your password has been reset. We have sent a login credentials to your email";
            $sms_util = new SMSUtil();
            $sms_util->sendSMS($merchant->phone_number, $sms_message);

            $json_array = array(
                'success' => 1,
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

    }

    public function changePassword(Request $request){

        $merchant =  DB::table('merchants')
            ->where('id', $request->merchant_id)
            ->first();

        if($merchant) {

            if (Hash::check($request->current_password, $merchant->password)) {

                $update = DB::table('merchants')
                    ->where('id', $request->merchant_id)
                    ->update([
                        'password' => bcrypt($request->new_password),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                if($update) {

                    // TODO - Send email with new credential
                    $merchant =  DB::table('merchants')
                        ->where('id', $request->merchant_id)
                        ->first();

                    $email_util = new EmailUtil();
                    $send_email = $email_util->passwordResetEmail($merchant->name, $merchant->email, $request->new_password);

                    $sms_message = "Hi $merchant->name !, Your password has been changed. We have sent a login credentials to your email";
                    $sms_util = new SMSUtil();
                    $sms_util->sendSMS($merchant->phone_number, $sms_message);

                    $json_array = array(
                        'success' => 1,
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
                    'message' => 'Wrong password entered',
                );

                $response = $json_array;
                return json_encode($response);
            }

        }


    }
}
