<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\PasswordGeneratorUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\Inventory;
use App\InventoryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryHistoryApiController extends Controller
{
    public function getInventoryHistoryList(Request $request)
    {

        $inventories = DB::table('inventory_histories')
            ->where('deleted_at', null)
            ->get();

        return json_encode($inventories);

    }

    public function getInventoryHistoryDetails(Request $request)
    {

        $inventory = DB::table('inventory_histories')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($inventory);

    }

    public function createInventoryHistoryInscanDetails(Request $request)
    {
        //Check if sku exists
        $sku_check = DB::table('inventories')->where('sku', $request->sku)->first();
        if(!$sku_check) {

            $json_array = array(
                'success' => 3,
                'message' => "SKU doesnt exist",
            );

            $response = $json_array;
            return json_encode($response);
        }

        $balance = $sku_check->quantity + $request->quantity;
        $inventory_history_object = new InventoryHistory();
        $inventory_history_created = $inventory_history_object->create([
            'inventory_id' => $sku_check->id,
            'transaction_type' => 1,
            'quantity' => $request->quantity,
            'balance' => $balance,
            'admin_id' => $request->admin_id,
        ]);

        if ($inventory_history_created) {

            $update = DB::table('inventories')
                ->where('id', $sku_check->id)
                ->update([
                    'quantity' => $balance,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.inventory.inscan')
            );

            $response = $json_array;
            return json_encode($response);


        } else {

            $json_array = array(
                'success' => 0,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function createInventoryHistoryOutscanDetails(Request $request)
    {
        //Check if sku exists
        $sku_check = DB::table('inventories')->where('sku', $request->sku)->first();
        if(!$sku_check) {

            $json_array = array(
                'success' => 3,
                'message' => "SKU doesnt exist",
            );

            $response = $json_array;
            return json_encode($response);
        }

        if($request->quantity > $sku_check->quantity){

            $json_array = array(
                'success' => 3,
                'message' => "Quantity exceeds balance",
            );

            $response = $json_array;
            return json_encode($response);
        }

        $balance = $sku_check->quantity - $request->quantity;
        $inventory_history_object = new InventoryHistory();
        $inventory_history_created = $inventory_history_object->create([
            'inventory_id' => $sku_check->id,
            'transaction_type' => 2,
            'quantity' => $request->quantity,
            'balance' => $balance,
            'admin_id' => $request->admin_id,
        ]);

        if ($inventory_history_created) {

            $update = DB::table('inventories')
                ->where('id', $sku_check->id)
                ->update([
                    'quantity' => $balance,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.inventory.outscan')
            );

            $response = $json_array;
            return json_encode($response);


        } else {

            $json_array = array(
                'success' => 0,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }


    public function deleteInventoryHistoryDetails(Request $request)
    {

        $update = DB::table('inventory_histories')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.inventory.inscan')
            );

            $response = $json_array;
            return json_encode($response);

        } else {

            $json_array = array(
                'success' => 0,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }


}
