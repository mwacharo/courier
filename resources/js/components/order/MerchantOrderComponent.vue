<template>

    <div>
        <div class="d-flex justify-content-center">
            <div class="col-8 alert alert-warning" role="alert" v-if="data_error">No Record Found!!</div>
        </div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-xl-11">

                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Order No:</label>
                                    <input class="form-control form-control-solid form-control-lg" name="sku" type="text" v-model="search_order_no"/>
                                </div>

                                <div class="col-lg-4">
                                    <label>Recipient Name:</label>
                                    <input class="form-control form-control-solid form-control-lg" name="search_recipient_name" type="text" v-model="search_recipient_name"/>
                                </div>

                                <div class="col-lg-4">
                                    <label>Recipient Phone:</label>
                                    <input class="form-control form-control-solid form-control-lg" name="search_recipient_phone" type="text" v-model="search_recipient_phone"/>
                                </div>

                            </div>
                            <div class="form-group row">

                                <div class="col-lg-4">
                                    <label>Date:</label>
                                    <select class="form-control" v-model="order_date" @change.prevent="selectedDate">
                                        <option value="all">All</option>
                                        <option value="today">Today</option>
                                        <option value="current_week">Current Week</option>
                                        <option value="last_week">Last Week</option>
                                        <option value="current_month">Current Month</option>
                                        <option value="current_year">Current Year</option>
                                        <option value="custom_date">
                                            <template v-if="custom_date">
                                                {{ custom_date }}
                                            </template>
                                            <template v-else>
                                                Custom Date
                                            </template>
                                        </option>
                                        <option value="custom_range">
                                            <template v-if="custom_start_date && custom_end_date">
                                                ({{ custom_start_date }}) to ({{ custom_end_date }})
                                            </template>
                                            <template v-else>
                                                Custom Range
                                            </template>

                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label>Order Status:</label>
                                    <select class="form-control" v-model="search_order_status">
                                        <option value="all">All</option>
                                        <option value="order_pending">Pending Confirmation</option>
                                        <option value="scheduled">Scheduled</option>
                                        <option value="awaiting_dispatch">Awaiting Dispatch</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="dispatched">Dispatched</option>
                                        <option value="undispatched">Undispatched</option>
                                        <option value="in_transit">In Transit</option>
                                        <option value="not_dispatched">Not Dispatched</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="returned">Returned</option>
                                        <option value="expired">Expired</option>
                                        <option value="out_of_stock">Out of stock</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label>Town:</label>
                                    <select class="form-control" v-model="search_town">
                                        <option value="all">All</option>
                                        <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                                    </select>
                                </div>
                            </div>


                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-8">
                                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                    <div class="spinner-border text-primary" role="status" v-if="loader">
                                      <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </form>

        <table class="table table-bordered table-responsive" id="order_table" style="margin-top: 13px !important">
            <thead>
            <tr>
                <th>#</th>
                <th>Order No</th>
                <th>Receiver Name</th>
                <th>Receiver Phone</th>
                <th>Receiver Address</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Order Status</th>
                <th>Special Instructions</th>
                <th>Status Date</th>
                <th>Added Date</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(order, index) in orders">
                <td>{{ index + 1 }}</td>
                <td>{{ order.order_no }}</td>
                <td>{{ order.receiver_name }}</td>
                <td>{{ order.receiver_phone }} <br> {{ order.receiver_phone_alternative }}</td>
                <td>{{ order.receiver_address }}</td>
                <td>
                        <span v-for="item in order.items">{{ item.description }}</span> <br>
                </td>
                <td>
                        <span v-for="item in order.items">{{ item.quantity }}</span> <br>
                </td>
                <td>
                        {{ order.amount }}
                </td>
                <td>
                    <a href="#" v-if="order.order_status == 'order_pending'">ORDER PENDING</a>
                    <a href="#" v-else-if="order.order_status == 'scheduled'">SCHEDULED</a>
                    <a href="#" v-else-if="order.order_status == 'awaiting_dispatch'">AWAITING DISPATCH</a>
                    <a href="#" v-else-if="order.order_status == 'dispatched'">DISPATCHED</a>
                    <a href="#" v-else-if="order.order_status == 'undispatched'">UNDISPATCHED</a>
                    <a href="#" v-else-if="order.order_status == 'in_transit'">IN TRANSIT</a>
                    <a href="#" v-else-if="order.order_status == 'delivered'">DELIVERED</a>
                    <a href="#" v-else-if="order.order_status == 'returned'">RETURNED</a>
                    <a href="#" v-else-if="order.order_status == 'cancelled'">CANCELLED</a>
                    <a href="#" v-else-if="order.order_status == 'not_dispatched'">NOT DISPATCHED</a>
                    <a href="#" v-else-if="order.order_status == 'expired'">EXPIRED</a>
                    <a href="#" v-else-if="order.order_status == 'out_of_stock'">OUT OF STOCK</a>

                </td>
                <td>
                    {{ order.special_instruction }}
                </td>
                <td>
                    {{ order.status_date }}
                </td>
                <td>
                    {{ order.created_at }}
                </td>
            </tr>
            </tbody>

        </table>

        <div class="modal fade" id="modalCustomDate" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="customDateSubmit" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Custom Date</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="dateClose">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body" style="height: 300px;">


                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Custom Date</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="custom_date" type="date" v-model="custom_date"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="dateClose">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Select Date</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="modalCustomRange" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="customRangeSubmit" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Custom Range</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="dateClose">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body" style="height: 300px;">


                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Start Date</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="custom_start_date" type="date" v-model="custom_start_date"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">End Date</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="custom_end_date" type="date" v-model="custom_end_date"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="dateClose">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Select Date</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>


