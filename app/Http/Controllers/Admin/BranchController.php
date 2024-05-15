<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BranchExport;
use App\Exports\CountryExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $log_controller = new LogController();
        $log_controller->branchListLog();

        $branches = DB::table('branches')
            ->where('deleted_at', null)
            ->get();

        $branch_result = array();
        foreach ($branches as $branch){

            // Get country
            $country_name = "";
            $country = DB::table('countries')
                ->where('id', $branch->country_id)
                ->first();
            if($country){
                $country_name = $country->name;
            }

            array_push($branch_result,
                array(
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'address' => $branch->address,
                    'phone_number' => $branch->phone_number,
                    'email' => $branch->email,
                    'country_id' => $branch->country_id,
                    'country_name' => $country_name,
                    'created_at' => $branch->created_at,
                    'updated_at' => $branch->updated_at,
                ));

        }

        $data = [
            'branches' => $branch_result
        ];

        return view('admin.branch.index', $data);
    }

    public function create()
    {
        return view('admin.branch.branch-create');
    }

    public function details(Request $request)
    {

        $branch = DB::table('branches')
            ->where('id', $request->id)
            ->first();

        $log_controller = new LogController();
        $log_controller->branchDetailsLog($request->id, $branch->name);

        $data = [
            'id' => $request->id
        ];
        return view('admin.branch.branch-details', $data);
    }

    public function reportExcel(){
        return Excel::download(new BranchExport, 'branch-report-excel.xls');
    }

    public function reportPdf(){

        $branches = DB::table('branches')
            ->where('deleted_at', null)
            ->get();

        $branch_result = array();
        foreach ($branches as $branch){

            // Get country
            $country_name = "";
            $country = DB::table('countries')
                ->where('id', $branch->country_id)
                ->first();
            if($country){
                $country_name = $country->name;
            }

            array_push($branch_result,
                array(
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'address' => $branch->address,
                    'phone_number' => $branch->phone_number,
                    'email' => $branch->email,
                    'country_id' => $branch->country_id,
                    'country_name' => $country_name,
                    'created_at' => $branch->created_at,
                    'updated_at' => $branch->updated_at,
                ));

        }

        $data = [
            'branches' => $branch_result
        ];

        $pdf = PDF::loadView('admin.report.branch-report-pdf', $data);
        return $pdf->stream('admin.report.branch-report-pdf');
    }
}
