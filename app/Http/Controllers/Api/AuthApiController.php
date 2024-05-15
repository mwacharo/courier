<?php

namespace App\Http\Controllers\Api;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthApiController extends Controller
{
    public function integrationLogin(Request $request){

        $email = $request->email;
        $password = $request->password;

        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $password], $request->remember)){
            $admin = Admin::query()->where('email', $email)->first();
            $json_array = array(
                'status' => 'success',
                'message' => 'Auth Successful',
                'crmSessionId' => Session::getId(),
                'user_id' => $admin->id
            );

            $response = $json_array;
            return json_encode($response);

        }else{

            $json_array = array(
                'status' => 'failed',
                'message' => 'Invalid email or password',
                'crmSessionId' => '',
                'userId' => ''
            );

            $response = $json_array;
            return json_encode($response);
        }
    }

    public function integrationLogout(Request $request){

        $id = $request->userId;
        $userToLogout = Admin::find($id);
        Auth::setUser($userToLogout);
        Auth::logout();

        $json_array = array(
            'status' => 'success',
            'message' => 'Auth successfully logout',
            'crmSessionId' => ''
        );

        $response = $json_array;
        return json_encode($response);

    }
}
