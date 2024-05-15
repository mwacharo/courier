<template>
    <div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-xl-11">

                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Status Date:</label>
                                    <select class="form-control" v-model="status_date" @change.prevent="selectedDate">
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
                                    <label>Upload Date:</label>
                                    <select class="form-control" v-model="upload_date" @change.prevent="uploadDate">
                                        <option value="all">All</option>
                                        <option value="today">Today</option>
                                        <option value="current_week">Current Week</option>
                                        <option value="last_week">Last Week</option>
                                        <option value="current_month">Current Month</option>
                                        <option value="current_year">Current Year</option>
                                        <option value="custom_date">Custom Date</option>
                                        <option value="custom_upload_range">Custom Range</option>
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
                                        <option value="scheduled">Scheduled</option>
                                                    <option value="awaiting_dispatch">Awaiting Dispatch</option>
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
                                        <option value="not_dispatched">Not Dispatched</option>
                                        <option value="in_tansit">In Transit</option>
                                        <option value="delivered">Delivered</option>
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
                                    <div class="spinner-border spinner-border text-warning" role="status" v-if="loader">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th title="Field #1">Order No</th>
                                <th title="Field #2">Sender</th>
                                <th title="Field #3">Receiver</th>
                                <th title="Field #4">Address</th>
                                <th title="Field #5">Order Status</th>
                                <th title="Field #6">Created At</th>
                                <th title="Field #7">View</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(order, index) in orders">
                                <td>{{ order.order_no }}</td>
                                <td>{{ order.sender_name }}</td>
                                <td>{{ order.receiver_name }}</td>
                                <td>{{ order.receiver_address }}</td>
                                <td>{{ order.order_status }}</td>
                                <td>{{ order.created_at }}</td>
                                <td>
                                    <a v-bind:href='"order-details/"+order.id'>View details</a>
                                </td>
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

        props: {
            merchant_id: String,
        },

        data(){
            return{
                upload_date:'all',
                status_date:'all',

                custom_date: '',
                custom_start_date: '',
                custom_end_date: '',
                destination_type: 'all',
                service_type: 'all',
                order_status: 'all',
                payment_status: 'all',
                loader:false,
                base_url_web_merchant: '',
                orders: [],
            }
        },

        methods: {

            selectedDate(){
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";

                if(this.status_date == 'custom_date'){
                    $('#modalCustomDate').modal('show');
                }else if(this.status_date == 'custom_range'){
                    $('#modalCustomRange').modal('show');
                }

            },
            uploadDate(){
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";

                if(this.upload_date == 'custom_date'){
                    $('#modalCustomDate').modal('show');
                }else if(this.upload_date == 'custom_range'){
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

            customDateSubmit(){

            if(this.custom_upload_date == ''){
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
                this.status_date = "all";
                this.upload_date = "all";
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";
            },

            formSubmit(){

                this.orders = [];
                this.loader = true;
                var status_date = this.status_date;
                var upload_date = this.upload_date;
                var custom_date = this.custom_date;
                var custom_start_date = this.custom_start_date;
                var custom_end_date = this.custom_end_date;
                var merchant_id = this.merchant_id;
                var destination_type = this.destination_type;
                var service_type = this.service_type;
                var order_status = this.order_status;
                var payment_status = this.payment_status;

                if(status_date == 'custom_date'){

                    if(this.custom_date == ''){
                        alert("Custom date not selected");
                        return;
                    }

                }

                if(status_date == 'custom_range'){

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
                formData.append( 'status_date', status_date);
                formData.append( 'upload_date', upload_date);
                formData.append( 'custom_date', custom_date);
                formData.append( 'custom_start_date', custom_start_date);
                formData.append( 'custom_end_date', custom_end_date);
                formData.append( 'merchant_id', merchant_id);
                formData.append( 'destination_type', destination_type);
                formData.append( 'service_type', service_type);
                formData.append( 'order_status', order_status);
                formData.append( 'payment_status', payment_status);

                let uri = base_url+`v1/merchant-order-report`;
                axios.post(uri, formData)
                    .then(function (response) {
                        vm.loader = false;
                        vm.orders = response.data;

                    })
                    .catch(function (error) {
                        vm.loader = false;
                        console.log(error);
                    });

            },

            generateExcel(){

                if(this.orders.length == 0){
                    alert('No items to generate');
                    return
                }

                let uri = base_url + `v1/merchant-order-report-generate`;
                axios({
                    url: uri,
                    method: 'POST',
                    responseType: 'blob',
                    data: {
                        orders: JSON.stringify(this.orders),
                        merchant_id: this.merchant_id,
                    }
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'order-report-excel.xls');
                    document.body.appendChild(link);
                    link.click();
                });

            },
            generateCsv(){

            if(this.orders.length == 0){
                alert('No items to generate');
                return
            }

            let uri = base_url + `v1/merchant-order-report-csv`;
            axios({
                url: uri,
                method: 'POST',
                responseType: 'blob',
                data: {
                    orders: JSON.stringify(this.orders),
                    merchant_id: this.merchant_id,
                }
            }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'merchant-report.csv');
                document.body.appendChild(link);
                link.click();
            });

            }
        }
    }
</script>
