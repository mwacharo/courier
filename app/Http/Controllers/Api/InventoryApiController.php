<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\PasswordGeneratorUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\Inventory;
use App\InventoryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryApiController extends Controller
{
    public function getInventoryList(Request $request)
    {

        $inventories = DB::table('inventories')
            ->where('deleted_at', null)
            ->get();

        return json_encode($inventories);

    }

    public function getInventoryDetails(Request $request)
    {

        $inventory = DB::table('inventories')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($inventory);

    }

    public function createInventoryDetails(Request $request)
    {
        //Check if sku exists
        $sku_check = DB::table('inventories')->where('sku', $request->sku)->first();
        if ($sku_check) {

            $json_array = array(
                'success' => 3,
                'message' => "SKU already exist",
            );

            $response = $json_array;
            return json_encode($response);
        }


        $image_name = "";
        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            ]);

            $image = $request->file('image');
            $file_name = 'rider' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/images/');
            $image->move($destinationPath, $file_name);
            $image_name = url('/') . '/api/v1/get-image/' . $file_name;
        }

        $inventory_object = new Inventory();
        $inventory_created = $inventory_object->create([
            'sku' => $request->sku,
            'name' => $request->name,
            'description' => $request->description,
            'amount' => $request->amount,
            'low_count' => $request->low_count,
            'quantity' => $request->quantity,
            'image' => $image_name,
            'merchant_id' => $request->merchant_id,
        ]);

        if ($inventory_created) {

            $log_controller = new LogController();
            $log_controller->inventoryCreateLog($request->causer_id, $inventory_created->id, $inventory_created->name);

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.inventory')
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

    public function editInventoryDetails(Request $request)
    {
        $inventory_id = $request->id;
        $sku = $request->sku;

        $inventory = DB::table('inventories')
            ->where('id', $inventory_id)
            ->where('deleted_at', null)
            ->first();

        //Check if phone exists
        $sku_check = DB::table('inventories')
            ->where('sku', $sku)
            ->first();
        if ($sku_check) {

            if ($sku_check->id != $inventory_id) {

                $json_array = array(
                    'success' => 3,
                    'message' => "SKU already exist",
                );

                $response = $json_array;
                return json_encode($response);
            }

        }

        $image_name = "";
        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            ]);

            $image = $request->file('image');
            $file_name = 'rider' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/images/');
            $image->move($destinationPath, $file_name);
            $image_name = url('/') . '/api/v1/get-image/' . $file_name;
        }

        $update = DB::table('inventories')
            ->where('id', $request->id)
            ->update([
                'sku' => $request->sku,
                'name' => $request->name,
                'description' => $request->description,
                'amount' => $request->amount,
                'quantity' => $request->quantity,
                'low_count' => $request->low_count,
                'spoilt' => $request->spoilt,
                'image' => $image_name,
                'merchant_id' => $request->merchant_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            // TODO Log inventory update
            $log_controller = new LogController();
            $log_controller->inventoryEditLog($request->causer_id, $inventory->id, $inventory->name);

            $json_array = array(
                'success' => 1,
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

    public function deleteInventoryDetails(Request $request)
    {

        $update = DB::table('inventories')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $inventory = DB::table('inventories')
                ->where('id', $request->id)
                ->first();

            // TODO Log inventory update
            $log_controller = new LogController();
            $log_controller->inventoryDeleteLog($request->causer_id, $inventory->id, $inventory->name);


            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.inventory')
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

    public function deleteImage(Request $request)
    {

        $update = DB::table('inventories')
            ->where('id', $request->id)
            ->update([
                'image' => '',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($update) {

            $json_array = array(
                'success' => 1,
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

    public function searchInventory(Request $request)
    {

        $inventory = DB::table('inventories')
            ->where('sku', $request->sku)
            ->where('deleted_at', null)
            ->first();
        if($inventory){
            return json_encode($inventory);
        }else{

            $json_array = array();
            $response = $json_array;
            return json_encode($response);
        }

    }

    public function searchMerchantInventory(Request $request)
    {

        $inventory = DB::table('inventories')
            ->where('sku', $request->sku)
            ->where('merchant_id', $request->merchant_id)
            ->where('deleted_at', null)
            ->first();
        if($inventory){
            return json_encode($inventory);
        }else{

            $json_array = array();
            $response = $json_array;
            return json_encode($response);
        }

    }

    public function searchOrder(Request $request){

        $order = DB::table('orders')
            ->where('order_no', $request->order_no)
            ->where('deleted_at', null)
            ->first();

        return json_encode($order);
    }

    public function searchInventoryName(Request $request){

        $inventories = DB::table('inventories')
            ->where('name', 'LIKE', "%{$request->search_query}%")
            ->orWhere('sku', 'LIKE', "%{$request->search_query}%")
            ->where('deleted_at', null)
            ->get();

        return json_encode($inventories);
    }

    public function searchMerchantInventoryName(Request $request){

        $inventories = DB::table('inventories')
            ->where('merchant_id', $request->merchant_id)
            ->where('name', 'LIKE', "%{$request->search_query}%")
            ->orWhere('sku', 'LIKE', "%{$request->search_query}%")
            ->where('deleted_at', null)
            ->get();

        return json_encode($inventories);
    }


    public function merchantInventoryList(Request $request)
    {

        $inventories = DB::table('inventories')
            ->where('merchant_id', $request->merchant_id)
            ->where('deleted_at', null)
            ->get();

        return json_encode($inventories);

    }

    public function merchantInventoryDetails(Request $request)
    {

        $inventory = DB::table('inventories')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($inventory);

    }

}
