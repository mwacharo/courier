<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\SMSController;
use App\Http\Controllers\Controller;
use App\PasswordResetCode;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function forgotPassword(){
        return view('auth.forgot-password');

    }

    public function sendVerificationToken(Request $request){

        /**
         * SEND EMAIL
         */

        $user =  DB::table('users')
            ->where('phone_number', $request->email)->first();

        if($user){

            $user_id = $user->id;
            $reset_code = rand(10000,99999);
            $password_reset_code = new PasswordResetCode();
            $code_created = $password_reset_code->create([
                'user_id'=>$user_id,
                'code'=>$reset_code,
                'status'=> false,
            ]);

            if($code_created){

                $data=[
                    'user_id' => $user_id,
                ];
                return view('auth.confirm-token', $data);


            }else{

                return redirect('forgot-password')->with('status', 'Failed to send verification token!');

            }

        } else {

            return redirect('forgot-password')->with('status', 'Account doesnt exists!');
        }


    }

    public function resetPassword(Request $request){

        $user_id = $request->user_id;
        $reset_code = $request->reset_code;
        $user =  DB::table('users')->where('id', $user_id)->first();

        if($user) {

            $user_id = $user->id;

            $conditions = ['user_id' => $user_id, 'code' => $reset_code];
            $check_reset=  DB::table('password_reset_codes')
                ->where($conditions)
                ->orderBy('created_at','DESC')
                ->first();

            if($check_reset){

                $status = $check_reset->status;
                if($status==0){

                    $update_password = DB::table('users')
                        ->where('id', $user_id)
                        ->update([ 'password'=> bcrypt($request->password)]);

                    if($update_password){

                        return view('auth.reset-password');

                    }else{

                        return redirect('forgot-password')->with('status', 'Failed to update password');
                    }


                }else{

                    return redirect('forgot-password')->with('status', 'Reset token invalid');
                }

            }else{

                return redirect('forgot-password')->with('status', 'Reset token invalid');

            }

        }else{

            return redirect('forgot-password')->with('status', 'Reset token invalid');
        }
    }
}
