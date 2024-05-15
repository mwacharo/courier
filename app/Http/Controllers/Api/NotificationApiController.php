<?php

namespace App\Http\Controllers\Api;

use App\Notification;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationApiController extends Controller
{
    public function getNotificationList()
    {

        $notifications = DB::table('notifications')
            ->where('deleted_at', null)
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->get();

        return json_encode($notifications);

    }

    public function updateNotificationDetails(Request $request){

        $update = DB::table('notifications')
            ->where('id', $request->id)
            ->update([
                'is_read'=>true,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if($update) {

            $json_array = array(
                'success' => 1,
            );

            $response = $json_array;
            return json_encode($response);

        } else {

            $json_array = array(
                'success' => 0,
            );

            $response = $json_array;
            return json_encode($response);
        }

    }

    public function getNotificationCount(){

        $notification_count = DB::table('notifications')
            ->where('is_read', 0)
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->where('deleted_at', null)
            ->count();

        $json_array = array(
            'notification_count' => $notification_count,
        );

        $response = $json_array;
        return json_encode($response);
    }


}
