<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MerchantExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoogleSheetController;
use App\Imports\MerchantImport;
use App\Imports\RiderImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class MerchantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $log_controller = new LogController();
        $log_controller->merchantListLog();

        $merchants = DB::table('merchants')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get();

        $merchant_result = array();
        foreach ($merchants as $merchant){

            // Get country
            $country_name = "";
            $country = DB::table('countries')
                ->where('id', $merchant->country_id)
                ->first();
            if($country){
                $country_name = $country->name;
            }

            // Get town
            $town_name = "";
            $town = DB::table('towns')
                ->where('id', $merchant->town_id)
                ->first();
            if($town){
                $town_name = $town->name;
            }

            array_push($merchant_result,
                array(
                    'id' => $merchant->id,
                    'name' => $merchant->name,
                    'address' => $merchant->address,
                    'phone_number' => $merchant->phone_number,
                    'email' => $merchant->email,
                    'enable_cash_on_delivery_fee' => $merchant->enable_cash_on_delivery_fee,
                    'cash_on_delivery_fee' => $merchant->cash_on_delivery_fee,
                    'enable_delivery_fee_nairobi' => $merchant->enable_delivery_fee_nairobi,
                    'delivery_fee_nairobi' => $merchant->delivery_fee_nairobi,
                    'enable_delivery_fee_outbound' => $merchant->enable_delivery_fee_outbound,
                    'delivery_fee_outbound' => $merchant->delivery_fee_outbound,
                    'enable_returns_management_fee' => $merchant->enable_returns_management_fee,
                    'enable_warehousing_fee' => $merchant->enable_warehousing_fee,
                    'warehousing_fee' => $merchant->warehousing_fee,
                    'enable_packaging_fee' => $merchant->enable_packaging_fee,
                    'packaging_fee' => $merchant->packaging_fee,
                    'enable_call_centre_fee' => $merchant->enable_call_centre_fee,
                    'call_centre_fee' => $merchant->call_centre_fee,
                    'enable_label_printing_fee' => $merchant->enable_label_printing_fee,
                    'label_printing_fee' => $merchant->label_printing_fee,
                    'contract' => $merchant->contract,
                    'enabled' => $merchant->enabled,
                    'country_id' => $merchant->country_id,
                    'country_name' => $country_name,
                    'town_id' => $merchant->town_id,
                    'town_name' => $town_name,
                    'created_at' => $merchant->created_at,
                    'google_sheet' => $merchant->google_sheet,
                    'updated_at' => $merchant->updated_at,
                ));

        }

        $data = [
            'merchants' => $merchant_result
        ];

        return view('admin.merchant.index', $data);
    }

    public function create()
    {
        return view('admin.merchant.merchant-create');
    }

    public function details(Request $request)
    {

        $merchant_id = $request->id;
        $merchant = DB::table('merchants')
            ->where('id', $merchant_id)
            ->first();

        $log_controller = new LogController();
        $log_controller->merchantDetailsLog($merchant_id, $merchant->name);

        $order_pending = DB::table('orders')
            ->where('merchant_id', $merchant_id)
            ->where('order_status', 'order_pending')
            ->count();

        $order_delivered = DB::table('orders')
            ->where('merchant_id', $merchant_id)
            ->where('order_status', 'delivered')
            ->count();

        $order_returned = DB::table('orders')
            ->where('merchant_id', $merchant_id)
            ->where('order_status', 'returned')
            ->count();

        $order_total = DB::table('orders')
            ->where('merchant_id', $merchant_id)
            ->count();

        $data = [
            'id' => $merchant_id,
            'order_pending' => $order_pending,
            'order_delivered' => $order_delivered,
            'order_returned' => $order_returned,
            'order_total' => $order_total,
        ];
        return view('admin.merchant.merchant-details', $data);
    }

    public function reportExcel(){
        return Excel::download(new MerchantExport, 'merchant-report-excel.xls');
    }

    public function reportPdf(){

        $merchants = DB::table('merchants')
            ->where('deleted_at', null)
            ->get();

        $merchant_result = array();
        foreach ($merchants as $merchant){

            // Get country
            $country_name = "";
            $country = DB::table('countries')
                ->where('id', $merchant->country_id)
                ->first();
            if($country){
                $country_name = $country->name;
            }

            // Get town
            $town_name = "";
            $town = DB::table('towns')
                ->where('id', $merchant->town_id)
                ->first();
            if($town){
                $town_name = $town->name;
            }

            array_push($merchant_result,
                array(
                    'id' => $merchant->id,
                    'name' => $merchant->name,
                    'address' => $merchant->address,
                    'phone_number' => $merchant->phone_number,

                    'email' => $merchant->email,
                    'enable_cash_on_delivery_fee' => $merchant->enable_cash_on_delivery_fee,
                    'cash_on_delivery_fee' => $merchant->cash_on_delivery_fee,
                    'enable_delivery_fee_nairobi' => $merchant->enable_delivery_fee_nairobi,
                    'delivery_fee_nairobi' => $merchant->delivery_fee_nairobi,
                    'enable_delivery_fee_outbound' => $merchant->enable_delivery_fee_outbound,
                    'delivery_fee_outbound' => $merchant->delivery_fee_outbound,
                    'enable_returns_management_fee' => $merchant->enable_returns_management_fee,

                    'enable_warehousing_fee' => $merchant->enable_warehousing_fee,
                    'warehousing_fee' => $merchant->warehousing_fee,
                    'enable_packaging_fee' => $merchant->enable_packaging_fee,
                    'packaging_fee' => $merchant->packaging_fee,
                    'enable_call_centre_fee' => $merchant->enable_call_centre_fee,
                    'call_centre_fee' => $merchant->call_centre_fee,
                    'enable_label_printing_fee' => $merchant->enable_label_printing_fee,
                    'label_printing_fee' => $merchant->label_printing_fee,
                    'contract' => $merchant->contract,
                    'enabled' => $merchant->enabled,
                    'country_id' => $merchant->country_id,
                    'country_name' => $country_name,
                    'town_id' => $merchant->town_id,
                    'town_name' => $town_name,
                    'created_at' => $merchant->created_at,
                    'updated_at' => $merchant->updated_at,
                ));

        }

        $data = [
            'merchants' => $merchant_result
        ];

        $pdf = PDF::loadView('admin.report.merchant-report-pdf', $data);
        return $pdf->stream('admin.report.merchant-report-pdf');
    }

    public function importExcel()
    {
        return view('admin.merchant.merchant-import');
    }

    public function importExcelUpload(Request $request)
    {
        $this->validate($request, [
            'select_file'  => 'required|mimes:xls,xlsx'
        ]);

        $path1 = $request->file('select_file')->store('temp');
        $path=storage_path('app').'/'.$path1;

        $import = new MerchantImport();
        Excel::import($import, $path);

        if($import->getErrorMessage() == ''){
            return back()->with('success', 'Excel Data Imported successfully.' . $import->getRowCount() . ' rows imported');
        }else{
            return back()->with('error', 'Import Errors.' . $import->getErrorMessage());
        }

    }

    public function importGoogleExcelSubmit(Request $request)
    {

        $merchant_id = $request->id;
        $sheet = $request->sheet;
        $sheet_controller = new GoogleSheetController();
        $sheet_controller->importOrders($merchant_id, $sheet);
        return back()->with('success', 'Excel Data Imported successfully.');

    }
}
