<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//
//});

Route::group(['middleware' => 'auth:api'], function() {

});

Route::get('v1/admin-list','Api\AdminApiController@getAdminList');
Route::post('v1/admin-create','Api\AdminApiController@createAdminDetails');
Route::post('v1/admin-edit','Api\AdminApiController@editAdminDetails');
Route::post('v1/admin-delete','Api\AdminApiController@deleteAdminDetails');
Route::post('v1/admin-edit-permission','Api\AdminApiController@adminPermissionsEdit');
Route::get('v1/admin-details/{id}','Api\AdminApiController@getAdminDetails');
Route::post('v1/admin-delete-image','Api\AdminApiController@deleteProfileImage');
Route::post('v1/admin-reset-password','Api\AdminApiController@resetPassword');
Route::post('v1/admin-password-change','Api\AdminApiController@changePassword');


Route::post('v1/rider-create','Api\RiderApiController@createRiderDetails');
Route::post('v1/rider-edit','Api\RiderApiController@editRiderDetails');
Route::post('v1/rider-delete','Api\RiderApiController@deleteRiderDetails');
Route::get('v1/rider-details/{id}','Api\RiderApiController@getRiderDetails');
Route::get('v1/rider-list','Api\RiderApiController@getRiderList');
Route::post('v1/rider-delete-image','Api\RiderApiController@deleteProfileImage');
Route::post('v1/rider-reset-password-admin','Api\RiderApiController@resetPassword');

Route::post('v1/merchant-create','Api\MerchantApiController@createMerchantDetails');
Route::post('v1/merchant-edit','Api\MerchantApiController@editMerchantDetails');
Route::post('v1/merchant-delete','Api\MerchantApiController@deleteMerchantDetails');
Route::get('v1/merchant-details/{id}','Api\MerchantApiController@getMerchantDetails');
Route::get('v1/merchant-list','Api\MerchantApiController@getMerchantList');
Route::post('v1/merchant-delete-image','Api\MerchantApiController@deleteProfileImage');
Route::post('v1/merchant-delete-contract','Api\MerchantApiController@deleteMerchantContract');
Route::post('v1/merchant-reset-password','Api\MerchantApiController@resetPassword');
Route::post('v1/merchant-password-change','Api\MerchantApiController@changePassword');

Route::post('v1/branch-create','Api\BranchApiController@createBranchDetails');
Route::post('v1/branch-edit','Api\BranchApiController@editBranchDetails');
Route::post('v1/branch-delete','Api\BranchApiController@deleteBranchDetails');
Route::get('v1/branch-list','Api\BranchApiController@getBranchList');
Route::get('v1/branch-details/{id}','Api\BranchApiController@getBranchDetails');

Route::post('v1/country-create','Api\CountryApiController@createCountryDetails');
Route::post('v1/country-edit','Api\CountryApiController@editCountryDetails');
Route::post('v1/country-delete','Api\CountryApiController@deleteCountryDetails');
Route::get('v1/country-list','Api\CountryApiController@getCountryList');
Route::get('v1/country-details/{id}','Api\CountryApiController@getCountryDetails');

Route::post('v1/town-create','Api\TownApiController@createTownDetails');
Route::post('v1/town-edit','Api\TownApiController@editTownDetails');
Route::post('v1/town-delete','Api\TownApiController@deleteTownDetails');
Route::get('v1/town-list','Api\TownApiController@getTownList');
Route::get('v1/town-details/{id}','Api\TownApiController@getTownDetails');

Route::post('v1/zone-create','Api\InboundZoneApiController@createZoneDetails');
Route::post('v1/zone-edit','Api\InboundZoneApiController@editZoneDetails');
Route::post('v1/zone-delete','Api\InboundZoneApiController@deleteZoneDetails');
Route::get('v1/zone-list','Api\InboundZoneApiController@getZoneList');
Route::get('v1/zone-details/{id}','Api\InboundZoneApiController@getZoneDetails');

Route::post('v1/schedule-outbound-create','Api\OutboundScheduleApiController@createScheduleDetails');
Route::post('v1/schedule-outbound-edit','Api\OutboundScheduleApiController@editScheduleDetails');
Route::post('v1/schedule-outbound-delete','Api\OutboundScheduleApiController@deleteScheduleDetails');
Route::get('v1/schedule-outbound-list','Api\OutboundScheduleApiController@getScheduleList');
Route::get('v1/schedule-outbound-details/{id}','Api\OutboundScheduleApiController@getScheduleDetails');

