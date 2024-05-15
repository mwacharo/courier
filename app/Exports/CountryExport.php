<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class CountryExport implements FromView
{
    public function view(): View
    {

        $countries = DB::table('countries')
            ->where('deleted_at', null)
            ->get();

        return view('admin.report.country-report-excel', [
            'countries' => $countries
        ]);
    }
}
