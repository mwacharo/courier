<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\LogController;
use App\OutboundDeliverySchedule;
use App\Schedule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutboundScheduleApiController extends Controller
{
    public function getScheduleList()
    {

        $schedules = DB::table('outbound_delivery_schedules')
            ->where('deleted_at', null)
            ->get();

        return json_encode($schedules);

    }

    public function getScheduleDetails(Request $request)
    {

        $schedule = DB::table('outbound_delivery_schedules')
            ->where('id', $request->id)
            ->where('deleted_at', null)
            ->first();

        return json_encode($schedule);

    }

    public function createScheduleDetails(Request $request){

        $schedule_object = new OutboundDeliverySchedule();
        $schedule_created = $schedule_object->create([
            'from'=>$request->from,
            'destination'=>$request->destination,
            'extra_weight'=>$request->extra_weight,
            'charge'=>$request->charge,
            'tax'=>$request->tax,
            'total_amount'=>$request->total_amount,
        ]);

        if($schedule_created){

            // TODO Log schedule update
            $log_controller = new LogController();
            $log_controller->scheduleCreateLog($request->causer_id, $schedule_created->id, $schedule_created->id);

            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.schedule.outbound')
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

    public function editScheduleDetails(Request $request){

        $update = DB::table('outbound_delivery_schedules')
            ->where('id', $request->id)
            ->update([
                'from'=>$request->from,
                'destination'=>$request->destination,
                'charge'=>$request->charge,
                'extra_weight'=>$request->extra_weight,
                'tax'=>$request->tax,
                'total_amount'=>$request->total_amount,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $schedule = DB::table('outbound_delivery_schedules')
                ->where('id', $request->id)
                ->first();

            // TODO Log schedule update
            $log_controller = new LogController();
            $log_controller->scheduleEditLog($request->causer_id, $schedule->id, $schedule->id);

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

    public function deleteScheduleDetails(Request $request){

        $update = DB::table('outbound_delivery_schedules')
            ->where('id', $request->id)
            ->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $schedule = DB::table('outbound_delivery_schedules')
                ->where('id', $request->id)
                ->first();

            // TODO Log schedule update
            $log_controller = new LogController();
            $log_controller->scheduleDeleteLog($request->causer_id, $schedule->id, $schedule->id);


            // TODO - Redirect route after delete
            $json_array = array(
                'success' => 1,
                'redirect' => route('admin.schedule.outbound')
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
