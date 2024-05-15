<?php

namespace App\Models;;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class MpesaPayment extends Model
{
    use Uuids;

    protected $fillable = [
        'FirstName', 'MiddleName','LastName', 'MSISDN', 'InvoiceNumber', 'BusinessShortCode', 'ThirdPartyTransID',
        'TransactionType', 'OrgAccountBalance', 'BillRefNumber', 'TransAmount',
    ];

    public $incrementing = false;
}
