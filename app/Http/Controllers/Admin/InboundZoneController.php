<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BranchExport;
use App\Exports\CountryExport;
use App\Exports\ZoneExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class InboundZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $log_controller = new LogController();
        $log_controller->zoneListLog();

        $zones = DB::table('inbound_zone_charges')
            ->where('deleted_at', null)
            ->orderBy('zone', 'ASC')
            ->get();

        $zone_result = array();
        foreach ($zones as $zone){

            array_push($zone_result,
                array(
                    'id' => $zone->id,
                    'zone' => $zone->zone,
                    'same_day_charge' => $zone->same_day_charge,
                    'same_day_tax' => $zone->same_day_tax,
                    'same_day_total_amount' => $zone->same_day_total_amount,
                    'overnight_charge' => $zone->overnight_charge,
                    'overnight_tax' => $zone->overnight_tax,
                    'overnight_total_amount' => $zone->overnight_total_amount,
                    'extra_weight' => $zone->extra_weight,
                    'created_at' => $zone->created_at,
                    'updated_at' => $zone->updated_at,
                ));

        }

        $data = [
            'zones' => $zone_result
        ];

        return view('admin.zone.index', $data);
    }

    public function create()
    {
        return view('admin.zone.zone-create');
    }

    public function details(Request $request)
    {
        $zone = DB::table('inbound_zone_charges')
            ->where('id', $request->id)
            ->first();

        $log_controller = new LogController();
        $log_controller->zoneDetailsLog($request->id, $zone->zone);

        $data = [
            'id' => $request->id
        ];
        return view('admin.zone.zone-details', $data);
    }

    public function reportExcel(){
        return Excel::download(new ZoneExport, 'zone-report-excel.xls');
    }

    public function reportPdf(){

        $zones = DB::table('inbound_zone_charges')
            ->where('deleted_at', null)
            ->orderBy('zone', 'ASC')
            ->get();

        $zone_result = array();
        foreach ($zones as $zone){

            array_push($zone_result,
                array(
                    'id' => $zone->id,
                    'zone' => $zone->zone,
                    'same_day_charge' => $zone->same_day_charge,
                    'same_day_tax' => $zone->same_day_tax,
                    'same_day_total_amount' => $zone->same_day_total_amount,
                    'overnight_charge' => $zone->overnight_charge,
                    'overnight_tax' => $zone->overnight_tax,
                    'overnight_total_amount' => $zone->overnight_total_amount,
                    'extra_weight' => $zone->extra_weight,
                    'created_at' => $zone->created_at,
                    'updated_at' => $zone->updated_at,
                ));

        }

        $data = [
            'zones' => $zone_result
        ];

        $pdf = PDF::loadView('admin.report.zone-report-pdf', $data);
        return $pdf->stream('admin.report.zone-report-pdf');
    }
}
