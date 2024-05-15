<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class BranchExport implements FromView
{
    public function view(): View
    {

        $branches = DB::table('branches')
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

        return view('admin.report.branch-report-excel', [
            'branches' => $branch_result
        ]);
    }
}
