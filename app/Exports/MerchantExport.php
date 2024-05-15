<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class MerchantExport implements FromView
{
    public function view(): View
    {

        $merchants = DB::table('merchants')
            ->where('deleted_at', null)
            ->get();

        $merchant_result = array();
        foreach ($merchants as $merchant){

            // Get country
            $country_name = "";
            $country = DB::table('countries')
                ->where('id', $merchant->country_id)
                ->first();
            if($country){
                $country_name = $country->name;
            }

            // Get town
            $town_name = "";
            $town = DB::table('towns')
                ->where('id', $merchant->town_id)
                ->first();
            if($town){
                $town_name = $town->name;
            }

            array_push($merchant_result,
                array(
                    'id' => $merchant->id,
                    'name' => $merchant->name,
                    'address' => $merchant->address,
                    'phone_number' => $merchant->phone_number,

                    'email' => $merchant->email,
                    'enable_cash_on_delivery_fee' => $merchant->enable_cash_on_delivery_fee,
                    'cash_on_delivery_fee' => $merchant->cash_on_delivery_fee,
                    'enable_delivery_fee_nairobi' => $merchant->enable_delivery_fee_nairobi,
                    'delivery_fee_nairobi' => $merchant->delivery_fee_nairobi,
                    'enable_delivery_fee_outbound' => $merchant->enable_delivery_fee_outbound,
                    'delivery_fee_outbound' => $merchant->delivery_fee_outbound,
                    'enable_returns_management_fee' => $merchant->enable_returns_management_fee,
                    'enable_warehousing_fee' => $merchant->enable_warehousing_fee,
                    'warehousing_fee' => $merchant->warehousing_fee,
                    'enable_packaging_fee' => $merchant->enable_packaging_fee,
                    'packaging_fee' => $merchant->packaging_fee,
                    'enable_call_centre_fee' => $merchant->enable_call_centre_fee,
                    'call_centre_fee' => $merchant->call_centre_fee,
                    'enable_label_printing_fee' => $merchant->enable_label_printing_fee,
                    'label_printing_fee' => $merchant->label_printing_fee,
                    'contract' => $merchant->contract,
                    'enabled' => $merchant->enabled,
                    'country_id' => $merchant->country_id,
                    'country_name' => $country_name,
                    'town_id' => $merchant->town_id,
                    'town_name' => $town_name,
                    'created_at' => $merchant->created_at,
                    'updated_at' => $merchant->updated_at,
                ));

        }

        return view('admin.report.merchant-report-excel', [
            'merchants' => $merchant_result
        ]);
    }
}
