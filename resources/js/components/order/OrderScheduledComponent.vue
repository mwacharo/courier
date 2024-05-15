<template>
    <div>
        <div class="d-flex justify-content-center">
            <div class="col-8 alert alert-warning text-center" role="alert" v-if="data_error">No Scheduled Orders Found!!</div>
            <div class="col-10 alert alert-danger text-center" role="alert" v-if="response_error">An Error Was Encountered.Please Report to IT Team!!</div>
        </div>
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
                                        <option value="all">All Merchants</option>
                                        <option v-for="merchant in merchants" :value="merchant.id">{{ merchant.name }}</option>
                                    </select>
                                </div>

                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-8">
                                    <button class="btn btn-light text-primary mr-2"  v-if="orders.length > 0">Available Orders: {{ orders.length }}</button>
                                    <button class="btn btn-warning mr-2"   v-if="(admin_role ==3 || admin_role ==7)&& orders.length >0" @click.prevent="generatePDF">Print Waybills <i class="flaticon2-printer text-danger"></i></button>
                                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>

                                    <div class="spinner-border spinner-border text-danger" role="status" v-if="loader">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered table-hover table-checkable" id="order_table" style="margin-top: 13px">
                            <thead>
                            <tr>
                                <th title="Field #0">No</th>
                                <th title="Field #1">Order No</th>
                                <th title="Field #2">Sender</th>
                                <th title="Field #3">Receiver</th>
                                <th title="Field #4">Address</th>
                                <th title="Field #5">View</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(order, index) in orders">
                                <td>{{ index + 1}}</td>
                                <td>{{ order.order_no }}</td>
                                <td>{{ order.sender_name }}</td>
                                <td>{{ order.receiver_name }}</td>
                                <td>{{ order.receiver_address }}</td>
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
            this.fetchMerchants();
        },

        props: {
            causer_id: String,
            admin_role: Number,
        },

        data(){
            return{
                order_date: 'all',
                merchant_id: 'all',
                custom_date: '',
                custom_start_date: '',
                custom_end_date: '',
                merchants: [],

                base_url_web_admin: '',
                orders: [],
                response_error: false,
                data_error: false,
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
                formData.append( 'custom_date', custom_date);
                formData.append( 'custom_start_date', custom_start_date);
                formData.append( 'custom_end_date', custom_end_date);
                formData.append( 'merchant_id', merchant_id);

                let uri = base_url+`v1/order-scheduled`;
                axios.post(uri, formData)
                    .then(function (response) {
                        vm.loader = false;
                        if(response.data==0){
                            vm.data_error = true;
                            vm.clearDataError()
                            vm.loader = false;
                        }
                        vm.orders = response.data;

                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

            generateExcel(){

                this.loader = true;
                if(this.orders.length == 0){
                    alert('No order items');
                    this.loader = false;
                    return
                }

                const vm = this;
                let uri = base_url + `v1/order-scheduled-generate-excel`;
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
                    link.setAttribute('download', 'order-scheduled-excel.xls');
                    document.body.appendChild(link);
                    link.click();
                });

            },

            clearDataError(){

                setTimeout(() => {
                    this.data_error = false;
                }, 3000);
                },
            clearResponseError(){

            setTimeout(() => {
                this.response_error = false;
            }, 3000);
            },

            generatePDF(){

                this.loader = true;
                if(this.orders.length == 0){
                    alert('No order items');
                    this.loader = false;
                    return
                };
                if(this.orders.length >300){
                    alert('Orders exceed the maximum printable items');
                    this.loader = false;
                    return
                };

                const vm = this;
                let uri = base_url + `v1/order-scheduled-bulkprint`;
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
                    vm.formSubmit();
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'waybill-printout.pdf');
                    document.body.appendChild(link);
                    link.click();
                }).catch(function (error)
                {
                    vm.loader = false;
                    vm.response_error = true;
                    vm.clearResponseError();
                    console.log(error);
                });;

            }
        }
    }
</script>
