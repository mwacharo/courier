<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\MpesaPayment;
use App\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentApiController extends Controller
{
    /**
     *  C2B Intergration
     */

    public function generateToken()
    {

        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $app_consumer_key = 'wOoKLJ0s7NePmNDtc5hIJuXbt4Y2WgnJ';
        $app_consumer_secret = 'ywoXrqqeaKsicZGg';
        $credentials = base64_encode($app_consumer_key.':'.$app_consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $token_info=json_decode($curl_response,true);
        return $token_info['access_token'];

    }

    public function registerUrl(){

        $access_token = $this->generateToken();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer '.$access_token));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            'ShortCode' => '4032407',
            'ResponseType' => 'Completed',
            'ConfirmationURL' => 'https://boxleocourier.com/dashboard/api/confirmation',
            'ValidationURL' => 'https://boxleocourier.com/dashboard/api/validation'
        )));

        $curl_response = curl_exec($curl);
        echo $curl_response;
    }

    public function paymentValidation(Request $request)
    {
        $result_code="0";
        $result_description="Accepted";

        $result = json_encode(["ResultCode"=>$result_code, "ResultDesc"=>$result_description]);
        $response = new Response();
        $response->headers->set("Content-Type","application/json; charset=utf-8");
        $response->setContent($result);
        return $response;
    }

    public function paymentConfirmation(Request $request)
    {
        $content=json_decode($request->getContent());
        $new_mpesa_payment = new MpesaPayment();
        $new_mpesa_payment->FirstName = $content->FirstName;
        $new_mpesa_payment->MiddleName = $content->MiddleName;
        $new_mpesa_payment->LastName = $content->LastName;
        $new_mpesa_payment->MSISDN = $content->MSISDN;
        $new_mpesa_payment->InvoiceNumber = $content->InvoiceNumber;
        $new_mpesa_payment->BusinessShortCode = $content->BusinessShortCode;
        $new_mpesa_payment->ThirdPartyTransID = $content->ThirdPartyTransID;
        $new_mpesa_payment->TransactionType = $content->TransactionType;
        $new_mpesa_payment->OrgAccountBalance = $content->OrgAccountBalance;
        $new_mpesa_payment->BillRefNumber = $content->BillRefNumber;
        $new_mpesa_payment->TransAmount = $content->TransAmount;
        $new_mpesa_payment->save();

        // Update payment history
        $order_no = strtoupper($content->BillRefNumber);
        $payment_history_object = new PaymentHistory();
        $paymnent_history = $payment_history_object->create([
            'order_no'=>$content->BillRefNumber,
            'amount'=>$content->TransAmount,
            'reference_name'=>$content->FirstName ." ".$content->LastName,
            'reference_no'=>$content->MSISDN,
        ]);

        if($paymnent_history){

            $order = DB::table('orders')
                ->where('order_no', $content->BillRefNumber)
                ->first();
            if($order){

                $payment_sum = DB::table('payment_histories')
                    ->where('order_no', $content->BillRefNumber)
                    ->sum('amount');

                $balance = $order->amount - $payment_sum;
                $payment_status = 0;
                if($order->payment_method == 2){

                    if($balance == $content->TransAmount){
                        $payment_status = 1;
                    }else if($balance > $content->TransAmount){
                        $payment_status = 2;
                    }else if($balance < $content->TransAmount){
                        $payment_status = 1;
                    }

                    $new_amount = $payment_sum + $content->TransAmount;
                    $update_order = DB::table('orders')
                        ->where('id', $order->id)
                        ->update([
                            'payment_status'=> $payment_status,
                            'mpesa_amount'=> $new_amount,
                            'updated_at'=>date('Y-m-d H:i:s'),
                        ]);

                }else if($order->payment_method == 3){

                    $new_amount = $payment_sum + $content->TransAmount;
                    $update_order = DB::table('orders')
                        ->where('id', $order->id)
                        ->update([
                            'mpesa_amount'=> $new_amount,
                            'updated_at'=>date('Y-m-d H:i:s'),
                        ]);

                }

                $receiver_name = $order->receiver_name;
                $receiver_email = $order->receiver_email;
                $receiver_phone = $order->receiver_phone;

                $email_util = new EmailUtil();
                $send_email = $email_util->paymentReceipt($order_no, $receiver_name, $receiver_email, $content->TransAmount);

                $sms_message = "Hi $receiver_name , We have received your payment for order $order_no. Hope you enjoyed our services";
                $sms_util = new SMSUtil();
                $sms_util->sendSMS($receiver_phone, $sms_message);

            }


        }else{

            return ['message'=>'Payment failed'];
        }

        // Responding to the confirmation request
        $response = new Response();
        $response->headers->set("Content-Type","text/xml; charset=utf-8");
        $response->setContent(json_encode(["C2BPaymentConfirmationResult"=>"Success"]));
        return $response;
    }


    public function checkPayment(Request $request){


        $order = DB::table('orders')
            ->where('id', $request->order_id)
            ->first();
        if($order){

            $payment_sum = DB::table('payment_histories')
                ->where('order_no', $order->order_no)
                ->sum('amount');

            if($order->cash_on_delivery == 1){

                $balance = ($order->amount) - $payment_sum;
                $json_array = array(
                    'amount' => $payment_sum,
                    'balance' => $balance,
                );

                $response = $json_array;
                return json_encode($response);

            }else{

                $balance = $order->amount - $payment_sum;
                $json_array = array(
                    'amount' => $payment_sum,
                    'balance' => $balance,
                );

                $response = $json_array;
                return json_encode($response);
            }

        }
    }

    public function updatePaymentStatus(Request $request)
    {
        $order_id = $request->order_id;
        $order =  DB::table('orders')
            ->where('id', $order_id)
            ->first();

        if($order){

            $transaction_code = "";
            if($request->transaction_code != 'null'){
                $transaction_code = $request->transaction_code;
            }

            $cash_amount = "";
            if($request->cash_amount != 'null'){
                $cash_amount = $request->cash_amount;
            }

            $mpesa_amount = "";
            if($request->mpesa_amount != 'null'){
                $mpesa_amount = $request->mpesa_amount;
            }

            $cash_mpesa_amount = "";
            if($request->cash_mpesa_amount != 'null'){
                $cash_mpesa_amount = $request->cash_mpesa_amount;
            }

            $update_order = DB::table('orders')
                ->where('id', $order_id)
                ->update([
                    'payment_status'=> $request->status,
                    'payment_method'=> $request->payment_method,
                    'cash_amount'=> $cash_amount,
                    'mpesa_amount'=> $mpesa_amount,
                    'cash_mpesa_amount'=> $cash_mpesa_amount,
                    'transaction_code'=> $transaction_code,
                    'updated_at'=>date('Y-m-d H:i:s'),
                ]);


            if($update_order){

                $update_order = DB::table('orders')
                    ->where('id', $order_id)
                    ->update([
                        'order_status'=> 'delivered',
                        'delivered_date'=>date('Y-m-d H:i:s'),
                        'updated_at'=>date('Y-m-d H:i:s'),
                    ]);

                $link = "https://boxleocourier.com/dashboard/api/v1/order-waybill/".$order->order_no;
                $sms_message = "Hi $order->receiver_name !, Your order has been delivered successfully. To see waybill click $link";
                $sms_util = new SMSUtil();
                $sms_util->sendSMS($order->receiver_phone, $sms_message);

                $json_array = array(
                    'success' => 1,
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

        } else {

            $json_array = array(
                'success' => 2,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }
}
