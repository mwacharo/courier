<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class ReportAllRiderOutscanExport implements FromView
{
    protected $orders;
    protected $rider_id;
    public function __construct(array $orders, string $rider_id)
    {
        $this->orders = $orders;
        $this->rider_id = $rider_id;
    }

    public function view(): View

    {

       


        return view('admin.report.report-all-rider-outscan-order-excel', [

            'orders' =>  $this->orders
        ]);
    }
}
