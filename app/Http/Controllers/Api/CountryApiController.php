<?php

namespace App\Http\Controllers\Api;

use App\Country;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryApiController extends Controller
{
    public function getCountryList()
    {

        $countries = DB::table('countries')
            ->where('deleted_at', null)
            ->get();

        return json_encode($countries);

    }

    public function getCountryDetails(Request $request)
    {

        $country = DB::table('countries')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($country);

    }

    public function createCountryDetails(Request $request){

        $country_object = new Country();
        $country_created = $country_object->create([
            'name'=>$request->name,
        ]);

        if($country_created){

            $log_controller = new LogController();
            $log_controller->countryCreateLog($request->causer_id, $country_created->id, $country_created->name);

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.country')
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

    public function editCountryDetails(Request $request){

        $update = DB::table('countries')
            ->where('id', $request->id)
            ->update([
                'name'=>$request->name,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $country= DB::table('countries')
                ->where('id', $request->id)
                ->first();

            // TODO Log country update
            $log_controller = new LogController();
            $log_controller->countryEditLog($request->causer_id, $country->id, $country->name);

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

    public function deleteCountryDetails(Request $request){

        $update = DB::table('countries')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $country= DB::table('countries')
                ->where('id', $request->id)
                ->first();

            // TODO Log country update
            $log_controller = new LogController();
            $log_controller->countryDeleteLog($request->causer_id, $country->id, $country->name);

            // TODO - Redirect route after delete
            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.country')
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
