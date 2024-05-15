<?php

namespace App\Http\Controllers;

use App\CallHistory;
use App\Http\Controllers\Util\SMSUtil;
use App\InventoryHistory;
use App\Models\UserEventTicketItem;
use App\Order;
use App\OrderItem;
use App\OrderLog;
use App\OrderScan;
use App\PaymentHistory;
use App\WaybillPrintLog;
use Illuminate\Support\Facades\DB;
use Response;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getImage($fileName){
        $path = public_path().'/uploads/images/'.$fileName;
        return Response::download($path);
    }

    public function getDocument($fileName){
        $path = public_path().'/uploads/documents/'.$fileName;
        return Response::download($path);
    }

    public function getAudio($fileName){
        $path = public_path().'/uploads/audio/'.$fileName;
        return Response::download($path);
    }

    public function getWaybill($fileName){
        $path = public_path().'/pdfs/'.$fileName;
        return Response::download($path);
    }

    public function checkSms(){

        $sender_phone = "254729304677";
        $sms_message = "Hi We have received your order request. We will notify you once it has been processed and dispatched";
        $sms_util = new SMSUtil();
        $sms_util->sendSMS($sender_phone, $sms_message);
    }

    public function deleteRecords(){

        DB::table('activity_log')
            ->whereDate('created_at', '<', '2022-06-01')
            ->delete();

        CallHistory::query()
            ->whereDate('created_at', '<', '2022-06-01')
            ->forceDelete();

        InventoryHistory::query()
            ->whereDate('created_at', '<', '2022-06-01')
            ->forceDelete();

        Order::query()
            ->whereDate('created_at', '<', '2022-06-01')
            ->forceDelete();

        OrderItem::query()
            ->whereDate('created_at', '<', '2022-06-01')
            ->forceDelete();

        OrderLog::query()
            ->whereDate('created_at', '<', '2022-06-01')
            ->forceDelete();

        OrderScan::query()
            ->whereDate('created_at', '<', '2022-06-01')
            ->forceDelete();

        PaymentHistory::query()
            ->whereDate('created_at', '<', '2022-06-01')
            ->forceDelete();

        WaybillPrintLog::query()
            ->whereDate('created_at', '<', '2022-06-01')
            ->forceDelete();

    }
}
