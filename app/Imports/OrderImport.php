<?php

namespace App\Imports;

use App\Order;

use App\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Util\SMSUtil;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;

class OrderImport implements ToCollection, WithStartRow
{

    private $rows = 0;
    private $message = "";

    protected $merchant_id;
    public function __construct(string $merchant_id)
    {
        $this->merchant_id = $merchant_id;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $order_no = $row[0];
            $cash_on_delivery = $row[1];
            $receiver_name = $row[2];
            $receiver_address = $row[3];
            $receiver_phone = $row[4];
            $receiver_alternative_phone = $row[5];
            $receiver_country_name = $row[6];
            $receiver_town_name = $row[7];
            $item_sku = $row[8];
            $item_name = $row[9];
            $item_quantity = $row[10];
            $order_status = $row[11];
            $status_date = $row[12];
            $special_instruction = $row[13];
            $scheduled_date = $row[14];
            $agent = $row[15];

            if ($order_no == '') {

                $order_count = DB::table('orders')
                    ->count();
                if ($order_count > 0) {
                    $order = DB::table('orders')
                        ->latest()
                        ->first();
                    if ($order) {
                        $order_id = $order->order_id + 1;
                        $order_no = "BX000" . $order_id;
                    }
                } else {
                    $order_no = "BX0001";
                }

            }

            $order_no_count = DB::table('orders')
                ->where('order_no', $order_no)
                ->count();
            if ($order_no_count == 0) {

                $sender_name = "";
                $sender_address = "";
                $sender_email = "";
                $sender_phone = "";
                $sender_country = "";
                $sender_town = "";
                $merchant_id = "";

                $merchant = DB::table('merchants')
                    ->where('id', $this->merchant_id)
                    ->first();
                if ($merchant) {
                    $merchant_id = $merchant->id;
                    $sender_name = $merchant->name;
                    $sender_address = $merchant->address;
                    $sender_phone = $merchant->phone_number;
                    $sender_email = $merchant->email;
                    $sender_country = $merchant->country_id;
                    $sender_town = $merchant->town_id;

                    if ($agent == '') {

                        $call_agent = DB::table('call_agents')
                            ->where('admin_id', $merchant->admin_id)
                            ->where('deleted_at', null)
                            ->first();

                        if ($call_agent) {
                            $agent = $call_agent->client_name;
                        }

                    }

                    if (strtoupper(substr($order_no, 0, 3)) != strtoupper($merchant->merchant_prefix)) {
                        dd (strtoupper(substr($order_no, 0, 3)));
                        $this->message = "Order does not match the merchant Row no. $this->rows";
                        return;
                    }

                } else {
                    $this->message = "Merchant does not exist or incorrect Row no. $this->rows";
                    return;
                }

                $receiver_country = "";
                if ($receiver_country_name != "") {
                    $country = DB::table('countries')
                        ->where('name', 'LIKE', "%{$receiver_country_name}%")
                        ->where('deleted_at', null)
                        ->first();
                    if ($country) {
                        $receiver_country = $country->id;
                    }
                }

                $receiver_town = "";
                if ($receiver_town_name != "") {

                    $town = DB::table('towns')
                        ->where('name', 'LIKE', "%{$receiver_town_name}%")
                        ->where('deleted_at', null)
                        ->first();
                    if ($town) {
                        $receiver_town = $town->id;
                    }

                }

                $cash_on_delivery_amount = 0;
                if ($cash_on_delivery != "") {
                    $cash_on_delivery_amount = $cash_on_delivery;
                    $cash_on_delivery = true;
                } else {
                    $cash_on_delivery = false;
                }

                if ($order_status == '') {
                    $order_status = 'order_pending';
                    $status_date = date('Y-m-d');
                    $scheduled_date = '';

                } else {

                    if ($order_status == 'PENDING') {

                        $order_status = 'order_pending';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                        if ($special_instruction != '') {
                            if ($sender_name != 'D.LIGHT LTD') {
                                $sms_message = "Hello $receiver_name!,\nWe received the order you placed online reference $order_no at KES $cash_on_delivery_amount. We tried contacting you but there was no positive response. We would like to deliver it to you.\n
                            Kindly contact us on  +254 791 960 533 to confirm availability for delivery process to be made successfully.";
                                // Create an instance of SMSUtil

                                $sms_util = new SMSUtil();
                                $sms_util->sendSMS($receiver_phone, $sms_message);


                            }

                        }

                    } else if ($order_status == 'SCHEDULED') {

                        $order_status = 'scheduled';
                        $status_date = date('Y-m-d');

                        if ($scheduled_date == '') {
                            $this->message = "Scheduled date not added. Please check again Row no. $this->rows";
                            return;
                        }

                        if($sender_name!='D.LIGHT LTD') {
                            $sms_message = "Thank you $receiver_name!\nYour order $order_no costing KES $cash_on_delivery_amount has been successfully scheduled for delivery on $scheduled_date in $receiver_address by Boxleo Courier and Fulfillment Services Ltd.";
                            $sms_util = new SMSUtil();
                            $sms_util->sendSMS($receiver_phone, $sms_message);

                        }

                    } else if ($order_status == 'CANCELLED') {

                        $order_status = 'cancelled';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                    } else if ($order_status == 'DISPATCHED') {

                        $order_status = 'dispatched';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                    } else if ($order_status == 'UNDISPATCHED') {

                        $order_status = 'undispatched';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                    } else if ($order_status == 'INTRANSIT') {

                        $order_status = 'in_transit';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                    } else if ($order_status == 'EXPIRED') {

                        $order_status = 'expired';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                    } else if ($order_status == 'OUTOFSTOCK') {

                        $order_status = 'out_of_stock';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                    } else if ($order_status == 'NOTDISPATCHED') {

                        $order_status = 'not_dispatched';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                    } else if ($order_status == 'AWAITINGDISPATCH') {

                        $order_status = 'awaiting_dispatch';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                    } else if ($order_status == 'DELIVERYPENDING') {

                        $order_status = 'delivery_pending';
                        $status_date = date('Y-m-d');
                        $scheduled_date = '';

                    }
                }

                $format_phone_number_util = new FormatPhoneNumberUtil();
                $receiver_phone = $format_phone_number_util->formatPhoneNumber($receiver_phone);

                $order_object = new Order();
                $order_created = $order_object->create([
                    'order_no' => $order_no,
                    'is_sender_merchant' => true,
                    'merchant_id' => $merchant_id,
                    'sender_name' => $sender_name,
                    'sender_address' => $sender_address,
                    'sender_email' => $sender_email,
                    'sender_phone' => $sender_phone,
                    'sender_country' => $sender_country,
                    'sender_town' => $sender_town,
                    'receiver_name' => $receiver_name,
                    'receiver_address' => $receiver_address,
                    'receiver_phone' => $receiver_phone,
                    'receiver_phone_alternative' => $receiver_alternative_phone,
                    'receiver_country' => $receiver_country,
                    'receiver_town' => $receiver_town,
                    'payment_type' => 2,
                    'amount' => $cash_on_delivery_amount,
                    'cash_on_delivery' => $cash_on_delivery,
                    'cash_on_delivery_amount' => $cash_on_delivery_amount,
                    'order_status' => $order_status,
                    'status_date' => $status_date,
                    'scheduled_date' => $scheduled_date,
                    'agent' => $agent,
                    'special_instruction' => $special_instruction,

                ]);

                if ($order_created) {

                    $inventory_id = null;
                    $inventory = DB::table('inventories')
                        ->where('sku', $item_sku)
                        ->where('deleted_at', null)
                        ->first();
                    if ($inventory) {
                        $inventory_id = $inventory->id;
                    }

                    $inventory_product = false;
                    if ($inventory_id != null) {
                        $inventory_product = true;
                    }

                    $order_item = new OrderItem();
                    $item_created = $order_item->create([
                        'order_id' => $order_created->id,
                        'inventory_product' => $inventory_product,
                        'inventory_id' => $inventory_id,
                        'description' => $item_name,
                        'quantity' => $item_quantity,
                    ]);

                    if ($inventory_product == true) {
                        $update_order = DB::table('orders')
                            ->where('id', $order_created->id)
                            ->update([
                                'inventory' => true,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }

                    ++$this->rows;

                }

            } else {

                $this->message = "Order no exists. Please check again. Row no. $this->rows";
                return;
            }

        }

    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function getErrorMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 4;
    }
}
