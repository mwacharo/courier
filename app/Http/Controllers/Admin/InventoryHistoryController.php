<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function inscan(Request $request)
    {
        // Recent in-scan
        $inventory_histories = DB::table('inventory_histories')
            ->where('transaction_type', 1)
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
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                ));

        }

        $data = [
            'inventory_histories' => $inventory_histories_result
        ];
        return view('admin.inventory.inventory-inscan', $data);
    }

    public function outscan(Request $request)
    {
        // Recent in-scan
        $inventory_histories = DB::table('inventory_histories')
            ->where('transaction_type', 2)
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
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                ));

        }

        $data = [
            'inventory_histories' => $inventory_histories_result
        ];
        return view('admin.inventory.inventory-outscan', $data);
    }
}
