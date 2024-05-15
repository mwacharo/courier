<?php

namespace App\Http\Controllers\Merchant;

use App\Exports\InventoryExport;
use App\Exports\MerchantInventoryExport;
use App\Exports\RiderExport;
use App\Http\Controllers\Controller;
use App\Imports\InventoryImport;
use App\Imports\MerchantImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:merchant');
    }

    public function index()
    {
        $id = Auth::id();
        $inventories = DB::table('inventories')
            ->where('merchant_id', $id)
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

        return view('merchant.inventory.index', $data);
    }


    public function reportExcel(){
        $id = Auth::id();
        return Excel::download(new MerchantInventoryExport($id), 'inventory-report-excel.xls');
    }

    public function reportPdf(){

        $id = Auth::id();
        $inventories = DB::table('inventories')
            ->where('merchant_id', $id)
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

        $pdf = PDF::loadView('merchant.report.inventory-report-pdf', $data);
        return $pdf->stream('merchant.report.inventory-report-pdf');
    }

}
