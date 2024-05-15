<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TownExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class TownController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $log_controller = new LogController();
        $log_controller->townListLog();

        $towns = DB::table('towns')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get();

        $town_result = array();
        foreach ($towns as $town){

            // Get country
            $country_name = "";
            $country = DB::table('countries')
                ->where('id', $town->country_id)
                ->first();
            if($country){
                $country_name = $country->name;
            }

            array_push($town_result,
                array(
                    'id' => $town->id,
                    'name' => $town->name,
                    'country_id' => $town->country_id,
                    'country_name' => $country_name,
                    'created_at' => $town->created_at,
                    'updated_at' => $town->updated_at,
                ));

        }

        $data = [
            'towns' => $town_result
        ];

        return view('admin.town.index', $data);
    }

    public function create()
    {
        return view('admin.town.town-create');
    }

    public function details(Request $request)
    {
        $town = DB::table('towns')
            ->where('id', $request->id)
            ->first();

        $log_controller = new LogController();
        $log_controller->townDetailsLog($request->id, $town->name);

        $data = [
            'id' => $request->id
        ];
        return view('admin.town.town-details', $data);
    }

    public function reportExcel(){
        return Excel::download(new TownExport, 'town-report-excel.xls');
    }

    public function reportPdf(){

        $towns = DB::table('towns')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get();

        $town_result = array();
        foreach ($towns as $town){

            // Get country
            $country_name = "";
            $country = DB::table('countries')
                ->where('id', $town->country_id)
                ->first();
            if($country){
                $country_name = $country->name;
            }

            array_push($town_result,
                array(
                    'id' => $town->id,
                    'name' => $town->name,
                    'country_id' => $town->country_id,
                    'country_name' => $country_name,
                    'created_at' => $town->created_at,
                    'updated_at' => $town->updated_at,
                ));

        }

        $data = [
            'towns' => $town_result
        ];

        $pdf = PDF::loadView('admin.report.town-report-pdf', $data);
        return $pdf->stream('admin.report.town-report-pdf');
    }
}
