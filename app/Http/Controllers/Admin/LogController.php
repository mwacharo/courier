<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     *  ADMIN LOGS
     */

    public function adminListLog(){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View admin list');

    }

    public function adminDetailsLog($admin_id, $name){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->withProperties(['id' => $admin_id, 'name' => $name])
            ->log('View admin details');

    }

    public function adminCreateLog($causer_id, $admin_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $admin_id, 'name' => $name])
            ->log('Create admin details');

    }

    public function adminEditLog($causer_id, $admin_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $admin_id, 'name' => $name])
            ->log('Edit admin details');

    }

    public function adminDeleteLog($causer_id, $admin_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $admin_id, 'name' => $name])
            ->log('Edit admin details');

    }

    /**
     *  COUNTRY LOGS
     */

    public function countryListLog(){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View country list');

    }

    public function countryDetailsLog($country_id, $name){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->withProperties(['id' => $country_id, 'name' => $name])
            ->log('View country details');

    }

    public function countryCreateLog($causer_id, $country_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $country_id, 'name' => $name])
            ->log('Create country details');

    }

    public function countryEditLog($causer_id, $country_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $country_id, 'name' => $name])
            ->log('Edit country details');

    }

    public function countryDeleteLog($causer_id, $country_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $country_id, 'name' => $name])
            ->log('Edit country details');

    }

    /**
     *  TOWN LOGS
     */

    public function townListLog(){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View town list');

    }

    public function townDetailsLog($town_id, $name){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->withProperties(['id' => $town_id, 'name' => $name])
            ->log('View town details');

    }

    public function townCreateLog($causer_id, $town_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $town_id, 'name' => $name])
            ->log('Create town details');

    }

    public function townEditLog($causer_id, $town_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $town_id, 'name' => $name])
            ->log('Edit town details');

    }

    public function townDeleteLog($causer_id, $town_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $town_id, 'name' => $name])
            ->log('Edit town details');

    }

    /**
     *  ZONE LOGS
     */

    public function zoneListLog(){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View zone list');

    }

    public function zoneDetailsLog($zone_id, $name){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->withProperties(['id' => $zone_id, 'name' => $name])
            ->log('Create zone details');

    }

    public function zoneCreateLog($causer_id, $zone_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $zone_id, 'name' => $name])
            ->log('Create zone details');

    }

    public function zoneEditLog($causer_id, $zone_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $zone_id, 'name' => $name])
            ->log('Edit zone details');

    }

    public function zoneDeleteLog($causer_id, $zone_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $zone_id, 'name' => $name])
            ->log('Edit zone details');

    }

    /**
     *  BRANCH LOGS
     */

    public function branchListLog(){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View branch list');

    }

    public function branchDetailsLog($branch_id, $name){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->withProperties(['id' => $branch_id, 'name' => $name])
            ->log('View branch details');

    }

    public function branchCreateLog($causer_id, $branch_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $branch_id, 'name' => $name])
            ->log('Create branch details');

    }

    public function branchEditLog($causer_id, $branch_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $branch_id, 'name' => $name])
            ->log('Edit branch details');

    }

    public function branchDeleteLog($causer_id, $branch_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $branch_id, 'name' => $name])
            ->log('Edit branch details');

    }

    /**
     *  MERCHANT LOGS
     */

    public function merchantListLog(){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View merchant list');

    }

    public function merchantDetailsLog($merchant_id, $name){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->withProperties(['id' => $merchant_id, 'name' => $name])
            ->log('View merchant details');

    }

    public function merchantCreateLog($causer_id, $merchant_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $merchant_id, 'name' => $name])
            ->log('Create merchant details');

    }

    public function merchantEditLog($causer_id, $merchant_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $merchant_id, 'name' => $name])
            ->log('Edit merchant details');

    }

    public function merchantDeleteLog($causer_id, $merchant_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $merchant_id, 'name' => $name])
            ->log('Edit merchant details');

    }

    /**
     *  RIDER LOGS
     */

    public function riderListLog(){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View rider list');

    }

    public function riderCreateLog($causer_id, $rider_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $rider_id, 'name' => $name])
            ->log('Create rider details');

    }

    public function riderDetailsLog($rider_id, $name){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->withProperties(['id' => $rider_id, 'name' => $name])
            ->log('View rider details');

    }

    public function riderEditLog($causer_id, $rider_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $rider_id, 'name' => $name])
            ->log('Edit rider details');

    }

    public function riderDeleteLog($causer_id, $rider_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $rider_id, 'name' => $name])
            ->log('Edit rider details');

    }

    /**
     *  INVENTORY LOGS
     */

    public function inventoryListLog(){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View inventory list');

    }

    public function inventoryCreateLog($causer_id, $inventory_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $inventory_id, 'name' => $name])
            ->log('Create inventory details');

    }

    public function inventoryDetailsLog($inventory_id, $name){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->withProperties(['id' => $inventory_id, 'name' => $name])
            ->log('View inventory details');

    }

    public function inventoryEditLog($causer_id, $inventory_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $inventory_id, 'name' => $name])
            ->log('Edit inventory details');

    }

    public function inventoryDeleteLog($causer_id, $inventory_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $inventory_id, 'name' => $name])
            ->log('Edit inventory details');

    }

    /**
     *  ORDER LOGS
     */

    public function orderListLog(){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View order list');

    }

    public function orderDetailsLog($order_id, $name){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->withProperties(['id' => $order_id, 'name' => $name])
            ->log('Create order details');

    }

    public function orderCreateLog($causer_id, $order_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $order_id, 'name' => $name])
            ->log('Create order details');

    }

    public function orderEditLog($causer_id, $order_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $order_id, 'name' => $name])
            ->log('Edit order details');

    }

    public function orderDeleteLog($causer_id, $order_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $order_id, 'name' => $name])
            ->log('Edit order details');

    }

    public function orderInscanLog($causer_id, $order_id, $order_no){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $order_id, 'name' => $order_no])
            ->log('Order inscan details');

    }

    public function orderOutscanLog($causer_id, $order_id, $order_no){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $order_id, 'name' => $order_no])
            ->log('Order inscan details');

    }

    /**
     *  SCHEDULE LOGS
     */

    public function scheduleListLog(){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View schedule list');

    }

    public function scheduleDetailsLog($schedule_id, $name){

        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->withProperties(['id' => $schedule_id, 'name' => $name])
            ->log('View schedule details');

    }

    public function scheduleCreateLog($causer_id, $schedule_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $schedule_id, 'name' => $name])
            ->log('Create schedule details');

    }

    public function scheduleEditLog($causer_id, $schedule_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $schedule_id, 'name' => $name])
            ->log('Edit schedule details');

    }

    public function scheduleDeleteLog($causer_id, $schedule_id, $name){

        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->withProperties(['id' => $schedule_id, 'name' => $name])
            ->log('Edit schedule details');

    }

    public function reportDeliveryGenerateLog($causer_id)
    {
        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->log('Generate delivery report');
    }

    public function reportInventoryGenerateLog($causer_id)
    {
        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->log('Generate inventory report');
    }

    public function reportRiderGenerateLog($causer_id)
    {
        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->log('Generate rider report');
    }

    public function reportMerchantGenerateLog($causer_id)
    {
        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->log('Generate merchant report');
    }

    public function reportOrderGenerateLog($causer_id)
    {
        $admin = Admin::where('id',$causer_id)->first();
        activity()
            ->causedBy($admin)
            ->log('Generate order report');
    }

    public function viewProfile()
    {
        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View profile');
    }

    public function notificationListLog()
    {
        $user = Auth::user();
        activity()
            ->causedBy($user)
            ->log('View notification list');
    }


}


