<?php


namespace App\Imports;

use App\Http\Controllers\Util\FormatPhoneNumberUtil;
use App\Http\Controllers\Util\PasswordGeneratorUtil;
use App\Http\Controllers\Util\SMSUtil;
use App\Rider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class RiderImport implements ToCollection, WithStartRow
{

    private $rows = 0;
    private $message = "";

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $first_name = $row[1];
            $last_name = $row[2];
            $date_of_birth = $row[3];
            $national_id = $row[4];
            $country_name = $row[5];
            $phone_number = $row[6];
            $email = $row[7];

            $format_phone_number_util = new FormatPhoneNumberUtil();
            $phone_number = $format_phone_number_util->formatPhoneNumber($phone_number);

            $phone_number_check = DB::table('riders')->where('phone_number', $phone_number)->first();
            if ($phone_number_check) {
                $this->message = "Phone number ($phone_number) already exist";
                return;
            }

            if ($email != '') {
                $email_check = DB::table('riders')->where('email', $email)->first();
                if ($email_check) {
                    $this->message = "Email address ($email) already exist";
                    return;
                }
            }

            if ($national_id != '') {
                $national_id_check = DB::table('riders')->where('national_id', $national_id)->first();
                if ($national_id_check) {
                    $this->message = "National ID ($national_id) already exist";
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

            $password_generator = new PasswordGeneratorUtil();
            $password = $password_generator->generatePassword();

            $rider_object = new Rider();
            $rider_created = $rider_object->create([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'date_of_birth' => $date_of_birth,
                'national_id' => $national_id,
                'country_id' => $country_id,
                'phone_number' => $phone_number,
                'email' => $email,
                'enabled' => true,
                'password' => bcrypt($password),
            ]);

            if ($rider_created) {

                ++$this->rows;

//                $email_util = new EmailUtil();
//                $send_email = $email_util->riderWelcomeEmail($rider_created->first_name, $rider_created->email, $password);

                $sms_message = "Hi $rider_created->first_name !, You have been added as an rider. We have sent a login credentials to your email";
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
