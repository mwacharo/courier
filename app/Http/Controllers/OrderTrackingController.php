<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderTrackingController extends Controller
{
    public function index(Request $request)
    {
        $order_no = $request->order_no;
        $order = DB::table('orders')
            ->where('order_no', $order_no)
            ->first();

        if($order){

            $data = [
                'order' => $order
            ];

            return view('tracking.index', $data);

        }else{

            $data = [
                'order_no' => $order_no
            ];

            return view('tracking.not-found', $data);
        }

    }

}
