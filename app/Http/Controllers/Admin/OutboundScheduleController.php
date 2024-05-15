<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CountryExport;
use App\Exports\OutboundScheduleExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class OutboundScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $log_controller = new LogController();
        $log_controller->scheduleListLog();

        $schedules = DB::table('outbound_delivery_schedules')
            ->where('deleted_at', null)
            ->latest()
            ->get();

        $schedule_result = array();
        foreach ($schedules as $schedule){


            $from_name = "";
            $town = DB::table('towns')
                ->where('id', $schedule->from)
                ->first();
            if($town){
                $from_name = $town->name;
            }

            $destination_name = "";
            $town = DB::table('towns')
                ->where('id', $schedule->destination)
                ->first();
            if($town){
                $destination_name = $town->name;
            }

            array_push($schedule_result,
                array(
                    'id' => $schedule->id,
                    'from' => $schedule->from,
                    'from_name' => $from_name,
                    'destination' => $schedule->destination,
                    'destination_name' => $destination_name,
                    'charge' => $schedule->charge,
                    'tax' => $schedule->tax,
                    'total_amount' => $schedule->total_amount,
                    'created_at' => $schedule->created_at,
                    'updated_at' => $schedule->updated_at,
                ));

        }

        $data = [
            'schedules' => $schedule_result
        ];

        return view('admin.schedule.outbound', $data);
    }

    public function create()
    {
        return view('admin.schedule.outbound-create');
    }

    public function details(Request $request)
    {
        $schedule = DB::table('outbound_delivery_schedules')
            ->where('id', $request->id)
            ->first();

        $log_controller = new LogController();
        $log_controller->scheduleDetailsLog($request->id, $schedule->id);

        $data = [
            'id' => $request->id
        ];
        return view('admin.schedule.outbound-details', $data);
    }

    public function reportExcel(){
        return Excel::download(new OutboundScheduleExport, 'outbound-schedule-report-excel.xls');
    }

    public function reportPdf(){

        $schedules = DB::table('outbound_delivery_schedules')
            ->where('deleted_at', null)
            ->latest()
            ->get();

        $schedule_result = array();
        foreach ($schedules as $schedule){


            $from_name = "";
            $town = DB::table('towns')
                ->where('id', $schedule->from)
                ->first();
            if($town){
                $from_name = $town->name;
            }

            $destination_name = "";
            $town = DB::table('towns')
                ->where('id', $schedule->destination)
                ->first();
            if($town){
                $destination_name = $town->name;
            }

            array_push($schedule_result,
                array(
                    'id' => $schedule->id,
                    'from' => $schedule->from,
                    'from_name' => $from_name,
                    'destination' => $schedule->destination,
                    'destination_name' => $destination_name,
                    'charge' => $schedule->charge,
                    'tax' => $schedule->tax,
                    'total_amount' => $schedule->total_amount,
                    'created_at' => $schedule->created_at,
                    'updated_at' => $schedule->updated_at,
                ));

        }

        $data = [
            'schedules' => $schedule_result
        ];

        $pdf = PDF::loadView('admin.report.outbound-schedule-report-pdf', $data);
        return $pdf->stream('admin.report.outbound-schedule-report-pdf');
    }
}
