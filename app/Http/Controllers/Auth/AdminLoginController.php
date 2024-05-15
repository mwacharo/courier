<?php

namespace App\Http\Controllers\Auth;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Util\EmailUtil;
use Illuminate\Database\Eloquent\Model;
 
use App\Admin;
// use App\Models\Admin; // Import the "\App\Models\Admin" class




class AdminLoginController extends Controller
{
    public function __construct()
    {
        //defining our middleware for this controller
        $this->middleware('guest:admin',['except' => ['logout']]);
    }

    //function to show admin login form
    public function showLoginForm() {
        return view('auth.admin-login');
    }

    public function resetPassword(Request $request) {

        $admin =  DB::table('admins')->where('email', $request->email)->first();
        if($admin) {

            $password = $this->generateRandomString();
            $email = $admin->email;
            $admin_id = $admin->id;
            $update_password = DB::table('admins')
                ->where('id', $admin_id)
                ->update([ 'password'=> bcrypt($password)]);

            if($update_password){

                //SEND EMAIL
                $email_util = new EmailUtil();
                $email_util->passwordResetEmail($admin->first_name, $email, $password);

                return redirect()->back()->with('message', 'Password has been reset. Check your email for your new password');

            }else{

                return redirect()->back()->with('message', 'Failed to reset password.');

            }
        }else{

            return redirect()->back()->with('message', 'No account matches the email provided.');

        }


    }

    //function to login admins
    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'

        ]);

        // Attempt to log in the admin
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            // Update the admin's online status and login count
            $admin = Auth::guard('admin')->user();
            // $admin->is_online = 1;
            // $admin->is_offline = 0;
            // $admin->login_count += 1;
            // $admin->login_time =  date('Y-m-d H:i:s');
            // $admin->save();

            // Redirect to the admin dashboard
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->back()->with('message', 'Wrong email or password.');
    }


    public function logout()
{
    $admin = Auth::guard('admin')->user();


    if ($admin) {
         DB::table('admins')
        ->where('id', $admin->id)
        ->update([
            'is_online' => 0,
            'is_offline' => 1,
            'last_login' => date('Y-m-d H:i:s'),

        ]);
        Auth::guard('admin')->logout();
    }

    return redirect('/')->with('success', 'Logged out successfully.');
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
