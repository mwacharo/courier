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
                                        <option value="all">Select Merchant</option>
                                        <option v-for="merchant in merchants" :value="merchant.id">{{ merchant.name }}</option>
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

                                    <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Order No</th>
                                <th>Total Amount</th>
                                <th>Item Quantity</th>
                                <th>Item Description</th>
                                <th>Client Name</th>
                                <th>Location</th>
                                <th>Town</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Custom Reason</th>
                                <th>Delivery Date</th>
                                <th>Status Date</th>
                                <th>Updated Date</th>
                                <th>View</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(order, index) in orders">
                                <td>{{ index + 1 }}</td>
                                <td>{{ order.order_no }}</td>
                                <td>{{ order.amount }}</td>
                                <td>{{ order.quantity }}</td>
                                <td>{{ order.item_name }}</td>
                                <td>{{ order.receiver_name }}</td>
                                <td>{{ order.receiver_address }}</td>
                                <td>{{ order.receiver_town_name }}</td>
                                <td>{{ order.receiver_phone }}</td>
                                <td>
                                    <span v-if="order.order_status == 'order_pending'">PENDING</span>
                                    <span v-else-if="order.order_status == 'follow_up'">FOLLOW UP</span>
                                    <span v-else-if="order.order_status == 'scheduled'">SCHEDULED</span>
                                    <span v-else-if="order.order_status == 'dispatched'">DISPATCHED</span>
                                    <span v-else-if="order.order_status == 'undispatched'">UNDISPATCHED</span>
                                    <span v-else-if="order.order_status == 'in_transit'">IN TRANSIT</span>
                                    <span v-else-if="order.order_status == 'delivered'">DELIVERED</span>
                                    <span v-else-if="order.order_status == 'returned'">RETURNED</span>
                                    <span v-else-if="order.order_status == 'out_of_stock'">OUT OF STOCK</span>
                                    <span v-else-if="order.order_status == 'expired'">EXPIRED</span>
                                    <span v-else-if="order.order_status == 'cancelled'">CANCELLED</span>
                                </td>
                                <td>{{ order.custom_reason }}</td>
                                <td>{{ order.delivered_date }}</td>
                                <td>{{ order.status_date }}</td>
                                <td>{{ order.updated_at }}</td>
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
    export default {

        mounted(){
            this.fetchMerchants();
        },

        props: {
            causer_id: String,
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

                let uri = base_url+`v1/report-merchant-remittance`;
                axios.post(uri, formData)
                    .then(function (response) {
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
                let uri = base_url + `v1/report-merchant-remittance-generate`;
                axios({
                    url: uri,
                    method: 'POST',
                    responseType: 'blob',
                    data: {
                        orders: JSON.stringify(this.orders),
                        merchant_id: this.merchant_id,
                    }
                }).then((response) => {
                    vm.loader = false;
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'merchant-remittance-excel.xls');
                    document.body.appendChild(link);
                    link.click();
                });

            },

        }
    }
</script>
