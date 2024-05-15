<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChargeCalculatorController extends Controller
{
    public function outboundChargeCalculator(Request $request){

        $is_sender_merchant = $request->is_sender_merchant;
        $merchant_id = $request->merchant_id;
        $from = $request->from;
        $destination = $request->destination;
        $weight = $request->weight;
        $insurance = $request->insurance;

        if($is_sender_merchant == "true" || $is_sender_merchant == 1){

            $merchant = DB::table('merchants')
                ->where('id', $merchant_id)
                ->first();
            if($merchant){

                if($merchant->enable_delivery_fee_outbound == 1){

                    $json_array = array(
                        'success' => 1,
                        'amount' => $merchant->delivery_fee_outbound,
                    );

                    $response = $json_array;
                    return json_encode($response);

                }else{

                    $schedule = DB::table('outbound_delivery_schedules')
                        ->where('from', $from)
                        ->where('destination', $destination)
                        ->where('deleted_at', null)
                        ->first();

                    if($schedule){

                        $amount = $schedule->total_amount;

                        $extra_weight = $schedule->extra_weight;
                        if($weight > 5){
                            $excess_weight = $weight-5;
                            $amount = $amount + ($excess_weight * $extra_weight);
                        }

                        if($insurance == 'true' || $insurance == '1'){
                            $amount = $amount + ($amount * 0.03);
                        }

                        $json_array = array(
                            'success' => 1,
                            'amount' => $amount,
                        );

                        $response = $json_array;
                        return json_encode($response);

                    }else{

                        $json_array = array(
                            'success' => 0,
                        );

                        $response = $json_array;
                        return json_encode($response);

                    }
                }

            }

        }else{

            $schedule = DB::table('outbound_delivery_schedules')
                ->where('from', $from)
                ->where('destination', $destination)
                ->where('deleted_at', null)
                ->first();

            if($schedule){

                $amount = $schedule->total_amount;

                $extra_weight = $schedule->extra_weight;
                if($weight > 5){
                    $excess_weight = $weight-5;
                    $amount = $amount + ($excess_weight * $extra_weight);
                }

                if($insurance == 'true' || $insurance == '1'){
                    $amount = $amount + ($amount * 0.03);
                }

                $json_array = array(
                    'success' => 1,
                    'amount' => $amount,
                );

                $response = $json_array;
                return json_encode($response);

            }else{

                $json_array = array(
                    'success' => 0,
                );

                $response = $json_array;
                return json_encode($response);

            }
        }



    }

    public function inboundChargeCalculator(Request $request){

        $is_sender_merchant = $request->is_sender_merchant;
        $merchant_id = $request->merchant_id;
        $service_type = $request->service_type;
        $inbound_rate_type = $request->inbound_rate_type;
        $weight = $request->weight;
        $insurance = $request->insurance;

        if($is_sender_merchant == "true" || $is_sender_merchant == 1) {

            $merchant = DB::table('merchants')
                ->where('id', $merchant_id)
                ->first();
            if ($merchant) {

                if($merchant->enable_delivery_fee_nairobi == 1){

                    $json_array = array(
                        'success' => 1,
                        'amount' => $merchant->delivery_fee_nairobi,
                    );

                    $response = $json_array;
                    return json_encode($response);

                }else{

                    if($service_type == 1){

                        if($inbound_rate_type == 1){

                            $amount = 200;
                            $delivery_distance = $request->delivery_distance;
                            if($delivery_distance > 5){
                                $amount = 200;
                                $excess_distance = $delivery_distance - 5;
                                $amount = $amount + ($excess_distance * 25);
                            }

                            if($weight > 5){
                                $excess_weight = $weight-5;
                                $amount = $amount + ($excess_weight * 30);
                            }

                            if($insurance == 'true' || $insurance == '1'){
                                $amount = $amount + ($amount * 0.03);
                            }

                            $json_array = array(
                                'success' => 1,
                                'amount' => $amount,
                            );

                            $response = $json_array;
                            return json_encode($response);

                        }elseif ($inbound_rate_type == 2){

                            $schedule = DB::table('inbound_zone_charges')
                                ->where('id', $request->zone)
                                ->where('deleted_at', null)
                                ->first();
                            if($schedule){

                                $amount = $schedule->same_day_total_amount;
                                if($weight > 5){
                                    $excess_weight = $weight-5;
                                    $amount = $amount + ($excess_weight * 30);
                                }

                                if($insurance == 'true' || $insurance == '1'){
                                    $amount = $amount + ($amount * 0.03);
                                }

                                $json_array = array(
                                    'success' => 1,
                                    'amount' => $amount,
                                );

                                $response = $json_array;
                                return json_encode($response);
                            }

                        }

                    }else{

                        if($inbound_rate_type == 1){

                            $amount = 200;
                            $delivery_distance = $request->delivery_distance;
                            if($delivery_distance > 5){
                                $amount = 200;
                                $excess_distance = $delivery_distance - 5;
                                $amount = $amount + ($excess_distance * 25);
                            }

                            if($weight > 5){
                                $excess_weight = $weight-5;
                                $amount = $amount + ($excess_weight * 30);
                            }

                            if($insurance == 'true' || $insurance == '1'){
                                $amount = $amount + ($amount * 0.03);
                            }

                            $json_array = array(
                                'success' => 1,
                                'amount' => $amount,
                            );

                            $response = $json_array;
                            return json_encode($response);

                        }elseif ($inbound_rate_type == 2){

                            $schedule = DB::table('inbound_zone_charges')
                                ->where('id', $request->zone)
                                ->where('deleted_at', null)
                                ->first();
                            if($schedule){

                                $amount = $schedule->overnight_charge;
                                if($weight > 5){
                                    $excess_weight = $weight-5;
                                    $amount = $amount + ($excess_weight * 30);
                                }

                                if($insurance == 'true' || $insurance == '1'){
                                    $amount = $amount + ($amount * 0.03);
                                }

                                $json_array = array(
                                    'success' => 1,
                                    'amount' => $amount,
                                );

                                $response = $json_array;
                                return json_encode($response);
                            }

                        }
                    }
                }
            }

        }else{

            if($service_type == 1){

                if($inbound_rate_type == 1){

                    $amount = 200;
                    $delivery_distance = $request->delivery_distance;
                    if($delivery_distance > 5){
                        $amount = 200;
                        $excess_distance = $delivery_distance - 5;
                        $amount = $amount + ($excess_distance * 25);
                    }

                    if($weight > 5){
                        $excess_weight = $weight-5;
                        $amount = $amount + ($excess_weight * 30);
                    }

                    if($insurance == 'true' || $insurance == '1'){
                        $amount = $amount + ($amount * 0.03);
                    }

                    $json_array = array(
                        'success' => 1,
                        'amount' => $amount,
                    );

                    $response = $json_array;
                    return json_encode($response);

                }elseif ($inbound_rate_type == 2){

                    $schedule = DB::table('inbound_zone_charges')
                        ->where('id', $request->zone)
                        ->where('deleted_at', null)
                        ->first();
                    if($schedule){

                        $amount = $schedule->same_day_total_amount;
                        if($weight > 5){
                            $excess_weight = $weight-5;
                            $amount = $amount + ($excess_weight * 30);
                        }

                        if($insurance == 'true' || $insurance == '1'){
                            $amount = $amount + ($amount * 0.03);
                        }

                        $json_array = array(
                            'success' => 1,
                            'amount' => $amount,
                        );

                        $response = $json_array;
                        return json_encode($response);
                    }

                }

            }else{

                if($inbound_rate_type == 1){

                    $amount = 200;
                    $delivery_distance = $request->delivery_distance;
                    if($delivery_distance > 5){
                        $amount = 200;
                        $excess_distance = $delivery_distance - 5;
                        $amount = $amount + ($excess_distance * 25);
                    }

                    if($weight > 5){
                        $excess_weight = $weight-5;
                        $amount = $amount + ($excess_weight * 30);
                    }

                    if($insurance == 'true' || $insurance == '1'){
                        $amount = $amount + ($amount * 0.03);
                    }

                    $json_array = array(
                        'success' => 1,
                        'amount' => $amount,
                    );

                    $response = $json_array;
                    return json_encode($response);

                }elseif ($inbound_rate_type == 2){

                    $schedule = DB::table('inbound_zone_charges')
                        ->where('id', $request->zone)
                        ->where('deleted_at', null)
                        ->first();
                    if($schedule){

                        $amount = $schedule->overnight_charge;
                        if($weight > 5){
                            $excess_weight = $weight-5;
                            $amount = $amount + ($excess_weight * 30);
                        }

                        if($insurance == 'true' || $insurance == '1'){
                            $amount = $amount + ($amount * 0.03);
                        }

                        $json_array = array(
                            'success' => 1,
                            'amount' => $amount,
                        );

                        $response = $json_array;
                        return json_encode($response);
                    }

                }
            }
        }


    }
}
