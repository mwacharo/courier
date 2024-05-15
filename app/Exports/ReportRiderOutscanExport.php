<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class ReportRiderOutscanExport implements FromView
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
        $rider = DB::table('riders')
            ->where('id', $this->rider_id)
            ->first();

        return view('admin.report.report-rider-outscan-order-excel', [
            'rider' => $rider,
            'orders' =>  $this->orders
        ]);
    }
}
