<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RiderExport;
use App\Http\Controllers\Controller;
use App\Imports\PayoutImport;
use App\Imports\RiderImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class RiderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $log_controller = new LogController();
        $log_controller->riderListLog();

        $riders = DB::table('riders')
            ->where('deleted_at', null)
            ->orderBy('first_name', 'ASC')
            ->get();

        $data = [
            'riders' => $riders
        ];

        return view('admin.rider.index', $data);
    }

    public function create()
    {
        return view('admin.rider.rider-create');
    }

    public function details(Request $request)
    {
        $rider = DB::table('riders')
            ->where('id', $request->id)
            ->first();

        $log_controller = new LogController();
        $log_controller->riderDetailsLog($request->id, $rider->first_name);

        $data = [
            'id' => $request->id
        ];
        return view('admin.rider.rider-details', $data);
    }

    public function reportExcel(){
        return Excel::download(new RiderExport, 'rider-report-excel.xls');
    }

    public function reportPdf(){

        $riders = DB::table('riders')
            ->where('deleted_at', null)
            ->get();

        $rider_result = array();
        foreach ($riders as $rider){

            // Get country
            $country_name = "";
            $country = DB::table('countries')
                ->where('id', $rider->country_id)
                ->first();
            if($country){
                $country_name = $country->name;
            }

            array_push($rider_result,
                array(
                    'id' => $rider->id,
                    'first_name' => $rider->first_name,
                    'last_name' => $rider->last_name,
                    'date_of_birth' => $rider->date_of_birth,
                    'national_id' => $rider->national_id,
                    'address' => $rider->address,
                    'phone_number' => $rider->phone_number,
                    'profile_image' => $rider->profile_image,
                    'email' => $rider->email,
                    'country_id' => $rider->country_id,
                    'country_name' => $country_name,
                    'created_at' => $rider->created_at,
                    'updated_at' => $rider->updated_at,
                ));

        }

        $data = [
            'riders' => $rider_result
        ];

        $pdf = PDF::loadView('admin.report.rider-report-pdf', $data);
        return $pdf->stream('admin.report.rider-report-pdf');
    }

    public function importExcel()
    {
        return view('admin.rider.rider-import');
    }

    public function importExcelUpload(Request $request)
    {
        $this->validate($request, [
            'select_file'  => 'required|mimes:xls,xlsx'
        ]);

        $path1 = $request->file('select_file')->store('temp');
        $path=storage_path('app').'/'.$path1;

        $import = new RiderImport;
        Excel::import($import, $path);

        if($import->getErrorMessage() == ''){
            return back()->with('success', 'Excel Data Imported successfully.' . $import->getRowCount() . ' rows imported');
        }else{
            return back()->with('error', 'Import Errors.' . $import->getErrorMessage());
        }

    }
}