Route::post('v1/outbound-delivery-charge-calculator','Api\ChargeCalculatorController@outboundChargeCalculator');
Route::post('v1/inbound-delivery-charge-calculator','Api\ChargeCalculatorController@inboundChargeCalculator');

Route::post('v1/inventory-create','Api\InventoryApiController@createInventoryDetails');
Route::post('v1/inventory-edit','Api\InventoryApiController@editInventoryDetails');
Route::post('v1/inventory-delete','Api\InventoryApiController@deleteInventoryDetails');
Route::get('v1/inventory-details/{id}','Api\InventoryApiController@getInventoryDetails');
Route::get('v1/inventory-list','Api\InventoryApiController@getInventoryList');
Route::post('v1/inventory-delete-image','Api\InventoryApiController@deleteImage');
Route::post('v1/inventory-search','Api\InventoryApiController@searchInventory');
Route::post('v1/merchant-inventory-search','Api\InventoryApiController@searchMerchantInventory');
Route::get('v1/inventory-search-name/{search_query}','Api\InventoryApiController@searchInventoryName');
Route::post('v1/merchant-inventory-search-name','Api\InventoryApiController@searchMerchantInventoryName');
Route::post('v1/inventory-order-search','Api\InventoryApiController@searchOrder');

Route::post('v1/order-inscan-create','Api\OrderScanApiController@orderInscan');
Route::post('v1/order-outscan-create','Api\OrderScanApiController@orderOutscan');

Route::post('v1/order-create','Api\OrderApiController@createOrderDetails');
Route::post('v1/order-edit','Api\OrderApiController@editOrderDetails');
Route::post('v1/order-delete','Api\OrderApiController@deleteOrderDetails');
Route::get('v1/order-list','Api\OrderApiController@getOrderList');
Route::get('v1/fetch-orders/{per_page}','Api\OrderApiController@fetchOrders');

//scheduled orders
Route::get('v1/scheduled-order-list','Api\OrderApiController@getScheduledOrderList');
Route::get('v1/order-followup-list','Api\OrderApiController@getOrderFollowupList');
Route::get('v1/order-pending-list','Api\OrderApiController@getOrderPendingList');
Route::get('v1/order-details/{id}','Api\OrderApiController@getOrderDetails');
Route::get('v1/order-items/{id}','Api\OrderApiController@getOrderItems');
Route::post('v1/order-item-delete','Api\OrderApiController@deleteOrderItemDetails');
Route::post('v1/order-assign-rider','Api\OrderApiController@assignRider');
Route::post('v1/order-search','Api\OrderApiController@searchOrder');
Route::post('v1/order-scheduled','Api\OrderApiController@scheduledOrder');
Route::post('v1/order-intransit','Api\OrderApiController@inTransitOrder');
Route::post('v1/order-awaiting-dispatch','Api\OrderApiController@awaitingOrder');
Route::post('v1/order-dispatched','Api\OrderApiController@dispatchedOrder');
Route::post('v1/order-undispatched','Api\OrderApiController@unDispatchedOrder');
//new routes
Route::post('v1/order-undispatch','Api\OrderApiController@undispatchOrder');
Route::post('v1/order-transit','Api\OrderApiController@transitOrder');

Route::post('v1/order-awaiting-dispatch-generate-excel','Api\OrderApiController@awaitingOrderGenerateExcel');
Route::post('v1/order-duplicate','Api\OrderApiController@duplicateOrder');
Route::post('v1/order-duplicate-generate-excel','Api\OrderApiController@duplicateOrderGenerateExcel');
Route::post('v1/order-duplicate-generate-pdf','Api\OrderApiController@duplicateOrderGeneratePdf');
Route::get('v1/order-dispatch-policy-default','Api\OrderApiController@dispatchPolicyOrderDefault');
Route::post('v1/order-dispatch-policy','Api\OrderApiController@dispatchPolicyOrder');
Route::post('v1/order-dispatch-policy-generate-excel','Api\OrderApiController@dispatchPolicyOrderGenerateExcel');
Route::post('v1/order-dispatch-policy-generate-pdf','Api\OrderApiController@dispatchPolicyOrderGeneratePdf');

