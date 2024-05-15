<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class InventoryExport implements FromView
{
    public function view(): View
    {

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

        return view('admin.report.inventory-report-excel', [
            'inventories' => $inventory_result
        ]);
    }
}
