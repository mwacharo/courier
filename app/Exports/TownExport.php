<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class TownExport implements FromView
{
    public function view(): View
    {

        $towns = DB::table('towns')
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

        return view('admin.report.town-report-excel', [
            'towns' => $town_result
        ]);
    }
}
