<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\LogController;
use App\InboundZoneCharge;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InboundZoneApiController extends Controller
{
    public function getZoneList()
    {
        $zones = DB::table('inbound_zone_charges')
            ->where('deleted_at', null)
            ->get();

        return json_encode($zones);

    }

    public function getZoneDetails(Request $request)
    {

        $zone = DB::table('inbound_zone_charges')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($zone);

    }

    public function createZoneDetails(Request $request){

        $zone_object = new InboundZoneCharge();
        $zone_created = $zone_object->create([
            'zone'=>$request->zone,
            'extra_weight'=>$request->extra_weight,
            'same_day_charge'=>$request->same_day_charge,
            'same_day_tax'=>$request->same_day_tax,
            'same_day_total_amount'=>$request->same_day_total_amount,
            'overnight_charge'=>$request->overnight_charge,
            'overnight_tax'=>$request->overnight_tax,
            'overnight_total_amount'=>$request->overnight_total_amount,
        ]);

        if($zone_created){

            // TODO Log zone created
            $log_controller = new LogController();
            $log_controller->zoneCreateLog($request->causer_id, $zone_created->id, $zone_created->zone);

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.zone')
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

    public function editZoneDetails(Request $request){

        $update = DB::table('inbound_zone_charges')
            ->where('id', $request->id)
            ->update([
                'zone'=>$request->zone,
                'extra_weight'=>$request->extra_weight,
                'same_day_charge'=>$request->same_day_charge,
                'same_day_tax'=>$request->same_day_tax,
                'same_day_total_amount'=>$request->same_day_total_amount,
                'overnight_charge'=>$request->overnight_charge,
                'overnight_tax'=>$request->overnight_tax,
                'overnight_total_amount'=>$request->overnight_total_amount,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $zone = DB::table('inbound_zone_charges')
                ->where('id', $request->id)
                ->first();

            // TODO Log zone update
            $log_controller = new LogController();
            $log_controller->zoneEditLog($request->causer_id, $zone->id, $zone->zone);

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

    public function deleteZoneDetails(Request $request){

        $update = DB::table('inbound_zone_charges')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $zone = DB::table('inbound_zone_charges')
                ->where('id', $request->id)
                ->first();

            // TODO Log zone update
            $log_controller = new LogController();
            $log_controller->zoneDeleteLog($request->causer_id, $zone->id, $zone->zone);

            // TODO - Redirect route after delete
            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.zone')
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