Route::post('v1/order-scheduled-generate-excel','Api\OrderApiController@scheduledOrderGenerateExcel');
Route::post('v1/order-undispatched-generate-excel','Api\OrderApiController@undispatchedOrderGenerateExcel');
Route::post('v1/order-scheduled-bulkprint','Api\OrderApiController@scheduledOrderBulkPrint');
Route::post('v1/order-update-status','Api\OrderApiController@updateOrderStatus');
Route::post('v1/order-update-special-instruction','Api\OrderApiController@updateSpecialInstruction');
Route::post('v1/order-update-scheduled-date','Api\OrderApiController@updateScheduledDate');
Route::post('v1/order-update-status-date','Api\OrderApiController@updateStatusDate');
Route::post('v1/order-update-location','Api\OrderApiController@updateLocationDetails');
Route::post('v1/order-update-agent','Api\OrderApiController@updateAgentDetails');
Route::post('v1/order-update-receiver','Api\OrderApiController@updateReceiverDetails');
Route::post('v1/order-update-upsell','Api\OrderApiController@updateUpsellDetails');
Route::post('v1/order-search-page','Api\ReportApiController@searchOrder');

Route::get('v1/send-scheduled-orders','CronJobController@sendScheduledOrderGeneratePdf');
Route::get('v1/waybill-scheduled-orders/{filename}','ApiController@getWaybill');

Route::post('v1/order-followup','Api\OrderApiController@followupOrder');
Route::post('v1/order-followup-search-page','Api\ReportApiController@searchFollowOrder');
Route::post('v1/order-pending','Api\OrderApiController@pendingOrder');
Route::post('v1/order-pending-search-page','Api\ReportApiController@searchPendingOrder');
Route::post('v1/order-followup-generate-excel','Api\OrderApiController@followupOrderGenerateExcel');
Route::post('v1/order-followup-generate-pdf','Api\OrderApiController@followupOrderGeneratePdf');

Route::post('v1/report-order','Api\ReportApiController@reportOrder');
Route::post('v1/report-order-generate','Api\ReportApiController@reportOrderGenerate');
Route::post('v1/report-order-csv','Api\ReportApiController@reportOrderGenerate');
Route::post('v1/report-merchant','Api\ReportApiController@reportMerchant');
Route::post('v1/report-merchant-generate','Api\ReportApiController@reportMerchantGenerate');
Route::post('v1/report-rider','Api\ReportApiController@reportRider');
Route::post('v1/report-rider-generate','Api\ReportApiController@reportRiderGenerate');
Route::post('v1/report-delivery','Api\ReportApiController@reportDelivery');
Route::post('v1/report-delivery-generate','Api\ReportApiController@reportDeliveryGenerate');
Route::post('v1/report-inventory','Api\ReportApiController@reportInventory');
Route::post('v1/report-inventory-generate','Api\ReportApiController@reportInventoryGenerate');
Route::post('v1/report-sku','Api\ReportApiController@reportSku');
Route::post('v1/report-sku-generate','Api\ReportApiController@reportSkuGenerate');
Route::post('v1/report-merchant-remittance','Api\ReportApiController@reportMerchantRemittance');
Route::post('v1/report-merchant-remittance-generate','Api\ReportApiController@reportMerchantRemittanceGenerate');
Route::get('v1/report-merchandise-remittance/{id}','Api\ReportApiController@reportMerchandiseRemittance');

Route::post('v1/report-admin-activity','Api\ReportApiController@adminActivity');
Route::post('v1report-admin-activity-generate','Api\ReportApiController@reportOrderGenerate');

Route::post('v1/report-rider-outscan','Api\ReportApiController@reportRiderOutscan');
Route::post('v1/report-rider-intransit','Api\ReportApiController@reportRiderIntransit');
Route::post('v1/report-rider-outscan-generate','Api\ReportApiController@reportRiderOutscanGenerate');
Route::post('v1/report-rider-intransit-generate','Api\ReportApiController@reportRiderIntransitGenerate');
Route::post('v1/report-rider-outscan-generate-pdf','Api\ReportApiController@reportRiderOutscanPdfGenerate');
Route::post('v1/report-rider-dispatched-generate','Api\ReportApiController@reportRiderDispatchedGenerate');

