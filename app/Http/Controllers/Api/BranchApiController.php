<?php

namespace App\Http\Controllers\Api;

use App\Branch;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchApiController extends Controller
{
    public function getBranchList()
    {

        $branches = DB::table('branches')
            ->where('deleted_at', null)
            ->get();

        return json_encode($branches);

    }

    public function getBranchDetails(Request $request)
    {

        $branch = DB::table('branches')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($branch);

    }

    public function createBranchDetails(Request $request){

        $branch_object = new Branch();
        $branch_created = $branch_object->create([
            'name'=>$request->name,
            'address'=>$request->address,
            'phone_number'=>$request->phone_number,
            'email'=>$request->email,
            'country_id'=>$request->country_id,
        ]);

        if($branch_created){

            // TODO Log branch created
            $log_controller = new LogController();
            $log_controller->branchCreateLog($request->causer_id, $branch_created->id, $branch_created->name);

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.branch')
            );

            $response = $json_array;
            return json_encode($response);

        }else{

            $json_array = array(
                'success' => 0,
            );

            $response = $json_array;
            return json_encode($response);

        }
    }

    public function editBranchDetails(Request $request){

        $update = DB::table('branches')
            ->where('id', $request->id)
            ->update([
                'name'=>$request->name,
                'address'=>$request->address,
                'phone_number'=>$request->phone_number,
                'email'=>$request->email,
                'country_id'=>$request->country_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $branch = DB::table('branches')
                ->where('id', $request->id)
                ->first();

            // TODO Log branch update
            $log_controller = new LogController();
            $log_controller->branchEditLog($request->causer_id, $branch->id, $branch->name);


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

    public function deleteBranchDetails(Request $request){

        $update = DB::table('branches')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $branch = DB::table('branches')
                ->where('id', $request->id)
                ->first();

            // TODO Log branch update
            $log_controller = new LogController();
            $log_controller->branchDeleteLog($request->causer_id, $branch->id, $branch->name);

            // TODO - Redirect route after delete
            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.branch')
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
