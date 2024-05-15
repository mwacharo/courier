<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Util\EmailUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Auth;

class MerchantLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:merchant',['except' => ['logout']]);
    }

    //function to show merchant login form
    public function showLoginForm() {
        return view('auth.merchant-login');
    }

    public function resetPassword(Request $request) {

        $merchant =  DB::table('merchants')->where('email', $request->email)->first();
        if($merchant) {

            $password = $this->generateRandomString();
            $email = $merchant->email;
            $merchant_id = $merchant->id;
            $update_password = DB::table('merchants')
                ->where('id', $merchant_id)
                ->update([ 'password'=> bcrypt($password)]);

            if($update_password){

                //SEND EMAIL
                $email_util = new EmailUtil();
                $send_email = $email_util->passwordResetEmail($merchant->name, $email, $password);

                return redirect()->back()->with('message', 'Password has been reset. Check your email for your new password');

            }else{

                return redirect()->back()->with('message', 'Failed to reset password.');

            }
        }else{

            return redirect()->back()->with('message', 'No account matches the email provided.');

        }


    }

    //function to login merchants
    public function login(Request $request) {
        //validate the form data
        $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        //attempt to login the merchants in
        if (Auth::guard('merchant')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)){
            //if successful redirect to merchant dashboard
            return redirect()->intended(route('merchant.dashboard'));
        }

        return redirect()->back()->with('message', 'Wrong email or password.');
    }

    public function logout()
    {
        Auth::guard('merchant')->logout();

        return redirect('/');
    }

    function generateRandomString(){
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $charsLength = strlen($characters) -1;
        $string = "";
        for($i=0; $i<6; $i++){
            $randNum = mt_rand(0, $charsLength);
            $string .= $characters[$randNum];
        }
        return $string;
    }

}