Route::get('/v1/get-image/{fileName}','ApiController@getImage');
Route::get('/v1/get-document/{fileName}','ApiController@getDocument');
Route::get('/v1/get-audio/{fileName}','ApiController@getAudio');

Route::post('v1/notification-edit','Api\NotificationApiController@updateNotificationDetails');
Route::get('v1/notification-list','Api\NotificationApiController@getNotificationList');
Route::get('v1/notification-count','Api\NotificationApiController@getNotificationCount');

/**
 * MERCHANT PORTAL ROUTES
 */

Route::get('v1/merchant-order-list/{id}','Api\MerchantOrderApiController@getOrderList');
Route::post('v1/merchant-order-search-page','Api\MerchantOrderApiController@searchOrder');
Route::post('v1/merchant-order-create','Api\MerchantOrderApiController@createOrderDetails');
Route::get('v1/merchant-order-details/{id}','Api\MerchantOrderApiController@getOrderDetails');
Route::get('v1/merchant-order-items/{id}','Api\MerchantOrderApiController@getOrderItems');
Route::post('v1/merchant-outbound-delivery-charge-calculator','Api\MerchantChargeCalculatorController@outboundChargeCalculator');
Route::post('v1/merchant-inbound-delivery-charge-calculator','Api\MerchantChargeCalculatorController@inboundChargeCalculator');
Route::post('v1/merchant-order-report','Api\MerchantReportApiController@reportMerchant');
Route::post('v1/merchant-order-report-csv','Api\MerchantReportApiController@reportMerchant');
Route::post('v1/merchant-order-report-generate','Api\MerchantReportApiController@reportMerchantGenerate');

/**
 * MOBILE APP ROUTES
 */

Route::post('/v1/login-rider', 'Api\MobileApiController@riderLogin')->name('login-rider');
Route::post('/v1/rider-forgot-password', 'Api\MobileApiController@riderForgotPassword')->name('rider-forgot-password');
Route::post('/v1/rider-reset-password', 'Api\MobileApiController@riderResetPassword')->name('rider-reset-password');
Route::post('/v1/rider-profile-details', 'Api\MobileApiController@riderProfileDetails')->name('rider-profile-details');
Route::post('/v1/rider-profile-image-update', 'Api\MobileApiController@riderUpdateProfileImage')->name('rider-profile-image-update');
Route::post('/v1/rider-profile-password-update', 'Api\MobileApiController@riderUpdateProfilePassword')->name('rider-profile-password-update');
Route::post('/v1/rider-order-history', 'Api\MobileApiController@riderOrderHistory')->name('rider-order-history');
Route::post('/v1/rider-order-history-details', 'Api\MobileApiController@riderOrderHistoryDetails')->name('rider-order-history-details');
Route::post('/v1/rider-order-history-active', 'Api\MobileApiController@riderActiveOrderHistory')->name('rider-order-history-active');
Route::post('/v1/rider-delivery-history', 'Api\MobileApiController@riderDeliveryHistory')->name('rider-delivery-history');
Route::post('/v1/rider-order-history-status', 'Api\MobileApiController@riderOrderHistoryStatus')->name('rider-order-history-status');
Route::post('/v1/rider-order-payment-status', 'Api\MobileApiController@riderOrderPaymentStatus')->name('rider-order-payment-status');
Route::get('/v1/rider-order-waybill/{id}', 'Api\MobileApiController@riderOrderWaybill')->name('rider-order-waybill');
Route::get('/v1/order-waybill/{id}', 'Api\MobileApiController@riderOrderWaybill')->name('order-waybill');
Route::get('/v1/google-sheets-import', 'GoogleSheetController@importOrders')->name('google-sheets-import');


/**
 * PAYMENT ROUTES
 */

Route::post('confirmation', 'Api\PaymentApiController@paymentConfirmation');
Route::post('validation', 'Api\PaymentApiController@paymentValidation');
Route::get('register-url', 'Api\PaymentApiController@registerUrl');
Route::post('/v1/check-payment', 'Api\PaymentApiController@checkPayment');
Route::get('/v1/check-sms', 'ApiController@checkSms');

Route::get('v1/mpesa-codes','Api\MpesaCodesApiController@index');


/**
 * 3RD PARTY ROUTES
 */

