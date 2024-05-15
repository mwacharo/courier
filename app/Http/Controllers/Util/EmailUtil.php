<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailUtil extends Controller
{
    public function adminWelcomeEmail($name, $email, $password){

        $data = array(
            'email'=>$email,
            'password'=>$password,
            'name'=>$name,
        );

        Mail::send('emails.admin-welcome-note', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Administrator Welcome Note - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Administrator Welcome Note - BOXLEO COURIER');
        });
    }

    public function riderWelcomeEmail($name, $email, $password){

        $data = array(
            'email'=>$email,
            'password'=>$password,
            'name'=>$name,
        );

        Mail::send('emails.rider-welcome-note', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Rider Welcome Note - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Rider Welcome Note - BOXLEO COURIER');
        });
    }

    public function merchantWelcomeEmail($name, $email, $password){

        $data = array(
            'email'=>$email,
            'password'=>$password,
            'name'=>$name,
        );

        Mail::send('emails.merchant-welcome-note', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Merchant Welcome Note - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Merchant Welcome Note - BOXLEO COURIER');
        });
    }

    public function passwordResetEmail($name, $email, $password){

        $data = array(
            'email'=>$email,
            'password'=>$password,
            'name'=>$name,
        );

        Mail::send('emails.password-reset', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Account Password Reset - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Account Password Reset - BOXLEO COURIER');
        });
    }

    public function passwordResetCode($name, $email, $reset_code){

        $data = array(
            'email'=>$email,
            'reset_code'=>$reset_code,
            'name'=>$name,
        );

        Mail::send('emails.password-reset-code', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Account Password Reset - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Account Password Reset Code - BOXLEO COURIER');
        });
    }

    public function orderReceivedEmail($name, $email, $order_no, $address){

        $data = array(
            'email'=>$email,
            'address'=>$address,
            'order_no'=>$order_no,
            'name'=>$name,
        );

        Mail::send('emails.order-received-note', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Shipment Order Received - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Shipment Order Received - BOXLEO COURIER');
        });
    }

    public function paymentReceipt($order_no, $receiver_name, $receiver_email, $amount){

        $data = array(
            'receiver_name'=>$receiver_name,
            'receiver_email'=>$receiver_email,
            'order_no'=>$order_no,
            'amount'=>$amount,
        );

        Mail::send('emails.payment-received-note', $data, function($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Shipment Payment Received - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Shipment Payment Received - BOXLEO COURIER');
        });
    }

    public function pendingOrderNotification(string $order_count)
    {
        $data = array(
            'order_count'=>$order_count,
        );

        Mail::send('emails.pending-order-notification', $data, function($message) use ($data) {
            $message->to('operations@boxleocourier.com', 'brian@boxleocourier.com')->subject('Pending Orders Notification - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Pending Orders Notification - BOXLEO COURIER');
        });
    }

    public function scheduledOrderNotification(string $order_count)
    {
        $data = array(
            'order_count'=>$order_count,
        );

        Mail::send('emails.scheduled-order-notification', $data, function($message) use ($data) {
            $message->to('operations@boxleocourier.com', 'brian@boxleocourier.com')->subject('Scheduled Orders Notification - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','Scheduled Orders Notification - BOXLEO COURIER');
        });
    }

    public function newOrderNotification(string $order_no)
    {
        $data = array(
            'order_no'=>$order_no,
        );

        Mail::send('emails.new-order-notification', $data, function($message) use ($data) {
            $message->to('operations@boxleocourier.com', 'brian@boxleocourier.com')->subject('New Order Notification - BOXLEO COURIER');
            $message->from('support@boxleocourier.com','New Order Notification - BOXLEO COURIER');
        });
    }
}
