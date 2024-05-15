<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\LogController;
use App\Town;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TownApiController extends Controller
{
    public function getTownList()
    {
        $towns = DB::table('towns')
            ->where('deleted_at', null)
            ->get();

        return json_encode($towns);

    }

    public function getTownDetails(Request $request)
    {

        $country = DB::table('towns')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($country);

    }

    public function createTownDetails(Request $request){

        $town_object = new Town();
        $town_created = $town_object->create([
            'name'=>$request->name,
            'country_id'=>$request->country_id,
        ]);

        if($town_created){

            // TODO Log town created
            $log_controller = new LogController();
            $log_controller->townCreateLog($request->causer_id, $town_created->id, $town_created->name);

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.town')
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

    public function editTownDetails(Request $request){

        $update = DB::table('towns')
            ->where('id', $request->id)
            ->update([
                'name'=>$request->name,
                'country_id'=>$request->country_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $town = DB::table('towns')
                ->where('id', $request->id)
                ->first();

            // TODO Log town update
            $log_controller = new LogController();
            $log_controller->townEditLog($request->causer_id, $town->id, $town->name);

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

    public function deleteTownDetails(Request $request){

        $update = DB::table('towns')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $town = DB::table('towns')
                ->where('id', $request->id)
                ->first();

            // TODO Log town update
            $log_controller = new LogController();
            $log_controller->townDeleteLog($request->causer_id, $town->id, $town->name);

            // TODO - Redirect route after delete
            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.town')
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
