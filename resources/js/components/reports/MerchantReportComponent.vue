<template>
    <div>
        <div class="d-flex justify-content-center">
            <div class="col-10 alert alert-warning text-center" role="alert" v-if="data_error">No Record Found!!</div>
        </div>
        <div class="d-flex justify-content-center">
            <div class="col-10 alert alert-warning text-center" role="alert" v-if="file_error">Prolonged Query alert!!</div>
        </div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-xl-11">

                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Status Date:</label>
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
                                    <label>Upload Date:</label>
                                    <select class="form-control" v-model="upload_date" @change.prevent="uploadDate">
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
                                    <label>Merchant Detail:</label>
                                    <select class="form-control" v-model="merchant_id">
                                        <option value="all">All Merchants</option>
                                        <option v-for="merchant in merchants" :value="merchant.id">{{ merchant.name }}</option>
                                    </select>
                                </div>

                            </div>
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Destination Type:</label>
                                    <select class="form-control" v-model="destination_type">
                                        <option value="all">All</option>
                                        <option value="inbound">Inbound</option>
                                        <option value="outbound">Outbound</option>
                                    </select>
                                </div>
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
                                        <option value="not_dispatched">Not Dispatched</option>
                                        <option value="dispatched">Dispatched</option>
                                        <option value="in_transit">In transit</option>
                                        <option value="undispatched">Undispatched</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="returned">Returned</option>
                                        <option value="expired">Expired</option>
                                        <option value="out_of_stock">Out of stock</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
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
                                <th title="Field #0">#</th>
                                <th title="Field #1">Order No</th>
                                <th title="Field #2">Product Item</th>
                                <th title="Field #3">Sender</th>
                                <th title="Field #4">Receiver</th>
                                <th title="Field #4">Call agent</th>
                                <th title="Field #5">Address</th>
                                <th title="Field #6">Order Status</th>
                                <th title="Field #7">Status Date</th>
                                <th title="Field #7">Upload Date</th>
                                <th title="Field #8">View</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(order, index) in orders">
                                <td>{{ index + 1 }}</td>
                                <td>{{ order.order_no }}</td>
                                <td>{{ order.item_name }}</td>
                                <td>{{ order.sender_name }}</td>
                                <td>{{ order.receiver_name }}</td>
                                <td>{{ order.agent }}</td>
                                <td>{{ order.receiver_address }}</td>
                                <td>{{ order.order_status }}</td>
                                <td>{{ order.status_date }}</td>
                                <td>{{ order.created_at }}</td>
                                <td>
                                    <a v-bind:href='base_url_web_admin+"order-details/"+order.id'>View details</a>
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
                upload_date:'all',
                custom_upload_date: '',
                custom_upload_start_date: '',
                custom_upload_end_date: '',
                order_date: 'all',
                custom_date: '',
                custom_start_date: '',
                custom_end_date: '',
                merchant_id: 'all',
                destination_type: 'all',
                service_type: 'all',
                order_status: 'all',
                payment_status: 'all',
                merchants: [],
                loader:false,
                data_error: false,
                file_error: false,
                base_url_web_admin: '',
                orders: [],
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
                var upload_date = this.upload_date;
                var custom_date = this.custom_date;
                var custom_start_date = this.custom_start_date;
                var custom_end_date = this.custom_end_date;
                var merchant_id = this.merchant_id;
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

                if(this.merchant_id == ''){
                    alert("Please Select merchant");
                    return;
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'order_date', order_date);
                formData.append( 'upload_date', upload_date);
                formData.append( 'custom_date', custom_date);
                formData.append( 'custom_start_date', custom_start_date);
                formData.append( 'custom_end_date', custom_end_date);
                formData.append( 'merchant_id', merchant_id);
                formData.append( 'destination_type', destination_type);
                formData.append( 'service_type', service_type);
                formData.append( 'order_status', order_status);
                formData.append( 'payment_status', payment_status);

                let uri = base_url+`v1/report-merchant`;
                axios.post(uri, formData)
                    .then(function (response) {
                        vm.orders = response.data;
                        if(response.data.length < 1){
                         vm.data_error = true;
                        }

                        // if(response.data.length > 10000){
                        //  vm.file_error = true;
                        // }
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
                    alert('No items to generate');
                    return
                }

                let uri = base_url + `v1/report-merchant-generate`;
                axios({
                    url: uri,
                    method: 'POST',
                    responseType: 'blob',
                    data: {
                        orders: JSON.stringify(this.orders),
                        merchant_id: this.merchant_id,
                        causer_id: this.causer_id,
                    }
                }).then((response) => {
                    this.loader = false;
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'merchant-report-excel.xls');
                    document.body.appendChild(link);
                    link.click();
                });

            }
        }
    }
</script>
