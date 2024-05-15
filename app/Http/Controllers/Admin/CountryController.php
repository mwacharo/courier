<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CountryExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class CountryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $log_controller = new LogController();
        $log_controller->countryListLog();

        $countries = DB::table('countries')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get();

        $data = [
            'countries' => $countries
        ];

        return view('admin.country.index', $data);
    }

    public function create()
    {
        return view('admin.country.country-create');
    }

    public function details(Request $request)
    {
        $country = DB::table('countries')
            ->where('id', $request->id)
            ->first();

        $log_controller = new LogController();
        $log_controller->countryDetailsLog($request->id, $country->name);

        $data = [
            'id' => $request->id
        ];
        return view('admin.country.country-details', $data);
    }

    public function reportExcel(){
        return Excel::download(new CountryExport, 'country-report-excel.xls');
    }

    public function reportPdf(){

        $countries = DB::table('countries')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->get();

        $data = [
            'countries' => $countries
        ];

        $pdf = PDF::loadView('admin.report.country-report-pdf', $data);
        return $pdf->stream('admin.report.country-report-pdf');
    }
}
