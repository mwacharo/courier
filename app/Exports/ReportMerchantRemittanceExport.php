<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class ReportMerchantRemittanceExport implements FromView
{
    protected $orders;
    protected $merchant_id;
    public function __construct(array $orders, string $merchant_id)
    {
        $this->orders = $orders;
        $this->merchant_id = $merchant_id;
    }

    public function view(): View
    {
        $merchant = DB::table('merchants')
            ->where('id', $this->merchant_id)
            ->first();

        return view('admin.report.merchant-remittance-report-excel', [
            'merchant' => $merchant,
            'orders' => $this->orders
        ]);
    }
}
