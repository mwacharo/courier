<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Auth\AdminLoginController;

Auth::routes();

Route::get('/trial', 'CronJobController@scheduledOrderNotification')->name('trial');
Route::get('/privacy-policy', 'HomeController@privacyPolicy')->name('privacy-policy');
Route::get('/order-tracking', 'OrderTrackingController@index')->name('order.tracking');

Route::get('/', [HomeController::class, 'index'])->name('admin.dashboard');
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::get('/logout', [AdminLoginController::class, 'logout'])->name('admin-logout');
Route::get('/forgot-password-form', [AdminLoginController::class, 'forgotPaswordForm'])->name('admin-forgot-password-form');
Route::post('/reset-password', [AdminLoginController::class, 'resetPassword'])->name('admin-reset-password');
Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');


/**
 * ADMIN ROUTES
 */
Route::get('/admin', [AdminController::class, 'index'])->name('admin.admin');
Route::get('/admin-create', [AdminController::class, 'create'])->name('admin.admin.create');
Route::get('/admin-activity', [AdminController::class, 'adminActivity'])->name('admin.admin.activity');
Route::get('/admin-details/{id}', [AdminController::class, 'details'])->name('admin.admin.details');
Route::post('/admin-permissions-edit/{id}', [AdminController::class, 'permissionsEdit'])->name('admin.admin.permissions');
Route::get('/admin-report-excel', [AdminController::class, 'reportExcel'])->name('admin.admin.report.excel');
Route::get('/admin-report-pdf', [AdminController::class, 'reportPdf'])->name('admin.admin.report.pdf');


/**
 * RIDER ROUTES
 */
Route::get('/rider', 'Admin\RiderController@index')->name('admin.rider');
Route::get('/rider-create', 'Admin\RiderController@create')->name('admin.rider.create');
Route::get('/rider-details/{id}', 'Admin\RiderController@details')->name('admin.rider.details');
Route::get('/rider-report-excel', 'Admin\RiderController@reportExcel')->name('admin.rider.report.excel');
Route::get('/rider-report-pdf', 'Admin\RiderController@reportPdf')->name('admin.rider.report.pdf');
Route::get('/rider-import', 'Admin\RiderController@importExcel')->name('admin.rider.import');
Route::post('/rider-import-upload', 'Admin\RiderController@importExcelUpload')->name('admin.rider.import.upload');

/**
 * MERCHANTS ROUTES
 */
Route::get('/merchant', 'Admin\MerchantController@index')->name('admin.merchant');
Route::get('/merchant-create', 'Admin\MerchantController@create')->name('admin.merchant.create');
Route::get('/merchant-details/{id}', 'Admin\MerchantController@details')->name('admin.merchant.details');
Route::get('/merchant-report-excel', 'Admin\MerchantController@reportExcel')->name('admin.merchant.report.excel');
Route::get('/merchant-report-pdf', 'Admin\MerchantController@reportPdf')->name('admin.merchant.report.pdf');
Route::get('/merchant-import', 'Admin\MerchantController@importExcel')->name('admin.merchant.import');
Route::post('/merchant-import-upload', 'Admin\MerchantController@importExcelUpload')->name('admin.merchant.import.upload');
Route::get('/merchant-import-google-sheet/{id}/{sheet}', 'Admin\MerchantController@importGoogleExcelSubmit')->name('admin.merchant.import.google');

/**
 * COUNTRY ROUTES
 */

Route::get('/country', 'Admin\CountryController@index')->name('admin.country');
Route::get('/country-create', 'Admin\CountryController@create')->name('admin.country.create');
Route::get('/country-details/{id}', 'Admin\CountryController@details')->name('admin.country.details');
Route::get('/country-report-excel', 'Admin\CountryController@reportExcel')->name('admin.country.report.excel');
Route::get('/country-report-pdf', 'Admin\CountryController@reportPdf')->name('admin.country.report.pdf');

/**
 * TOWNS ROUTES
 */

Route::get('/town', 'Admin\TownController@index')->name('admin.town');
Route::get('/town-create', 'Admin\TownController@create')->name('admin.town.create');
Route::get('/town-details/{id}', 'Admin\TownController@details')->name('admin.town.details');
Route::get('/town-report-excel', 'Admin\TownController@reportExcel')->name('admin.town.report.excel');
Route::get('/town-report-pdf', 'Admin\TownController@reportPdf')->name('admin.town.report.pdf');

/**
 * ZONES ROUTES
 */

Route::get('/zone', 'Admin\InboundZoneController@index')->name('admin.zone');
Route::get('/zone-create', 'Admin\InboundZoneController@create')->name('admin.zone.create');
Route::get('/zone-details/{id}', 'Admin\InboundZoneController@details')->name('admin.zone.details');
Route::get('/zone-report-excel', 'Admin\InboundZoneController@reportExcel')->name('admin.zone.report.excel');
Route::get('/zone-report-pdf', 'Admin\InboundZoneController@reportPdf')->name('admin.zone.report.pdf');