Route::get('/v1/address-countries', 'Api\IntergrationApiController@getCountryList');
Route::get('/v1/address-towns/{id}', 'Api\IntergrationApiController@getTownList');
Route::post('/v1/shipping-calculator', 'Api\IntergrationApiController@shippingCalculator');
Route::post('/v1/shipping-order-create', 'Api\IntergrationApiController@createOrder');
Route::post('/v1/shipping-track-order', 'Api\IntergrationApiController@shippingTrackOrder');

/**
 * INTEGRATION ROUTES
 */

Route::post('v1/integration-order-edit','Api\OrderApiController@integrationEditOrder');
Route::post('v1/integration-order-details','Api\OrderApiController@integrationOrderDetails');
Route::post('v1/integration-order-details-phone','Api\OrderApiController@integrationOrderDetailsPhone');
Route::post('v1/integration-order-delete','Api\OrderApiController@integrationOrderDelete');
Route::post('v1/integration-order-delete-multiple','Api\OrderApiController@integrationOrderDeleteMultiple');
Route::post('v1/integration-sms','Api\OrderApiController@intergrationSms');
Route::post('v1/integration-auth-login','Api\AuthApiController@integrationLogin');
Route::post('v1/integration-auth-logout','Api\AuthApiController@integrationLogout');

Route::post('v1/integration-inventory-list','Api\InventoryApiController@merchantInventoryList');
Route::post('v1/integration-inventory-details','Api\InventoryApiController@merchantInventoryDetails');

/**
 * CALL CENTRE
 */

Route::post('v1/africastalking-handle-callback','Api\CallCenterApiController@handleVoiceCallback');
Route::post('v1/africastalking-handle-event','Api\CallCenterApiController@handleEventCallback');

Route::get('v1/call-centre-make-call','Api\CallCenterApiController@makeOutboundCall');
Route::get('v1/call-centre-upload-media','Api\CallCenterApiController@uploadMediaFile');
Route::get('v1/call-centre-play-welcome','Api\CallCenterApiController@messageBuilderPlayWelcome');
Route::post('v1/call-centre-transfer-call','Api\CallCenterApiController@transferCall');
Route::post('v1/call-centre-dequeue-call','Api\CallCenterApiController@dequeueCall');
Route::post('v1/call-centre-generate-token','Api\CallCenterApiController@getToken');
Route::get('v1/call-waiting-history','Api\CallCenterApiController@getCallWaitingHistory');
Route::get('v1/call-agent-history/{id}','Api\CallCenterApiController@getAgentCallHistory');

Route::get('v1/report-call-waiting-history','Api\CallCenterApiController@getCallWaitingHistory');
Route::get('v1/report-call-ongoing-history','Api\CallCenterApiController@getCallOngoingHistory');
Route::get('v1/report-call-agent-list-summary','Api\CallCenterApiController@getAgentListSummary');
Route::post('v1/report-call-agent-list-summary-filter','Api\CallCenterApiController@getAgentListSummaryFilter');
Route::get('v1/report-call-history-list','Api\CallCenterApiController@getCallHistory');
Route::post('v1/report-call-history-list-filter','Api\CallCenterApiController@getCallHistoryFilter');
Route::get('v1/report-call-centre-summary','Api\CallCenterApiController@getSummaryReport');
Route::get('v1/call-order-history/{phone_number}','Api\CallCenterApiController@callOrderHistory');


Route::post('v1/call-agent-create','Api\CallAgentApiController@createCallAgentDetails');
Route::post('v1/call-agent-edit','Api\CallAgentApiController@editCallAgentDetails');
Route::post('v1/call-agent-edit-status','Api\CallAgentApiController@editCallAgentStatus');
Route::post('v1/call-agent-delete','Api\CallAgentApiController@deleteCallAgentDetails');
Route::get('v1/call-agent-list','Api\CallAgentApiController@getCallAgentList');
Route::get('v1/call-agent-available-list','Api\CallAgentApiController@getCallAgentListAvailable');
Route::get('v1/call-agent-details/{id}','Api\CallAgentApiController@getCallAgentDetails');
Route::get('v1/call-agent-details-2/{id}','Api\CallAgentApiController@getCallAgentDetails2');
Route::get('v1/call-agent-summary/{id}','Api\CallAgentApiController@getCallAgentSummary');

Route::get('v1/delete-records','ApiController@deleteRecords');


