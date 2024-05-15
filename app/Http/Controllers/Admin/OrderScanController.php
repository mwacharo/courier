<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderScanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function inscan()
    {
        return view('admin.order.order-inscan');
    }

    public function outscan()
    {
        return view('admin.order.order-outscan');
    }

}
