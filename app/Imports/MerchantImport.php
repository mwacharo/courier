<?php


namespace App\Imports;

use App\Http\Controllers\Util\EmailUtil;
use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\PasswordGeneratorUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\Merchant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MerchantImport implements ToCollection, WithStartRow
{

    private $rows = 0;
    private $message = "";

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $name = $row[1];
            $merchant_type = $row[2];
            $address = $row[3];
            $country_name = $row[4];
            $town_name = $row[5];
            $phone_number = $row[6];
            $email = $row[7];
            $enable_cash_on_delivery_fee = $row[8];
            $cash_on_delivery_fee = $row[9];
            $enable_delivery_fee_nairobi = $row[10];
            $delivery_fee_nairobi = $row[11];
            $enable_delivery_fee_outbound = $row[12];
            $delivery_fee_outbound = $row[13];
            $enable_returns_management_fee = $row[14];
            $enable_warehousing_fee = $row[15];
            $warehousing_fee = $row[16];
            $enable_packaging_fee = $row[17];
            $packaging_fee = $row[18];
            $enable_call_centre_fee = $row[19];
            $call_centre_fee = $row[20];
            $enable_label_printing_fee = $row[21];
            $label_printing_fee = $row[22];

            $format_phone_number_util = new FormatPhoneNumberUtil();
            $phone_number = $format_phone_number_util->formatPhoneNumber($phone_number);

            if(strtolower($merchant_type) == 'individual'){
                $merchant_type = 0;
            }elseif (strtolower($merchant_type) == 'company'){
                $merchant_type = 1;
            }else{
                $merchant_type = 0;
            }

            $phone_number_check = DB::table('merchants')->where('phone_number', $phone_number)->first();
            if ($phone_number_check) {
                $this->message = "Phone number ($phone_number) already exist";
                return;
            }

            if ($email != '') {
                $email_check = DB::table('merchants')->where('email', $email)->first();
                if ($email_check) {
                    $this->message = "Email address ($email) already exist";
                    return;
                }
            }

            $country_id = "";
            $country = DB::table('countries')
                ->where('name', 'LIKE', "%{$country_name}%")
                ->where('deleted_at', null)
                ->first();
            if($country){
                $country_id = $country->id;
            }else{
                $country = DB::table('countries')
                    ->where('name', 'LIKE', "%Kenya%")
                    ->where('deleted_at', null)
                    ->first();
                if($country){
                    $country_id = $country->id;
                }
            }

            $town_id = "";
            $town = DB::table('towns')
                ->where('name', 'LIKE', "%{$town_name}%")
                ->where('deleted_at', null)
                ->first();
            if($town){
                $town_id = $town->id;
            }else{
                $town = DB::table('towns')
                    ->where('name', 'LIKE', "%Nairobi%")
                    ->where('deleted_at', null)
                    ->first();
                if($town){
                    $town_id = $town->id;
                }
            }

            if(strtolower($enable_cash_on_delivery_fee) == "true" || $enable_cash_on_delivery_fee == true){
                $enable_cash_on_delivery_fee = true;

            }else{
                $enable_cash_on_delivery_fee = false;
                $cash_on_delivery_fee = 0;
            }

            if(strtolower($enable_delivery_fee_nairobi) == "true" || $enable_delivery_fee_nairobi = true){
                $enable_delivery_fee_nairobi = true;
            }else{
                $delivery_fee_nairobi = 0;
                $enable_delivery_fee_nairobi = false;
            }

            if(strtolower($enable_delivery_fee_outbound) == "true" || $enable_delivery_fee_outbound = true){
                $enable_delivery_fee_outbound = true;
            }else{
                $delivery_fee_outbound = 0;
                $enable_delivery_fee_outbound = false;
            }

            if(strtolower($enable_returns_management_fee) == "true" || $enable_returns_management_fee = true){
                $enable_returns_management_fee = true;
            }else{
                $enable_returns_management_fee = false;
            }

            if(strtolower($enable_warehousing_fee) == "true" || $enable_warehousing_fee = true){
                $enable_warehousing_fee = true;
            }else{
                $warehousing_fee = 0;
                $enable_warehousing_fee = false;
            }

            if(strtolower($enable_packaging_fee) == "true" || $enable_packaging_fee = true){
                $enable_packaging_fee = true;
            }else{
                $packaging_fee = 0;
                $enable_packaging_fee = false;
            }

            if(strtolower($enable_call_centre_fee) == "true" || $enable_call_centre_fee = true){
                $enable_call_centre_fee = true;
            }else{
                $call_centre_fee = 0;
                $enable_call_centre_fee = false;
            }

            if(strtolower($enable_label_printing_fee) == "true" || $enable_label_printing_fee = true){
                $enable_label_printing_fee = true;
            }else{
                $label_printing_fee = 0;
                $enable_label_printing_fee = false;
            }

            $password_generator = new PasswordGeneratorUtil();
            $password = $password_generator->generatePassword();

            $merchant_object = new Merchant();
            $merchant_created = $merchant_object->create([
                'name' => $name,
                'merchant_type' => $merchant_type,
                'address' => $address,
                'phone_number' => $phone_number,
                'email' => $email,
                'country_id' => $country_id,
                'town_id' => $town_id,
                'enable_cash_on_delivery_fee' => $enable_cash_on_delivery_fee,
                'cash_on_delivery_fee' => $cash_on_delivery_fee,
                'enable_delivery_fee_nairobi' => $enable_delivery_fee_nairobi,
                'delivery_fee_nairobi' => $delivery_fee_nairobi,
                'enable_delivery_fee_outbound' => $enable_delivery_fee_outbound,
                'delivery_fee_outbound' => $delivery_fee_outbound,
                'enable_returns_management_fee' => $enable_returns_management_fee,
                'enable_warehousing_fee' => $enable_warehousing_fee,
                'warehousing_fee' => $warehousing_fee,
                'enable_packaging_fee' => $enable_packaging_fee,
                'packaging_fee' => $packaging_fee,
                'enable_call_centre_fee' => $enable_call_centre_fee,
                'call_centre_fee' => $call_centre_fee,
                'enable_label_printing_fee' => $enable_label_printing_fee,
                'label_printing_fee' => $label_printing_fee,
                'enabled' => true,
                'password' => bcrypt($password),
            ]);

            if ($merchant_created) {

                ++$this->rows;

                $email_util = new EmailUtil();
                $send_email = $email_util->merchantWelcomeEmail($merchant_created->name, $merchant_created->email, $password);

                $sms_message = "Hi $merchant_created->name !, You have been added as an merchant. We have sent a login credentials to your email";
                $sms_util = new SMSUtil();
                $sms_util->sendSMS($phone_number, $sms_message);

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
