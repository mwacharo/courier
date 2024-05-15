<template>
    <div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="tab-content">
                <!--begin::Tab-->
                <div class="tab-pane show active px-7" id="kt_user_edit_tab_1" role="tabpanel">

                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-8 my-2">

                            <!--begin::Row-->
                            <div class="row">
                                <label class="col-3"></label>
                                <div class="col-9">
                                    <h6 class="text-dark font-weight-bold mb-10">Order Info:</h6>
                                </div>
                            </div>
                            <!--end::Row-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Order No <br><small class="text-danger">(Blank will autogenerate)</small></label>
                                <div class="col-lg-8 col-xl-8">
                                    <input class="form-control form-control-solid form-control-lg" name="order_no"
                                           type="text" v-model="order_no" disabled/>
                                    <barcode v-bind:value="order_no" :options="{ lineColor: '#0275d8', text: 'Scan'}" v-if="order_no"></barcode>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Destination Type</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="destination_type"
                                            type="text" v-model="destination_type" disabled>
                                        <option value="1">Outbound Delivery (Out of Nairobi)</option>
                                        <option value="2">Inbound Delivery</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Service Type</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="service_type"
                                            type="text" v-model="service_type" disabled>
                                        <option value="">Select Service</option>
                                        <option value="1" v-if="destination_type==2">Same-Day Delivery</option>
                                        <option value="2">Scheduled Delivery</option>
                                        <option value="3">Overnight Delivery</option>
                                        <option value="4">Pickup Delivery</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="destination_type==2">
                                <label class="col-xl-4 col-lg-4 col-form-label">Inbound Rate Type</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="inbound_rate_type"
                                            type="text" v-model="inbound_rate_type" disabled>
                                        <option value="">Select Inbound Rate</option>
                                        <option value="1">On-demand delivery charges</option>
                                        <option value="2">Zone delivery charges</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="inbound_rate_type==1">
                                <label class="col-xl-4 col-lg-4 col-form-label">Approximate Distance (KM)</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="delivery_distance"
                                           type="number" min="0" onwheel="this.blur()" v-model="delivery_distance" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="service_type==2">
                                <label class="col-xl-4 col-lg-4 col-form-label">Delivery Date</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="delivery_date"
                                           type="date" v-model="delivery_date" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->


                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Enable Cash On Delivery</label>
                                <div class="col-4">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="cash_on_delivery" disabled/>
                                    <span></span>
                                </label>
                            </span>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="cash_on_delivery">
                                <label class="col-xl-4 col-lg-4 col-form-label">Cash On Delivery Amount</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg"
                                           name="cash_on_delivery_amount" type="number" min="0" onwheel="this.blur()" v-model="cash_on_delivery_amount" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->


                            <!--begin::Group-->
                            <div class="form-group row" v-if="service_type==4">
                                <label class="col-xl-4 col-lg-4 col-form-label">Pickup Country</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="pickup_country" disabled>
                                        <option value="">Select Pickup Country</option>
                                        <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="service_type==4">
                                <label class="col-xl-4 col-lg-4 col-form-label">Pickup Town</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="pickup_town" disabled>
                                        <option value="">Select Pickup Town</option>
                                        <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="service_type==4">
                                <label class="col-xl-4 col-lg-4 col-form-label">Pickup Address</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="pickup_address" type="text" v-model="pickup_address" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <h5 class="text-dark font-weight-bold mb-10">Receiver Details:</h5>

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Name</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_name" type="text" v-model="receiver_name" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Address</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_address" type="text" v-model="receiver_address" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Email</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_email" type="text" v-model="receiver_email" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Phone</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_phone" type="number" min="0" onwheel="this.blur()" v-model="receiver_phone" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Phone (Alternative)</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg"
                                           name="receiver_phone_alternative" type="number" min="0" onwheel="this.blur()" v-model="receiver_phone_alternative" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="destination_type==1">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Country</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="receiver_country" disabled>
                                        <option value="">Select Country</option>
                                        <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="destination_type==1">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Town</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="receiver_town" disabled>
                                        <option value="">Select Town</option>
                                        <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="destination_type==2 && inbound_rate_type==2">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Zone</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="zone_id" type="text" v-model="zone_id" disabled>
                                        <option value="">Select Zone</option>
                                        <option v-for="zone in zones" :value="zone.id">{{ zone.zone }}</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Latitude</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_latitude"
                                           type="number" min="0" onwheel="this.blur()" id="location_latitude" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Longitude</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_longitude"
                                           type="number" min="0" onwheel="this.blur()" id="location_longitude" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-8 my-2">

                            <div class="border-top pt-10 mt-15">

                                <h5 class="text-dark font-weight-bold mb-10">Order Items <small><a href="#" data-toggle="modal" data-target="#modalAddItem" class="text-danger">(Add Item)</a></small></h5>

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <div class="col-lg-12 col-xl-12">
                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Total Weight</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(item, index) in items">
                                                <td>{{ item.description }}</td>
                                                <td>Kshs {{ item.price }}</td>
                                                <td>{{ item.quantity }}</td>
                                                <td>{{ item.weight }}kg</td>
                                            </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                <!--end::Group-->


                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-8 my-2">

                            <div class="border-top pt-10 mt-15">

                                <h5 class="text-dark font-weight-bold mb-10">Shipment Details</h5>

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Special Instructions</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <textarea class="form-control form-control-solid form-control-lg" name="special_instruction" type="text" v-model="special_instruction" rows="4" disabled></textarea>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Rider/Driver</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="rider_id" disabled>
                                            <option value="">Select Rider</option>
                                            <option v-for="rider in riders" :value="rider.id">{{ rider.first_name }} {{ rider.last_name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Payment Type</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="payment_type" disabled>
                                            <option value="">Select Payment Type</option>
                                            <option value="1">Cash</option>
                                            <option value="2">Invoice</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Order Status</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="order_status" type="text" v-model="order_status" disabled>
                                            <option value="order_pending" v-if="order_status==''">Pending Confirmation</option>
                                            <option value="dispatched" v-if="order_status!='cancelled'">Dispatched</option>
                                            <option value="delivery_pending" v-if="order_status!='cancelled'">Delivery Pending</option>
                                            <option value="delivered" v-if="order_status!='cancelled' || order_status=='dispatched' ">Delivered</option>
                                            <option value="returned" v-if="order_status!='cancelled' || order_status=='dispatched' ">Returned</option>
                                            <option value="cancelled" v-if="order_status!='delivered' || order_status!='cancelled'">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row" v-if="order_status=='order_pending' || order_status=='delivery_pending' || order_status=='returned' || order_status=='cancelled'">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Status Reason</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="status_reason" type="text" v-model="status_reason">
                                            <option value="">Select Reason</option>
                                            <option value="wrong_order" v-if="order_status=='dispatch_held' || order_status=='cancelled' || order_status=='returned'">Wrong Order</option>
                                            <option value="not_available">Not available</option>
                                            <option value="call_back">Call back</option>
                                            <option value="not_picking">Not Picking</option>
                                            <option value="client_offline">Client Offline</option>
                                            <option value="out_of_destination" v-if="order_status=='delivery_pending' || order_status=='cancelled'">Out of destination</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Payment Status</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="payment_status" type="text" v-model="payment_status" disabled>
                                            <option value="0">Pending</option>
                                            <option value="1">Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-8 my-2">

                            <div class="border-top pt-10 mt-15">

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <div class="col-lg-12 col-xl-12">
                                        <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
                                            <div class="col-md-11">
                                                <div class="d-flex justify-content-between flex-column flex-md-row font-size-lg">
                                                    <div class="d-flex flex-column mb-10 mb-md-0">
                                                        <div class="font-weight-bolder font-size-lg mb-3">ORDER SUMMARY</div>
                                                        <div class="d-flex justify-content-between mb-3">
                                                            <span class="mr-15 font-weight-bold">Total Items:</span>
                                                            <span class="text-right">{{ items.length }} </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-3">
                                                            <span class="mr-15 font-weight-bold">Total Weight:</span>
                                                            <span class="text-right">{{ total_weight }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column text-md-right">
                                                        <span class="font-size-lg font-weight-bolder mb-1">TOTAL AMOUNT</span>
                                                        <span class="font-size-h2 font-weight-boldest text-danger mb-1">Kshs {{ total_amount }}</span>
                                                        <span>Taxes Included</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Group-->

                            </div>

                        </div>
                    </div>

                    <!--end::Row-->
                </div>
                <!--end::Tab-->

            </div>
        </form>

    </div>

</template>

<script>
    import VueBarcode from "vue-barcode";

    export default {

        mounted(){
            this.fetchCountries();
            this.fetchTowns();
            this.fetchZones();
            this.fetchBranches();
            this.fetchMerchants();
            this.fetchRiders();
            this.fetchDetails();
            this.fetchOrderItems();
        },

        components: {
            'barcode': VueBarcode
        },


        props: {
            order_id: String,
        },

        data(){
            return{
                order_no: '',
                destination_type: '1',
                delivery_distance: '',
                service_type: '',
                inbound_rate_type: '',
                cash_on_delivery: false,
                cash_on_delivery_amount: '',
                is_sender_merchant: false,
                merchant: '',
                merchant_id: '',
                sender_name: '',
                sender_address: '',
                sender_email: '',
                sender_phone: '',
                sender_phone_alternative: '',
                sender_country: '',
                sender_town: '',
                pickup_country: '',
                pickup_town: '',
                pickup_address: '',
                receiver_name: '',
                receiver_address: '',
                receiver_email: '',
                receiver_phone: '',
                receiver_phone_alternative: '',
                receiver_country: '',
                receiver_town: '',
                receiver_latitude: '',
                receiver_longitude: '',
                special_instruction: '',
                payment_type: '',
                amount: '',
                insurance: false,
                order_status: 'order_pending',
                payment_status: '0',
                status_reason: '',
                rider_id: '',
                branch_id: '',
                zone_id: '',
                booking_date: '',
                booking_time: '',
                delivery_date: '',
                admin_id: '',

                item_inventory: false,
                item_inventory_product: null,
                item_inventory_id: '',
                item_inventory_quantity: 0,
                item_inventory_amount: 0,
                item_id: '',
                item_sku: '',
                item_index: '',
                item_description: '',
                item_price: '',
                item_quantity: '',
                item_weight: '',
                items: [],
                inventory_products: [],
                total_weight: 0,
                total_amount: 0,

                merchants: [],
                riders: [],
                countries: [],
                towns: [],
                branches: [],
                zones: [],

                alert_error: false,
                alert_success: false,
                loader: false,
            }
        },

        methods: {

            fetchMerchants() {
                let uri = base_url + `v1/merchant-list`;
                axios.get(uri).then((response) => {
                    this.merchants = response.data;
                });
            },

            fetchRiders() {
                let uri = base_url + `v1/rider-list`;
                axios.get(uri).then((response) => {
                    this.riders = response.data;
                });
            },

            fetchCountries() {
                let uri = base_url + `v1/country-list`;
                axios.get(uri).then((response) => {
                    this.countries = response.data;
                });
            },

            fetchTowns() {
                let uri = base_url + `v1/town-list`;
                axios.get(uri).then((response) => {
                    this.towns = response.data;
                });
            },

            fetchZones() {
                let uri = base_url + `v1/zone-list`;
                axios.get(uri).then((response) => {
                    this.zones = response.data;
                });
            },

            fetchBranches() {
                let uri = base_url + `v1/branch-list`;
                axios.get(uri).then((response) => {
                    this.branches = response.data;
                });
            },

            selectedMerchant(merchant){
                this.merchant_id = merchant.id;
                this.sender_country = merchant.country_id;
                this.sender_town = merchant.town_id;
                this.sender_name = merchant.name;
                this.sender_phone = merchant.phone_number;
                this.sender_address = merchant.address;
                this.sender_email = merchant.email;
            },


            fetchDetails(){

                const vm = this;
                let uri = base_url+`v1/order-details/`+this.order_id;
                axios.get(uri).then((response) => {

                    let order = response.data;
                    if(order){

                        this.order_no = order.order_no;
                        this.destination_type = order.destination_type;
                        this.delivery_distance = order.delivery_distance;
                        this.service_type = order.service_type;
                        this.inbound_rate_type = order.inbound_rate_type;
                        this.cash_on_delivery = order.cash_on_delivery;
                        this.cash_on_delivery_amount = order.cash_on_delivery_amount;
                        this.is_sender_merchant = order.is_sender_merchant;
                        this.merchant_id = order.merchant_id;
                        this.sender_name = order.sender_name;
                        this.sender_address = order.sender_address;
                        this.sender_email = order.sender_email;
                        this.sender_phone = order.sender_phone;
                        this.sender_phone_alternative = order.sender_phone_alternative;
                        this.sender_country = order.sender_country;
                        this.sender_town = order.sender_town;
                        this.receiver_name = order.receiver_name;
                        this.receiver_address = order.receiver_address;
                        this.receiver_email = order.receiver_email;
                        this.receiver_phone = order.receiver_phone;
                        this.receiver_phone_alternative = order.receiver_phone_alternative;
                        this.receiver_country = order.receiver_country;
                        this.receiver_town = order.receiver_town;
                        this.receiver_latitude = order.receiver_latitude;
                        this.receiver_longitude = order.receiver_longitude;
                        this.delivery_date = order.delivery_date;
                        this.special_instruction = order.special_instruction;
                        this.payment_type = order.payment_type;
                        this.insurance = order.insurance;
                        this.order_status = order.order_status;
                        this.status_reason = order.status_reason;
                        this.payment_status = order.payment_status;
                        this.rider_id = order.rider_id;
                        this.zone_id = order.zone_id;
                        this.admin_id = order.admin_id;
                        this.total_weight = order.total_weight;
                        this.total_amount = order.amount;

                        for (var i = 0; i < vm.merchants.length; i++) {
                            if (vm.merchants[i]['id'] === vm.merchant_id) {
                                vm.merchant = vm.merchants[i]
                            }
                        }

                    }
                });
            },

            fetchOrderItems() {
                let uri = base_url+`v1/order-items/`+this.order_id;
                axios.get(uri).then((response) => {
                    this.items = response.data;
                });
            },

        }
    }
</script>
