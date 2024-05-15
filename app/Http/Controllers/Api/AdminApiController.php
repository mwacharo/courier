<?php

namespace App\Http\Controllers\Api;
namespace App\Models;


use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Util\SMSUtil;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\PasswordGeneratorUtil;

class AdminApiController extends Controller
{

    public function getAdminList(){

        $admins = DB::table('admins')
            ->where('deleted_at', null)
            ->orderBy('first_name', 'ASC')
            ->get();

        return json_encode($admins);
    }

    public function getAdminDetails(Request $request)
    {

        $admin = DB::table('admins')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($admin);

    }

    public function createAdminDetails(Request $request)
    {
        $format_phone_number_util = new FormatPhoneNumberUtil();
        $phone_number = $format_phone_number_util->formatPhoneNumber($request->phone_number);

        //Check if phone exists
        $phone_number_check = DB::table('admins')->where('phone_number', $phone_number)->first();
        if ($phone_number_check) {

            $json_array = [
                'success' => 3,
                'message' => "Phone number already exist",
            ];

            $response = $json_array;
            return json_encode($response);
        }

        //Check if email exists
        if ($request->email != '') {
            $email_check = DB::table('admins')->where('email', $request->email)->first();
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
            $national_id_check = DB::table('admins')->where('national_id', $request->national_id)->first();
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
            $file_name = 'admin' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/images/');
            $image->move($destinationPath, $file_name);
            $image_name = url('/') . '/api/v1/get-image/' . $file_name;
        }

        $enabled = false;
        if($request->enabled == "true"){
            $enabled = true;
        }

        $password_generator = new PasswordGeneratorUtil();
        $password = $password_generator->generatePassword();

        $admin_object = new Admin();
        $admin_created = $admin_object->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'national_id' => $request->national_id,
            'profile_image' => $image_name,
            'phone_number' => $phone_number,
            'email' => $request->email,
            'role' => $request->role,
            'enabled' => $enabled,
            'password' => bcrypt($password),
        ]);

        if ($admin_created) {

            $permissions = [];

            if ($request->role == 1) {

                $role = "Super administrator";

                $permissions = [
                    'admin-list',
                    'admin-details',
                    'admin-create',
                    'admin-edit',
                    'admin-delete',
                    'admin-import',
                    'admin-export',
                    'admin-reports',
                    'admin-permission',
                    'merchant-list',
                    'merchant-details',
                    'merchant-create',
                    'merchant-edit',
                    'merchant-delete',
                    'merchant-import',
                    'merchant-export',
                    'merchant-reports',
                    'rider-list',
                    'rider-details',
                    'rider-create',
                    'rider-edit',
                    'rider-delete',
                    'rider-import',
                    'rider-export',
                    'rider-reports',
                    'branch-list',
                    'branch-details',
                    'branch-create',
                    'branch-edit',
                    'branch-delete',
                    'branch-import',
                    'branch-export',
                    'branch-reports',
                    'country-list',
                    'country-details',
                    'country-create',
                    'country-edit',
                    'country-delete',
                    'country-import',
                    'country-export',
                    'country-reports',
                    'town-list',
                    'town-details',
                    'town-create',
                    'town-edit',
                    'town-delete',
                    'town-import',
                    'town-export',
                    'town-reports',
                    'finance-reports',

                ];

            } elseif ($request->role == 2) {

                $role = "Administrator";
                $permissions = [
                    'admin-list',
                    'admin-details',
                    'admin-create',
                    'admin-edit',
                    'admin-delete',
                    'admin-import',
                    'admin-export',
                    'admin-reports',
                    'admin-permission',
                    'merchant-list',
                    'merchant-details',
                    'merchant-create',
                    'merchant-edit',
                    'merchant-delete',
                    'merchant-import',
                    'merchant-export',
                    'merchant-reports',
                    'rider-list',
                    'rider-details',
                    'rider-create',
                    'rider-edit',
                    'rider-delete',
                    'rider-import',
                    'rider-export',
                    'rider-reports',
                    'branch-list',
                    'branch-details',
                    'branch-create',
                    'branch-edit',
                    'branch-delete',
                    'branch-import',
                    'branch-export',
                    'branch-reports',
                    'country-list',
                    'country-details',
                    'country-create',
                    'country-edit',
                    'country-delete',
                    'country-import',
                    'country-export',
                    'country-reports',
                    'town-list',
                    'town-details',
                    'town-create',
                    'town-edit',
                    'town-delete',
                    'town-import',
                    'town-export',
                    'town-reports',
                    'finance-reports',
                ];

            } elseif ($request->role == 3) {

                $role = "Operations";
                $permissions = [

                ];

            } elseif ($request->role == 4) {

                $role = "Customer service";
                $permissions = [

                ];

            } elseif ($request->role == 5) {

                $role = "Finance";
                $permissions = [
                    'finance-reports',
                ];
            }

            // TODO Log admin created
            $log_controller = new LogController();
            $log_controller->adminCreateLog($request->causer_id, $admin_created->id, $admin_created->first_name);

            $email_util = new EmailUtil();
            $send_email = $email_util->adminWelcomeEmail($admin_created->first_name, $admin_created->email, $password);

            $admin_created->syncPermissions($permissions);
            $sms_message = "Hi $admin_created->first_name !, You have been added as an administrator($role). We have sent a login credentials to your email";
            $sms_util = new SMSUtil();
            $sms_util->sendSMS($phone_number, $sms_message);

            $json_array = [
                'success' => 1,
                'redirect' => route('admin.admin')
            ];

            $response = $json_array;
            return json_encode($response);

        } else {

            $json_array = [
                'success' => 0,
            ];

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function editAdminDetails(Request $request)
    {
        $admin_id = $request->id;
        $format_phone_number_util = new FormatPhoneNumberUtil();
        $phone_number = $format_phone_number_util->formatPhoneNumber($request->phone_number);

        //Check if phone exists
        $phone_number_check = DB::table('admins')
            ->where('phone_number', $phone_number)
            ->first();
        if ($phone_number_check) {

            if($phone_number_check->id != $admin_id){

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
            $email_check = DB::table('admins')->where('email', $request->email)->first();
            if ($email_check) {

                if($email_check->id != $admin_id){

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
            $national_id_check = DB::table('admins')->where('national_id', $request->national_id)->first();
            if ($national_id_check) {

                if($national_id_check->id != $admin_id){

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
            $file_name = 'admin' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/images/');
            $image->move($destinationPath, $file_name);
            $image_name = url('/') . '/api/v1/get-image/' . $file_name;
        }

        $enabled = false;
        if($request->enabled == "true" || $request->enabled == 1){
            $enabled = true;
        }

        $update = DB::table('admins')
            ->where('id', $request->id)
            ->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'national_id' => $request->national_id,
                'profile_image' => $image_name,
                'phone_number' => $phone_number,
                'email' => $request->email,
                'role' => $request->role,
                'enabled' => $enabled,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $admin = DB::table('admins')
                ->where('id', $request->id)
                ->first();

            // TODO Log admin update
            $log_controller = new LogController();
            $log_controller->adminEditLog($request->causer_id, $admin->id, $admin->first_name);

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

    public function deleteAdminDetails(Request $request){

        $update = DB::table('admins')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            // TODO Log admin update
            $admin = DB::table('admins')
                ->where('id', $request->id)
                ->first();

            $log_controller = new LogController();
            $log_controller->adminDeleteLog($request->causer_id, $admin->id, $admin->first_name);

            // TODO - Redirect route after delete
            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.admin')
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

    public function deleteProfileImage(Request $request){

        $update = DB::table('admins')
            ->where('id', $request->id)
            ->update([
                'profile_image' => '',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

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

    public function resetPassword(Request $request){

        $password_generator = new PasswordGeneratorUtil();
        $password = $password_generator->generatePassword();

        $update = DB::table('admins')
            ->where('id', $request->id)
            ->update([
                'password' => bcrypt($password),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            // TODO - Send email with new credential
            $admin =  DB::table('admins')
                ->where('id', $request->id)
                ->first();

            $email_util = new EmailUtil();
            $send_email = $email_util->passwordResetEmail($admin->first_name, $admin->email, $password);

            $sms_message = "Hi $admin->first_name !, Your password has been reset. We have sent a login credentials to your email";
            $sms_util = new SMSUtil();
            $sms_util->sendSMS($admin->phone_number, $sms_message);

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

        $admin =  DB::table('admins')
            ->where('id', $request->admin_id)
            ->first();

        if($admin) {

            if (Hash::check($request->current_password, $admin->password)) {

                $update = DB::table('admins')
                    ->where('id', $request->admin_id)
                    ->update([
                        'password' => bcrypt($request->new_password),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                if($update) {

                    // TODO - Send email with new credential
                    $admin =  DB::table('admins')
                        ->where('id', $request->admin_id)
                        ->first();

                    $email_util = new EmailUtil();
                    $send_email = $email_util->passwordResetEmail($admin->first_name, $admin->email, $request->new_password);

                    $sms_message = "Hi $admin->first_name !, Your password has been reset. We have sent a login credentials to your email";
                    $sms_util = new SMSUtil();
                    $sms_util->sendSMS($admin->phone_number, $sms_message);

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

    public function adminPermissionsEdit(Request $request)
    {

        Artisan::call('cache:clear');
        $id = $request->id;

        $delete = DB::table('model_has_permissions')
            ->where('model_id', $id)
            ->delete();

        $post_permissions = $request->input('user_permissions');
        $user = Admin::where('id', $id)
            ->first();
        $user->syncPermissions($post_permissions);

        /**
         *  Permissions
         */
        $permissions = DB::table('permissions')
            ->get();
        $result = array();
        foreach ($permissions as $permission){

            // Check if user has permission
            $has_permission = 0;
            $user_permission = DB::table('model_has_permissions')
                ->where('model_id', $id)
                ->where('permission_id', $permission->id)
                ->first();
            if($user_permission){
                $has_permission = 1;
            }

            array_push($result,
                array(
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'guard_name' => $permission->guard_name,
                    'has_permission' => $has_permission,
                ));

        }

        $json_array = array(
            'success' => 1,
        );

        $response = $json_array;
        return json_encode($response);

    }

}

