<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class OutboundScheduleExport implements FromView
{
    public function view(): View
    {

        $schedules = DB::table('outbound_delivery_schedules')
            ->where('deleted_at', null)
            ->latest()
            ->get();

        $schedule_result = array();
        foreach ($schedules as $schedule){


            $from_name = "";
            $town = DB::table('towns')
                ->where('id', $schedule->from)
                ->first();
            if($town){
                $from_name = $town->name;
            }

            $destination_name = "";
            $town = DB::table('towns')
                ->where('id', $schedule->destination)
                ->first();
            if($town){
                $destination_name = $town->name;
            }

            array_push($schedule_result,
                array(
                    'id' => $schedule->id,
                    'from' => $schedule->from,
                    'from_name' => $from_name,
                    'destination' => $schedule->destination,
                    'destination_name' => $destination_name,
                    'charge' => $schedule->charge,
                    'tax' => $schedule->tax,
                    'total_amount' => $schedule->total_amount,
                    'created_at' => $schedule->created_at,
                    'updated_at' => $schedule->updated_at,
                ));

        }


        return view('admin.report.outbound-schedule-report-excel', [
            'schedules' => $schedule_result
        ]);
    }
}
