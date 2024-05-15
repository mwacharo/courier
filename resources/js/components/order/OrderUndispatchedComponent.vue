<template>
    <div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-xl-11">

                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                        <div class="card-body">
                            <div class="form-group row">

                                <div class="col-lg-6">
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

                                <div class="col-lg-6">
                                    <label>Merchant Details:</label>
                                    <select class="form-control" v-model="merchant_id">
                                        <option value="all">All</option>
                                        <option v-for="merchant in merchants" :value="merchant.id">{{ merchant.name }}</option>
                                    </select>
                                </div>

                                <div class="col-lg-8 mt-3">
                                    <label>Order No:</label>
                                    <input class="form-control form-control-solid form-control-lg" name="sku" type="text" v-model="order_no"/>
                                </div>
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

                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th title="Field #0">#</th>
                            <th title="Field #1">Order No</th>
                            <th title="Field #2">Sender</th>
                            <th title="Field #3">Receiver</th>
                            <th title="Field #4">Address</th>
                            <th title="Field #4">Receiver phone</th>
                            <th title="Field #5">Order Status <br><span class="text-danger text-center">(update)</span></th>
                            <th title="Field #6">Custom Reason</th>
                            <th title="Field #7">Undispatch Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(order, index) in orders">
                            <td>{{ index + 1 }}</td>
                            <td>{{ order.order_no }}</td>
                            <td>{{ order.sender_name }}</td>
                            <td>{{ order.receiver_name }}</td>
                            <td>{{ order.receiver_address }}</td>
                            <td>{{ order.receiver_phone }} <br><a href="#" class="text-warning" @click.prevent="makeCall(order.receiver_phone)"><i class="fa fa-phone text-primary" aria-hidden="true"></i> (Call)
                                                                </a>
                            </td>
                            <td class="text-warning">
                                <a href="#" @click.prevent="selectOrderStatus(order)" class="text-info" data-toggle="modal" data-target="#statusModal">{{ order.order_status }}</a></td>

                            <td v-if="order.custom_reason == 'not_ready_financially'">Not financially ready</td>
                            <td v-else-if="order.custom_reason == 'not_picking_calls'">Not picking Calls</td>
                            <td v-else-if="order.custom_reason == 'order_cancelled'">Order was Cancelled</td>
                            <td v-else-if="order.custom_reason == 'offline'">Offline</td>
                            <td v-else-if="order.custom_reason == 'office_pickup'">Office pickup</td>
                            <td v-else-if="order.custom_reason == 'outbound_order'">Outbound Order</td>
                            <td v-else-if="order.custom_reason == 'assign_another_rider'"><span class="text-info">Assign another rider</span></td>
                            <td v-else-if="order.custom_reason == 'user_busy'">User Busy</td>
                            <td v-else-if="order.custom_reason == 'duplicate_order'">Duplicate Order</td>
                            <td v-else-if="order.custom_reason == 'duplicate_of_delivered'">Duplicate Of already delivered</td>
                            <td v-else-if="order.custom_reason == 'unresponsive'">Unresponsive</td>
                            <td v-else-if="order.custom_reason == 'not_around'">Client not around</td>
                            <td v-else-if="order.custom_reason == 'deliver_another_day'">Deliver another day</td>
                            <td v-else-if="order.custom_reason == 'no_longer_interested'">No longer interested</td>
                            <td v-else-if="order.custom_reason == 'will_call_later_date'">Will call at later date</td>
                            <td v-else-if="order.custom_reason == 'not_around'">Client not around</td>
                            <td v-else> <span class="text-primary">{{ order.custom_reason }}</span> </td>
                            <td>{{ order.updated_at }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>


            </div>
        </form>
        <div class="modal fade" id="statusModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="formSubmitChangeStatus" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="statusModalLabel">Update Status</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="closeStatusModal">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="alert alert-success" role="alert" v-if="alert_success">Order status updated!</div>
                                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating status!</div>

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <div class="form-group">
                                            <h5>Choose Status</h5>
                                            <div>
                                                <select class="form-control" name="order_status" type="text" v-model="order_status">
                                                    <option value="scheduled">Scheduled</option>
                                                    <option value="cancelled">Cancelled</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="closeStatusModal">Close</button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Update Status</button>
                            <div class="spinner-border spinner-border" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

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
    export default {

        mounted(){
            this.fetchMerchants();
        },

        props: {
            causer_id: String,
        },

        data(){
            return{
                order_no: '',
                merchant_id: 'all',
                order_date:'all',
                custom_date: '',
                custom_start_date: '',
                custom_end_date: '',
                merchants: [],
                base_url_web_admin: '',
                orders: [],
                loader: false,
                is_sender_merchant: '',
                status_reason: '',
                order_id: '',
                order_status: '',
                alert_error:false,
                alert_success:false,
            }
        },

        methods: {

            fetchMerchants() {
                let uri = base_url + `v1/merchant-list`;
                axios.get(uri).then((response) => {
                    this.merchants = response.data;
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

            selectOrderStatus(order){

                this.alert_error = false;
                this.alert_success = false;
                this.loader = false;

                this.order_id = order.id;
                this.order_status = order.order_status;
                this.status_reason = order.status_reason;
                this.custom_reason = order.custom_reason;
                this.scheduled_date = order.scheduled_date;
                this.is_sender_merchant = order.is_sender_merchant;
                },

                formSubmitChangeStatus(){

                        this.loader = true;

                        var order_status = this.order_status;
                        var status_reason = this.status_reason;
                        var custom_reason = this.custom_reason;
                        var scheduled_date = this.scheduled_date;
                        var is_sender_merchant = this.is_sender_merchant;
                        if(order_status == 'scheduled' && is_sender_merchant == 1){

                            if(!scheduled_date){
                                alert('Enter scheduled date');
                                this.loader = false;
                                return
                            }
                        }

                        if(status_reason == 'custom_reason'){

                            if(!custom_reason){
                                alert('Enter custom reason');
                                this.loader = false;
                                return
                            }
                        }

                        const vm = this;
                        const formData = new FormData();
                        formData.append( 'id', this.order_id);
                        formData.append('order_status', order_status);
                        formData.append('status_reason', status_reason);
                        formData.append('custom_reason', custom_reason);
                        formData.append('scheduled_date', scheduled_date);
                        formData.append('causer_id', this.causer_id);

                        let uri = base_url+`v1/order-update-status`;
                        axios.post(uri, formData)
                            .then(function (response) {
                                var status = response.data.success;
                                if(status === 1){
                                    vm.alert_success = true;
                                    vm.loader = false;

                                    if(vm.order_date !== 'all' || vm.client_type !== 'all' || vm.search_service_type !== 'all'
                                        || vm.search_order_status !== 'all' || vm.search_payment_status !== 'all' || vm.search_merchant_id !== 'all'
                                        || vm.search_town !== 'all' || vm.search_agent_id !== 'all' || vm.search_recipient_name !== '' || vm.search_order_no !== ''){

                                        vm.formSubmit();

                                    }else{
                                        vm.fetchOrders();
                                    }

                                    $('#statusModal').modal('hide');
                                    $(document.body).removeClass('modal-open');
                                    $('.modal-backdrop').remove();

                                    vm.closeStatusModal();
                                }else{

                                    vm.loader = false;
                                    vm.alert_error = true;
                                }

                            })
                            .catch(function (error) {
                                console.log(error);
                                vm.loader = false;
                                vm.alert_error = true;
                            });

                        },

            formSubmit(){

                this.orders = [];
                var order_no = this.order_no;
                var order_date = this.order_date;
                var custom_date = this.custom_date;
                var custom_start_date = this.custom_start_date;
                var custom_end_date = this.custom_end_date;
                var merchant_id = this.merchant_id;


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
                formData.append( 'order_no', order_no);
                formData.append( 'custom_date', custom_date);
                formData.append( 'custom_start_date', custom_start_date);
                formData.append( 'custom_end_date', custom_end_date);
                formData.append( 'merchant_id', merchant_id);

                let uri = base_url+`v1/order-undispatched`;
                axios.post(uri, formData)
                    .then(function (response) {
                        vm.orders = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            makeCall(val){
                this.$eventBus.$emit('make-call', val);
            },

            generateExcel(){

                this.loader = true;
                if(this.orders.length == 0){
                    alert('No order items');
                    this.loader = false;
                    return
                }

                const vm = this;
                let uri = base_url + `v1/order-undispatched-generate-excel`;
                axios({
                    url: uri,
                    method: 'POST',
                    responseType: 'blob',
                    data: {
                        orders: JSON.stringify(this.orders),
                        causer_id: this.causer_id,
                    }
                }).then((response) => {
                    vm.loader = false;
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'undispatched-orders-excel.xls');
                    document.body.appendChild(link);
                    link.click();
                });

            },

            generatePDF(){

                this.loader = true;
                if(this.orders.length == 0){
                    alert('No order items');
                    this.loader = false;
                    return
                }

                const vm = this;
                let uri = base_url + `v1/order-scheduled-generate-pdf`;
                axios({
                    url: uri,
                    method: 'POST',
                    responseType: 'blob',
                    data: {
                        orders: JSON.stringify(this.orders),
                        causer_id: this.causer_id,
                    }
                }).then((response) => {
                    vm.loader = false;
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'order-waybill-printout.pdf');
                    document.body.appendChild(link);
                    link.click();
                });

            }
        }
    }
</script>









