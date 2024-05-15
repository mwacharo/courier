<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $log_controller = new LogController();
        $log_controller->viewProfile();

        $user = Auth::user();
        $logs = DB::table('activity_log')
            ->where('causer_id', $user->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $data = [
            'logs' => $logs,
        ];

        return view('admin.profile.index', $data);
    }

}
