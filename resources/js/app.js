require('./bootstrap');

window.base_url = "http://127.0.0.1:8000/api/";
window.base_url_web = "http://127.0.0.1:8000";
window.base_url_web_admin = "http://127.0.0.1:8000/admin/";
window.base_url_web_merchant = "http://127.0.0.1:8000/merchant/";

// window.base_url = "https://boxleocourier.com/dashboard/api/";
// window.base_url_web = "https://boxleocourier.com/dashboard/";
// window.base_url_web_admin = "https://boxleocourier.com/dashboard/admin/";
// window.base_url_web_merchant = "https://boxleocourier.com/dashboard/merchant/";


window.Vue = require('vue');



Vue.component('admin-create-component', require('./components/admin/AdminCreateComponent').default);
Vue.component('admin-activity-component', require('./components/admin/AdminActivityComponent').default);
Vue.component('admin-details-component', require('./components/admin/AdminDetailsComponent').default);
Vue.component('admin-password-component', require('./components/admin/AdminChangePasswordComponent').default);
Vue.component('rider-create-component', require('./components/rider/RiderCreateComponent').default);
Vue.component('rider-details-component', require('./components/rider/RiderDetailsComponent').default);
Vue.component('merchant-create-component', require('./components/merchant/MerchantCreateComponent').default);
Vue.component('merchant-details-component', require('./components/merchant/MerchantDetailsComponent').default);
Vue.component('merchant-password-component', require('./components/merchant/MerchantChangePasswordComponent').default);
Vue.component('inventory-create-component', require('./components/inventory/InventoryCreateComponent').default);
Vue.component('inventory-details-component', require('./components/inventory/InventoryDetailsComponent').default);
Vue.component('inventory-inscan-component', require('./components/inventory/InventoryInscanComponent').default);
Vue.component('inventory-outscan-component', require('./components/inventory/InventoryOutscanComponent').default);
Vue.component('order-component', require('./components/order/OrderComponent').default);
Vue.component('order-followup-component', require('./components/order/OrderFollowupComponent').default);
Vue.component('order-pending-component', require('./components/order/OrderPendingComponent').default);
Vue.component('order-dispatch-policy-component', require('./components/order/OrderDispatchPolicyComponent').default);
Vue.component('order-create-component', require('./components/order/OrderCreateComponent').default);
Vue.component('order-details-component', require('./components/order/OrderDetailsComponent').default);
Vue.component('order-inscan-component', require('./components/order/OrderInscanComponent').default);
Vue.component('order-outscan-component', require('./components/order/OrderOutscanComponent').default);
Vue.component('order-assign-rider-component', require('./components/order/OrderAssignRiderComponent').default);
Vue.component('order-scheduled-component', require('./components/order/OrderScheduledComponent').default);
Vue.component('order-intransit-component', require('./components/order/OrderIntransitComponent').default);
Vue.component('order-awaiting-dispatch-component', require('./components/order/OrderAwaitingDispatchComponent').default);
//dispatched component
Vue.component('order-dispatched-component', require('./components/order/OrderDispatchedComponent').default);
Vue.component('order-undispatched-component', require('./components/order/OrderUndispatchedComponent').default);
Vue.component('order-duplicate-component', require('./components/order/OrderDuplicateComponent').default);
Vue.component('shipping-calculator-component', require('./components/order/ShippingCalculatorComponent').default);
Vue.component('country-create-component', require('./components/country/CountryCreateComponent').default);
Vue.component('country-details-component', require('./components/country/CountryDetailsComponent').default);
Vue.component('town-create-component', require('./components/town/TownCreateComponent').default);
Vue.component('town-details-component', require('./components/town/TownDetailsComponent').default);
Vue.component('zone-create-component', require('./components/schedule/zone/ZoneCreateComponent').default);
Vue.component('zone-details-component', require('./components/schedule/zone/ZoneDetailsComponent').default);
Vue.component('branch-create-component', require('./components/branch/BranchCreateComponent').default);
Vue.component('branch-details-component', require('./components/branch/BranchDetailsComponent').default);
Vue.component('outbound-schedule-create-component', require('./components/schedule/oubound/OutboundCreateComponent').default);
Vue.component('outbound-schedule-details-component', require('./components/schedule/oubound/OutboundDetailsComponent').default);
Vue.component('notification-component', require('./components/notification/NotificationComponent').default);
Vue.component('notification-count-component', require('./components/notification/NotificationCountComponent').default);

Vue.component('report-order-component', require('./components/reports/OrderReportComponent').default);
Vue.component('report-merchant-component', require('./components/reports/MerchantReportComponent').default);
Vue.component('report-rider-component', require('./components/reports/RiderReportComponent').default);
Vue.component('report-rider-outscan-component', require('./components/reports/RiderOutscanReportComponent').default);
Vue.component('report-rider-intransit-component', require('./components/reports/RiderTransitReportComponent').default);
Vue.component('report-delivery-component', require('./components/reports/DeliveryReportComponent').default);
Vue.component('report-inventory-component', require('./components/reports/InventoryReportComponent').default);
Vue.component('report-sku-component', require('./components/reports/SkuReportComponent').default);
Vue.component('merchant-remittance-component', require('./components/reports/MerchantRemittanceComponent').default);

Vue.component('merchant-order-component', require('./components/order/MerchantOrderComponent').default);
Vue.component('merchant-order-create-component', require('./components/order/MerchantOrderCreateComponent').default);
Vue.component('merchant-order-details-component', require('./components/order/MerchantOrderDetailsComponent').default);
Vue.component('merchant-shipping-calculator-component', require('./components/order/MerchantShippingCalculatorComponent').default);
Vue.component('merchant-order-report-component', require('./components/reports/MerchantOrderReportComponent').default);

Vue.component('call-centre-component', require('./components/callcentre/CallCentreComponent').default);
Vue.component('call-menu-component', require('./components/callcentre/CallMenuComponent').default);
Vue.component('call-agent-details-component', require('./components/call-agent/CallAgentDetailsComponent').default);
Vue.component('call-agent-create-component', require('./components/call-agent/CallAgentCreateComponent').default);


import AudioVisual from 'vue-audio-visual'
Vue.use(AudioVisual)


Vue.prototype.$eventBus = new Vue();

const app = new Vue({
    el: '#app',
});