/**
 * BRANCHES ROUTES
 */

Route::get('/branch', 'Admin\BranchController@index')->name('admin.branch');
Route::get('/branch-create', 'Admin\BranchController@create')->name('admin.branch.create');
Route::get('/branch-details/{id}', 'Admin\BranchController@details')->name('admin.branch.details');
Route::get('/branch-report-excel', 'Admin\BranchController@reportExcel')->name('admin.branch.report.excel');
Route::get('/branch-report-pdf', 'Admin\BranchController@reportPdf')->name('admin.branch.report.pdf');

/**
 * INVENTORIES ROUTES
 */
Route::get('/inventory', 'Admin\InventoryController@index')->name('admin.inventory');
Route::get('/inventory-create', 'Admin\InventoryController@create')->name('admin.inventory.create');
Route::get('/inventory-details/{id}', 'Admin\InventoryController@details')->name('admin.inventory.details');
Route::get('/inventory-report-excel', 'Admin\InventoryController@reportExcel')->name('admin.inventory.report.excel');
Route::get('/inventory-report-pdf', 'Admin\InventoryController@reportPdf')->name('admin.inventory.report.pdf');
Route::get('/inventory-import', 'Admin\InventoryController@importExcel')->name('admin.inventory.import');
Route::post('/inventory-import-upload', 'Admin\InventoryController@importExcelUpload')->name('admin.inventory.import.upload');

/**
 * OUTBAND SCHEDULES ROUTES
 */

Route::get('/schedule-outbound', 'Admin\OutboundScheduleController@index')->name('admin.schedule.outbound');
Route::get('/schedule-outbound-create', 'Admin\OutboundScheduleController@create')->name('admin.schedule.outbound.create');
Route::get('/schedule-outbound-details/{id}', 'Admin\OutboundScheduleController@details')->name('admin.schedule.outbound.details');
Route::get('/schedule-outbound-report-excel', 'Admin\OutboundScheduleController@reportExcel')->name('admin.schedule.outbound.report.excel');
Route::get('/schedule-outbound-report-pdf', 'Admin\OutboundScheduleController@reportPdf')->name('admin.schedule.outbound.report.pdf');

/**
 * ORDER ROUTES
 */
Route::get('/order', 'Admin\OrderController@index')->name('admin.order');
Route::get('/order-create', 'Admin\OrderController@create')->name('admin.order.create');
Route::get('/order-details/{id}', 'Admin\OrderController@details')->name('admin.order.details');
Route::get('/order-report-excel', 'Admin\OrderController@reportExcel')->name('admin.order.report.excel');
Route::get('/order-report-pdf', 'Admin\OrderController@reportPdf')->name('admin.order.report.pdf');
Route::get('/order-import', 'Admin\OrderController@importExcel')->name('admin.order.import');
Route::get('/order-import-google', 'Admin\OrderController@importGoogleExcel')->name('admin.order.import.google');
Route::get('/order-import-google-submit', 'Admin\OrderController@importGoogleExcelSubmit')->name('admin.order.import.google.submit');
Route::post('/order-import-upload', 'Admin\OrderController@importExcelUpload')->name('admin.order.import.upload');
Route::get('/shipping-calculator', 'Admin\OrderController@shippingCalculator')->name('admin.order.calculator');
Route::get('/order-inscan', 'Admin\OrderScanController@inscan')->name('admin.order.inscan');
Route::get('/order-outscan', 'Admin\OrderScanController@outscan')->name('admin.order.outscan');
Route::get('/order-assign-rider/{id}', 'Admin\OrderController@assignRider')->name('admin.order.assign.rider');
Route::get('/order-waybill/{id}', 'Admin\OrderController@getOrderWaybill')->name('admin.order.waybill');
Route::get('/order-schedule', 'Admin\OrderController@orderSchedule')->name('admin.order.scheduled');
Route::get('/order-intransit', 'Admin\OrderController@orderInTransit')->name('admin.order.transit');
Route::get('/order-awaiting-dispatch', 'Admin\OrderController@orderAwaitingDispatch')->name('admin.order.awaiting.dispatch');
Route::get('/order-duplicate', 'Admin\OrderController@orderDuplicate')->name('admin.order.duplicate');
Route::get('/order-followup', 'Admin\OrderController@orderFollowup')->name('admin.order.followup');
Route::get('/order-pending', 'Admin\OrderController@orderPending')->name('admin.order.pending');
Route::get('/order-dispatch-policy', 'Admin\OrderController@orderDispatchPolicy')->name('admin.order.dispatch.policy');
Route::get('/order-undispatch', 'Admin\OrderController@orderUndispatch')->name('admin.order.undispatch');
Route::get('/order-dispatched', 'Admin\OrderController@orderDispatch')->name('admin.order.dispatched');
Route::get('/order-undispatched', 'Admin\OrderController@orderUndispatched')->name('admin.order.undispatched');

