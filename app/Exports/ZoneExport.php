<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class ZoneExport implements FromView
{
    public function view(): View
    {

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

        return view('admin.report.zone-report-excel', [
            'zones' => $zone_result
        ]);
    }
}
