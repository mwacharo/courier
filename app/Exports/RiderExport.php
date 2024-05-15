<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class RiderExport implements FromView
{
    public function view(): View
    {

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

        return view('admin.report.rider-report-excel', [
            'riders' => $rider_result
        ]);
    }
}
