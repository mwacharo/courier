<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Util\EmailUtil;
use App\Rider;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\PasswordGeneratorUtil;
use App\Http\Controllers\Util\SMSUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiderApiController extends Controller
{

    public function getRiderList(Request $request)
    {

        $riders = DB::table('riders')
            ->where('deleted_at', null)
            ->get();

        return json_encode($riders);

    }

    public function getRiderDetails(Request $request)
    {

        $rider = DB::table('riders')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($rider);

    }

    public function createRiderDetails(Request $request)
    {
        $format_phone_number_util = new FormatPhoneNumberUtil();
        $phone_number = $format_phone_number_util->formatPhoneNumber($request->phone_number);

        //Check if phone exists
        $phone_number_check = DB::table('riders')->where('phone_number', $phone_number)->first();
        if ($phone_number_check) {

            $json_array = array(
                'success' => 3,
                'message' => "Phone number already exist",
            );

            $response = $json_array;
            return json_encode($response);
        }

        //Check if email exists
        if ($request->email != '') {
            $email_check = DB::table('riders')->where('email', $request->email)->first();
            if ($email_check) {

                $json_array = array(
                    'success' => 3,
                    'message' => "Email address already exist",
                );

                $response = $json_array;
                return json_encode($response);
            }
        }

        //Check if national ID exists
        if ($request->national_id != '') {
            $national_id_check = DB::table('riders')->where('national_id', $request->national_id)->first();
            if ($national_id_check) {

                $json_array = array(
                    'success' => 3,
                    'message' => "National ID already exist",
                );

                $response = $json_array;
                return json_encode($response);
            }
        }


        $image_name = "";
        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            ]);

            $image = $request->file('image');
            $file_name = 'rider' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/images/');
            $image->move($destinationPath, $file_name);
            $image_name = url('/') . '/api/v1/get-image/' . $file_name;
        }

        $enabled = false;
        if($request->enabled == "true" || $request->enabled == 1){
            $enabled = true;
        }

        $password_generator = new PasswordGeneratorUtil();
        $password = $password_generator->generatePassword();

        $rider_object = new Rider();
        $rider_created = $rider_object->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'national_id' => $request->national_id,
            'country_id' => $request->country_id,
            'profile_image' => $image_name,
            'phone_number' => $phone_number,
            'email' => $request->email,
            'enabled' => $enabled,
            'password' => bcrypt($password),
        ]);

        if ($rider_created) {

            // TODO Log merchant created
            $log_controller = new LogController();
            $log_controller->riderCreateLog($request->causer_id, $rider_created->id, $rider_created->first_name);

            $email_util = new EmailUtil();
            $send_email = $email_util->riderWelcomeEmail($rider_created->first_name, $rider_created->email, $password);

            $sms_message = "Hi $rider_created->first_name!, You have been added as a delivery agent. We have sent the login credentials to your email";
            $sms_util = new SMSUtil();
            $sms_util->sendSMS($phone_number, $sms_message);

            // TODO Log rider created
            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.rider')
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

    public function editRiderDetails(Request $request)
    {
        $rider_id = $request->id;
        $format_phone_number_util = new FormatPhoneNumberUtil();
        $phone_number = $format_phone_number_util->formatPhoneNumber($request->phone_number);

        //Check if phone exists
        $phone_number_check = DB::table('riders')
            ->where('phone_number', $phone_number)
            ->first();
        if ($phone_number_check) {

            if ($phone_number_check->id != $rider_id) {

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
            $email_check = DB::table('riders')->where('email', $request->email)->first();
            if ($email_check) {

                if ($email_check->id != $rider_id) {

                    $json_array = array(
                        'success' => 3,
                        'message' => "Email address already exist",
                    );

                    $response = $json_array;
                    return json_encode($response);
                }

            }
        }

        //Check if national ID exists
        if ($request->national_id != '') {
            $national_id_check = DB::table('riders')->where('national_id', $request->national_id)->first();
            if ($national_id_check) {

                if ($national_id_check->id != $rider_id) {

                    $json_array = array(
                        'success' => 3,
                        'message' => "National ID already exist",
                    );

                    $response = $json_array;
                    return json_encode($response);
                }


            }
        }

        $image_name = "";
        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            ]);

            $image = $request->file('image');
            $file_name = 'rider' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/images/');
            $image->move($destinationPath, $file_name);
            $image_name = url('/') . '/api/v1/get-image/' . $file_name;
        }

        $enabled = false;
        if($request->enabled == "true" || $request->enabled == 1){
            $enabled = true;
        }

        $update = DB::table('riders')
            ->where('id', $request->id)
            ->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'national_id' => $request->national_id,
                'country_id' => $request->country_id,
                'profile_image' => $image_name,
                'phone_number' => $phone_number,
                'email' => $request->email,
                'enabled' => $enabled,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $rider = DB::table('riders')
                ->where('id', $request->id)
                ->first();

            // TODO Log rider update
            $log_controller = new LogController();
            $log_controller->riderEditLog($request->causer_id, $rider->id, $rider->first_name);

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

    public function deleteRiderDetails(Request $request)
    {

        $update = DB::table('riders')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $rider = DB::table('riders')
                ->where('id', $request->id)
                ->first();

            // TODO Log rider update
            $log_controller = new LogController();
            $log_controller->riderDeleteLog($request->causer_id, $rider->id, $rider->first_name);

            // TODO - Redirect route after delete
            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.rider')
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

        $update = DB::table('riders')
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

    public function resetPassword(Request $request)
    {

        $password_generator = new PasswordGeneratorUtil();
        $password = $password_generator->generatePassword();

        $update = DB::table('riders')
            ->where('id', $request->id)
            ->update([
                'password' => bcrypt($password),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            // TODO - Send email with new credentials
            $rider = DB::table('riders')
                ->where('id', $request->id)
                ->first();

            $email_util = new EmailUtil();
            $email_util->passwordResetEmail($rider->first_name, $rider->email, $password);

            $sms_message = "Hi $rider->first_name !, Your password has been reset. We have sent a login credentials to your email";
            $sms_util = new SMSUtil();
            $sms_util->sendSMS($rider->phone_number, $sms_message);

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


}
