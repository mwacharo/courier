<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InventoryExport;
use App\Exports\RiderExport;
use App\Http\Controllers\Controller;
use App\Imports\InventoryImport;
use App\Imports\MerchantImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $log_controller = new LogController();
        $log_controller->inventoryListLog();

        $inventories = DB::table('inventories')
            ->where('deleted_at', null)
            ->latest()
            ->get();

        $inventory_result = array();
        foreach ($inventories as $inventory){

            // Get merchant
            $merchant_name = "";
            $merchant = DB::table('merchants')
                ->where('id', $inventory->merchant_id)
                ->first();
            if($merchant){
                $merchant_name = $merchant->name;
            }

            array_push($inventory_result,
                array(
                    'id' => $inventory->id,
                    'sku' => $inventory->sku,
                    'merchant_id' => $inventory->merchant_id,
                    'merchant_name' => $merchant_name,
                    'name' => $inventory->name,
                    'description' => $inventory->description,
                    'image' => $inventory->image,
                    'quantity' => $inventory->quantity,
                    'low_count' => $inventory->low_count,
                    'amount' => $inventory->amount,
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                ));

        }

        $data = [
            'inventories' => $inventory_result
        ];

        return view('admin.inventory.index', $data);
    }

    public function create()
    {
        return view('admin.inventory.inventory-create');
    }

    public function details(Request $request)
    {

        // Recent in-scan
        $inventory_histories = DB::table('inventory_histories')
            ->where('inventory_id', $request->id)
            ->where('deleted_at', null)
            ->latest()
            ->get();

        $inventory_histories_result = array();
        foreach ($inventory_histories as $inventory_history){

            $inventory = DB::table('inventories')
                ->where('id', $inventory_history->inventory_id)
                ->first();

            $merchant_name = "";
            $merchant = DB::table('merchants')
                ->where('id', $inventory->merchant_id)
                ->first();
            if($merchant){
                $merchant_name = $merchant->name;
            }

            $transaction_type = "";
            if($inventory_history->transaction_type == 1){
                $transaction_type = "INSCAN";
            }elseif ($inventory_history->transaction_type == 2){
                $transaction_type= "OUTSCAN";
            }
            array_push($inventory_histories_result,
                array(
                    'id' => $inventory_history->id,
                    'inventory_id' => $inventory_history->id,
                    'sku' => $inventory->sku,
                    'merchant_id' => $inventory->merchant_id,
                    'merchant_name' => $merchant_name,
                    'name' => $inventory->name,
                    'description' => $inventory->description,
                    'image' => $inventory->image,
                    'quantity' => $inventory->quantity,
                    'balance' => $inventory_history->balance,
                    'low_count' => $inventory->low_count,
                    'amount' => $inventory->amount,
                    'transaction_type' => $transaction_type,
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                ));

        }

        $inventory = DB::table('inventories')
            ->where('id', $request->id)
            ->first();

        $log_controller = new LogController();
        $log_controller->inventoryDetailsLog($request->id, $inventory->name);

        $data = [
            'id' => $request->id,
            'inventory_histories' => $inventory_histories_result
        ];
        return view('admin.inventory.inventory-details', $data);
    }

    public function reportExcel(){
        return Excel::download(new InventoryExport, 'inventory-report-excel.xls');
    }

    public function reportPdf(){

        $inventories = DB::table('inventories')
            ->where('deleted_at', null)
            ->latest()
            ->get();

        $inventory_result = array();
        foreach ($inventories as $inventory){

            // Get merchant
            $merchant_name = "";
            $merchant = DB::table('merchants')
                ->where('id', $inventory->merchant_id)
                ->first();
            if($merchant){
                $merchant_name = $merchant->name;
            }

            array_push($inventory_result,
                array(
                    'id' => $inventory->id,
                    'sku' => $inventory->sku,
                    'merchant_id' => $inventory->merchant_id,
                    'merchant_name' => $merchant_name,
                    'name' => $inventory->name,
                    'description' => $inventory->description,
                    'image' => $inventory->image,
                    'quantity' => $inventory->quantity,
                    'low_count' => $inventory->low_count,
                    'spoilt' => $inventory->spoilt,
                    'amount' => $inventory->amount,
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                ));

        }

        $data = [
            'inventories' => $inventory_result
        ];

        $pdf = PDF::loadView('admin.report.inventory-report-pdf', $data);
        return $pdf->stream('admin.report.inventory-report-pdf');
    }

    public function importExcel()
    {
        $merchants = DB::table('merchants')
            ->orderBy('name', 'ASC')
            ->get();
        $data = [
            'merchants' => $merchants
        ];

        return view('admin.inventory.inventory-import', $data);
    }

    public function importExcelUpload(Request $request)
    {
        $this->validate($request, [
            'select_file'  => 'required|mimes:xls,xlsx'
        ]);

        $path1 = $request->file('select_file')->store('temp');
        $path=storage_path('app').'/'.$path1;

        $merchant_id = $request->merchant_id;
        $import = new InventoryImport($merchant_id);
        Excel::import($import, $path);

        if($import->getErrorMessage() == ''){
            return back()->with('success', 'Excel Data Imported successfully.' . $import->getRowCount() . ' rows imported');
        }else{
            return back()->with('error', 'Import Errors.' . $import->getErrorMessage());
        }

    }
}