/**
 * REPORTS ROUTES
 */

Route::get('/report-orders', 'Admin\ReportController@orderReport')->name('admin.report.order');
Route::get('/report-merchant', 'Admin\ReportController@merchantReport')->name('admin.report.merchant');

Route::get('/report-rider', 'Admin\ReportController@riderReport')->name('admin.report.rider');
Route::get('/report-sales', 'Admin\ReportController@salesReport')->name('admin.report.sales');
Route::get('/report-delivery', 'Admin\ReportController@deliveryReport')->name('admin.report.delivery');
Route::get('/report-inventory', 'Admin\ReportController@inventoryReport')->name('admin.report.inventory');
Route::get('/report-sku', 'Admin\ReportController@skuReport')->name('admin.report.sku');
Route::get('/report-outscan', 'Admin\ReportController@riderOutscan')->name('admin.report.outscan');
Route::get('/report-intransit', 'Admin\ReportController@riderIntransit')->name('admin.report.intransit');
Route::get('/report-merchant-remittance', 'Admin\ReportController@merchantRemittanceReport')->name('admin.report.merchant.remittance');
Route::get('/report-merchant-remittance-excel/{id}', 'Admin\ReportController@reportMerchandiseRemittance')->name('admin.report.merchant.remittance.generate');

/**
 * NOTIFICATION ROUTES
 */
Route::get('/notifications', 'Admin\NotificationController@index')->name('admin.notification');

/**
 * DOWNLOAD ROUTES
 */
Route::get('/download-rider-template', 'DownloadController@getImportTemplateRider')->name('admin.download.rider.template');
Route::get('/download-merchant-template', 'DownloadController@getImportTemplateMerchant')->name('admin.download.merchant.template');
Route::get('/download-order-template', 'DownloadController@getImportTemplateOrder')->name('admin.download.order.template');
Route::get('/download-inventory-template', 'DownloadController@getImportTemplateInventory')->name('admin.download.inventory.template');

/**
 * CALL CENTRE ROUTES
 */
Route::get('/call-centre-report', 'Admin\CallCentreController@index')->name('admin.call.centre');

/**
 * CALL AGENTS ROUTES
 */

Route::get('/call-agent', 'Admin\CallAgentController@index')->name('admin.call-agent');
Route::get('/call-agent-create', 'Admin\CallAgentController@create')->name('admin.call-agent.create');
Route::get('/call-agent-details/{id}', 'Admin\CallAgentController@details')->name('admin.call-agent.details');
Route::get('/call', 'Tests\CallTestController@testCall');

Route::prefix('merchant')->group(function () {

    Route::get('/', 'Merchant\HomeController@index')->name('merchant.dashboard');
    Route::get('/login', 'Auth\MerchantLoginController@showLoginForm')->name('merchant.login');
    Route::post('/login', 'Auth\MerchantLoginController@login')->name('merchant.login.submit');
    Route::get('/logout', 'Auth\MerchantLoginController@logout')->name('merchant-logout');
    Route::get('/forgot-password-form', 'Auth\MerchantLoginController@forgotPaswordForm')->name('merchant-forgot-password-form');
    Route::post('/reset-password', 'Auth\MerchantLoginController@resetPassword')->name('merchant-reset-password');
    Route::get('/profile', 'Merchant\ProfileController@index')->name('merchant.profile');

    /**
     * ORDER ROUTES
     */
    Route::get('/order', 'Merchant\OrderController@index')->name('merchant.order');
    Route::get('/order-create', 'Merchant\OrderController@create')->name('merchant.order.create');
    Route::get('/order-details/{id}', 'Merchant\OrderController@details')->name('merchant.order.details');
    Route::get('/order-report-excel', 'Merchant\OrderController@reportExcel')->name('merchant.order.report.excel');
    Route::get('/order-report-pdf', 'Merchant\OrderController@reportPdf')->name('merchant.order.report.pdf');
    Route::get('/shipping-calculator', 'Merchant\OrderController@shippingCalculator')->name('merchant.order.calculator');
    Route::get('/order-report-merchant', 'Merchant\ReportController@merchantReport')->name('merchant.order.report');
    /**
     * INVENTORIES ROUTES
     */
    Route::get('/inventory', 'Merchant\InventoryController@index')->name('merchant.inventory');
    Route::get('/inventory-report-excel', 'Merchant\InventoryController@reportExcel')->name('merchant.inventory.report.excel');
    Route::get('/inventory-report-pdf', 'Merchant\InventoryController@reportPdf')->name('merchant.inventory.report.pdf');

    Route::post('/call', 'Tests\CallTestController@testCall');

    /**
     * DOWNLOAD ROUTES
     */
    Route::get('/download-order-template', 'DownloadController@getImportTemplateOrder')->name('merchant.download.order.template');

});
