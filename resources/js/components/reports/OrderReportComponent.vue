<template>
    <div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-xl-11">

                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                        <div class="card-body">
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
                                    <label>Client Type:</label>
                                    <select class="form-control" v-model="client_type">
                                        <option value="all">All</option>
                                        <option value="merchant">Merchants</option>
                                        <option value="non_merchant">Non-Merchant</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label>Destination Type:</label>
                                    <select class="form-control" v-model="destination_type">
                                        <option value="all">All</option>
                                        <option value="inbound">Inbound</option>
                                        <option value="outbound">Outbound</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Service Type:</label>
                                    <select class="form-control" v-model="service_type">
                                        <option value="all">All</option>
                                        <option value="same_day_delivery">Same Day Delivery</option>
                                        <option value="overnight">Overnight</option>
                                        <option value="pickup">Pickup</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label>Order Status:</label>
                                    <select class="form-control" v-model="order_status">
                                        <option value="all">All</option>
                                        <option value="order_pending">Pending Confirmation</option>
                                        <option value="scheduled">Scheduled</option>
                                        <option value="awaiting_dispatch">Awaiting Dispatch</option>
                                        <option value="dispatched">Dispatched</option>
                                        <option value="undispatched">Undispatched</option>
                                        <option value="in_transit">In Transit</option>
                                        <option value="dispatched">Dispatched</option>
                                        <option value="not_dispatched">Not Dispatched</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="returned">Returned</option>
                                        <option value="expired">Expired</option>
                                        <option value="out_of_stock">Out of stock</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label>Payment Status:</label>
                                    <select class="form-control" v-model="payment_status">
                                        <option value="all">All</option>
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-8">
                                    <button class="btn btn-success mr-2" @click.prevent="generateExcel" v-if="orders.length > 0">Generate XLS</button>
                                    <button class="btn btn-secondary mr-2" @click.prevent="generateCsv" v-if="orders.length > 0">Generate CSV</button>
                                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                    <div class="spinner-border text-danger" role="status" v-if="loader">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th title="Field #1">#</th>
                                <th title="Field #1">Order No</th>
                                <th title="Field #3">Receiver</th>
                                <th title="Field #4">Receiver phone</th>
                                <th title="Field #4">Receiver address</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(order, index) in orders">
                                <td>{{ index + 1 }}</td>
                                <td>{{ order.order_no }}</td>
                                <td>{{ order.receiver_name }}</td>
                                <td>{{ order.receiver_phone ? order.receiver_phone : order.receiver_phone_alternative }}</td>
                                <td>{{ order.receiver_address }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </form>

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

        },

        props: {
            causer_id: String,
        },

        data(){
            return{
                order_date: 'all',
                custom_date: '',
                custom_start_date: '',
                custom_end_date: '',
                client_type: 'all',
                destination_type: 'all',
                service_type: 'all',
                order_status: 'all',
                payment_status: 'all',
                loader:false,
                base_url_web_admin: '',
                orders: [],
            }
        },

        methods: {
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

                this.orders = [];
                this.loader = true;
                var order_date = this.order_date;
                var custom_date = this.custom_date;
                var custom_start_date = this.custom_start_date;
                var custom_end_date = this.custom_end_date;
                var client_type = this.client_type;
                var destination_type = this.destination_type;
                var service_type = this.service_type;
                var order_status = this.order_status;
                var payment_status = this.payment_status;

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
                formData.append( 'client_type', client_type);
                formData.append( 'destination_type', destination_type);
                formData.append( 'service_type', service_type);
                formData.append( 'order_status', order_status);
                formData.append( 'payment_status', payment_status);

                let uri = base_url+`report-admin-activity`;
                axios.post(uri, formData)
                    .then(function (response) {
                        vm.orders = response.data;
                        vm.loader = false;
                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                    });

            },

            generateExcel(){
                this.loader = true;
                if(this.orders.length == 0){
                    alert('No order items');
                    return
                }

                let uri = base_url + `v1/report-order-generate`;
                axios({
                    url: uri,
                    method: 'POST',
                    responseType: 'blob',
                    data: {
                        orders: JSON.stringify(this.orders),
                        causer_id: this.causer_id,
                    }
                }).then((response) => {
                    this.loader=false;
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'order-report-xls.xlsx');
                    document.body.appendChild(link);
                    link.click();
                })

                .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                    });

            },

            generateCsv(){
                this.loader = true;
                if(this.orders.length == 0){
                    alert('No order items');
                    return
                }

                let uri = base_url + `v1/report-order-csv`;
                axios({
                    url: uri,
                    method: 'POST',
                    responseType: 'blob',
                    data: {
                        orders: JSON.stringify(this.orders),
                        causer_id: this.causer_id,
                    }
                }).then((response) => {
                    this.loader=false;
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'order-report.csv');
                    document.body.appendChild(link);
                    link.click();
                })

                .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                    });

            }
        }
    }
</script>
