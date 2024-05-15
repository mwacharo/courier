<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:merchant');
    }

    public function merchantReport()
    {
        $id = Auth::id();
        $data = [
            'id' => $id
        ];
        return view('merchant.report.merchant-report', $data);
    }

}