</template>

<script>

    const CancelToken = axios.CancelToken;
    const order_source = CancelToken.source();
    const filter_source = CancelToken.source();

    export default {

        mounted(){
            this.fetchOrders ();
            this.fetchCountries();
            this.fetchTowns();
            this.fetchZones();
        },

        props: {
            merchant_id: String,
        },

        data(){
            return{
                orders: [],
                agents: [],

                order_id: '',
                order_status: 'order_pending',
                status_reason: '',
                custom_reason: '',
                scheduled_date: '',
                status_date: '',
                is_sender_merchant: '',
                special_instruction: '',
                zone_id: '',
                pickup_country: '',
                pickup_town: '',
                pickup_address: '',
                sender_country: '',
                sender_town: '',
                receiver_country: '',
                receiver_town: '',
                receiver_address: '',
                destination_type: '1',
                delivery_distance: '',
                service_type: '',
                inbound_rate_type: '',
                agent: '',
                items: [],
                total_weight: 0,
                total_amount: 0,

                order_date: 'all',
                custom_date: '',
                custom_start_date: '',
                custom_end_date: '',
                client_type: 'all',
                search_service_type: 'all',
                search_order_status: 'all',
                search_payment_status: 'all',
                search_destination_type: 'all',
                search_town: 'all',
                search_order_no: '',
                search_recipient_name: '',
                search_recipient_phone: '',
                search_agent_id: 'all',

                base_url_web_merchant: '',

                countries: [],
                towns: [],
                zones: [],
                merchants: [],

                alert_error:false,
                alert_success:false,
                data_error:false,
                loader:false,

            }
        },

        methods: {

            fetchOrders (){
                let uri = base_url+`v1/merchant-order-list/` + this.merchant_id;
                axios.get(uri, {
                cancelToken: order_source.token
                }).then((response) => {
                        this.orders = response.data;
                        this.myTable();
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

            myTable(){
                $(document).ready( function () {
                    $('#order_table').DataTable();
                });
            },

            fetchOrderItems() {
                let uri = base_url+`v1/order-items/`+this.order_id;
                axios.get(uri).then((response) => {
                    this.items = response.data;
                });
            },


            selectedDate(){
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";

                if(this.order_date == 'custom_date'){
                    $('#modalCustomDate').modal('show');
                }else if(this.order_date == 'custom_range'){
                    $('#modalCustomRange').modal('show');
                }

            },

            customDateSubmit(){

                if(this.custom_date == ''){
                    alert('Select custom date');
                    return;
                }

                $('#modalCustomDate').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();
            },

            customRangeSubmit(){

                if(this.custom_start_date == ''){
                    alert('Select start date');
                    return;
                }

                if(this.custom_end_date == ''){
                    alert('Select end date');
                    return;
                }

                $('#modalCustomRange').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();
            },

            dateClose(){
                this.order_date = "all";
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";
            },


            formSubmit(){

                order_source.cancel('Operation canceled by the user.');
                this.orders = [];
                this.loader = true;
                this.data_error = false;
                var order_date = this.order_date;
                var custom_date = this.custom_date;
                var custom_start_date = this.custom_start_date;
                var custom_end_date = this.custom_end_date;
                var merchant_id = this.merchant_id;
                var destination_type = this.search_destination_type;
                var recipient_name = this.search_recipient_name;
                var recipient_phone = this.search_recipient_phone;
                var order_status = this.search_order_status;
                var payment_status = this.search_payment_status;
                var order_no = this.search_order_no;
                var town_id = this.search_town;
                var agent_id = this.search_agent_id;

                if(order_date == 'custom_date'){

                    if(this.custom_date == ''){
                        alert("Custom date not selected");
                        return;
                    }

                }

                if(order_date == 'custom_range'){

                    if(this.custom_start_date == ''){
                        alert("Custom start date not selected");
                        return;
                    }

                    if(this.custom_end_date == ''){
                        alert("Custom end date not selected");
                        return;
                    }
                }


                const vm = this;
                const formData = new FormData();
                formData.append( 'order_date', order_date);
                formData.append( 'custom_date', custom_date);
                formData.append( 'custom_start_date', custom_start_date);
                formData.append( 'custom_end_date', custom_end_date);
                formData.append( 'merchant_id', merchant_id);
                formData.append( 'recipient_name', recipient_name);
                formData.append( 'recipient_phone', recipient_phone);
                formData.append( 'order_status', order_status);
                formData.append( 'payment_status', payment_status);
                formData.append( 'order_no', order_no);
                formData.append( 'town_id', town_id);
                formData.append( 'destination_type', destination_type);
                formData.append( 'agent_id', agent_id);

                let uri = base_url+`v1/merchant-order-search-page`;
                axios.post(uri, formData)
                    .then(function(response) {
                        vm.orders = response.data;
                        vm.loader = false;
                        vm.data_error = vm.orders.length === 0; // Toggle data_error based on orders array length
                        setTimeout(function () {
                            vm.data_error = false;
                        }, 2000);
                    })
                    .catch(function(error) {
                        console.log(error);
                        vm.loader = false;
                    });

            },

        }
    }
</script>
