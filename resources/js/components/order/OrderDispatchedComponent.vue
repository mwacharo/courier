<template>
    <div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-xl-11">


                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                        <div class="alert alert-success" role="alert" v-if="alert_success">Order Undispatched successfully!</div>
                        <div class="alert alert-danger" role="alert" v-if="alert_error">Failed to Undispatch order!</div>

                        <div class="alert alert-success" role="alert" v-if="transit_success">Transit of orders confirmed!</div>
                        <div class="alert alert-danger" role="alert" v-if="transit_error">Failed to dispatch orders!</div>

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
                                    <label>Rider Details:</label>
                                    <select class="form-control" v-model="rider_id">
                                        <option value="">Select Rider</option>
                                        <option v-for="rider in riders" :value="rider.id">{{ rider.first_name }} {{ rider.last_name }}</option>
                                    </select>
                                </div>

                                <div class="col-10 pt-3">
                                    <div class="form-group row">

                                        <div class="col-lg-6 col-xl-6">
                                            <input class="form-control form-control-solid form-control-lg" placeholder="Enter order no / scan barcode" v-on:keyup.prevent="getCustomReason" name="order_no" type="text" v-model="order_no"/>
                                        </div>
                                        <div class="col-lg-2 col-xl-2">
                                            <a href="#" class="btn btn-icon btn-info btn-circle btn-lg" @click.prevent="getCustomReason">

                                                <span class="svg-icon"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\General\Search.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                        <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"/>
                                                    </g>
                                                </svg><!--end::Svg Icon--></span>

                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-8">

                                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset" class="btn btn-secondary mr-2">Cancel</button>
                                    <button type="button" class="btn btn-danger ml-2" data-toggle="modal" data-target="#transitModal"  v-if="orders.length > 0">
                                        Transit
                                    </button>
                                    <div class="spinner-border text-warning" role="status" v-if="loader">
                                      <span class="sr-only">Loading..</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <div>
            <table class="table table-bordered table-hover table-checkable" id="order_table">
                <thead>
                <tr>
                    <th>#</th>
                    <th title="Field #1">Order No</th>
                    <th title="Field #2">Sender</th>
                    <th title="Field #3">Receiver</th>
                    <th title="Field #4">Address</th>
                    <th title="Field #5">Order Status</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(order, index) in orders">
                    <td>{{ index + 1 }}</td>
                    <td>{{ order.order_no }}</td>
                    <td>{{ order.sender_name }}</td>
                    <td>{{ order.receiver_name }}</td>
                    <td>{{ order.receiver_address }}</td>
                    <td class="text-info">{{ order.order_status }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="transitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel text-success">Transit Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span class="text-info">Confirm Transit Of These Orders</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" @click.prevent="transitOrders" class="btn btn-primary">Confirm</button>
            </div>
            </div>
        </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="orderUndispatchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="orderUndispatch" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Reason For Undispatching Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!--begin::Group-->
                            <div class="form-group">
                                <select class="form-control form-control-solid" v-model="custom_reason">
                                    <option value="">Select Reason</option>
                                    <option value="not_picking_calls">Not Picking Calls</option>
                                    <option value="not_ready_financially">Not financially Ready</option>
                                    <option value="assign_another_rider"> <span class="text-info">Assign another Rider</span> </option>
                                    <option value="office_pickup">Office Pickup</option>
                                    <option value="outbound_order">Outbound Order</option>
                                    <option value="offline">Offline</option>
                                    <option value="schedule_for_another_day"> <span class="text-info">Schedule for another day</span></option>
                                    <option value="order_cancelled">Order was cancelled</option>
                                    <option value="user_busy">User Busy</option>
                                    <option value="unresponsive">Unresponsive</option>
                                    <option value="duplicate_order">Duplicate Order</option>
                                    <option value="duplicate_of_delivered">Duplicate of aleady delivered</option>
                                    <option value="no_longer_interested">No longer interested</option>
                                    <option value="wrong_order">Wrong Order</option>
                                    <option value="will_call_later_date">Will Call at later date</option>
                                    <option value="not_around">Client not around</option>
                                </select>
                            </div>
                            <!--end::Group-->

                            <!--scheduled date-->
                            <div class="form-group row" v-if="custom_reason=='schedule_for_another_day'">
                                <label>Select Date:</label>
                                <input class="form-control form-control-solid form-control-lg" name="scheduled_date" type="date" v-model="scheduled_date"/>
                            </div>
                            <!--end::Group-->

                             <!--select rider-->
                             <div class="form-group row" v-if="custom_reason=='assign_another_rider'">
                                <label>Rider Details:</label>
                                <select class="form-control"  v-model="selected_rider_id">
                                    <option value="">Select Rider</option>
                                    <option v-for="rider in riders" :value="rider.id">{{ rider.first_name }} {{ rider.last_name }}</option>
                                </select>

                            </div>
                            <!--end::Group-->



                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
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
            this.fetchRiders();
        },

        props: {
            causer_id: String,
        },

        data(){
            return{
                order_no: '',
                order_date: 'all',
                rider_id: '',
                rider_name: '',
                custom_date: '',
                custom_start_date: '',
                custom_reason: '',
                custom_end_date: '',
                riders: [],
                base_url_web_admin: '',
                orders: [],
                loader: false,
                alert_error: false,
                alert_success: false,
                transit_error: false,
                transit_success: false,
                selected_rider_id:'',
                scheduled_date:'',
            }
        },

        methods: {

            fetchRiders() {
                let uri = base_url + `v1/rider-list`;
                axios.get(uri).then((response) => {
                    this.riders = response.data;
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

            getCustomReason(){
                var order_no = this.order_no;

                //check if order exists in riders report
                // if(!this.orders.some(order => order.order_no === this.order_no)){

                //     alert("Order not available in the rider's outscan report!");
                //     this.order_no = '';
                //     return
                // }
                // if(order_no == '' || order_no == 'undefined' || order_no == 'null'){

                //     alert("Please scan the barcode");

                //     this.alert_error = false;
                //     this.alert_success = false;

                //     this.loader = false;
                //     return;
                // }


                if(this.rider_id == '' || this.rider_id == 'undefined' || this.rider_id == 'null'){

                alert("Please select a rider");

                this.alert_error = false;
                this.alert_success = false;

                this.loader = false;
                return;

                }

                this.formSubmit();

                $('#orderUndispatchModal').modal('show');
                },

            orderUndispatch(){

                let custom_reason  = this.custom_reason;
                let order_no  = this.order_no;
                let selected_rider_id  = this.selected_rider_id;
                let sheduled_date  = this.scheduled_date;



                if(this.custom_reason == '' || this.custom_reason == 'undefined' || this.custom_reason == 'null'){
                        alert("Please enter a custom reason");
                        this.alert_error = false;
                        this.alert_success = false;
                        return;
                    }



                    const vm = this;
                    const formData = new FormData();
                    formData.append( 'order_no', order_no);
                    formData.append('custom_reason', custom_reason);
                    formData.append('causer_id', this.causer_id);
                    formData.append( 'selected_rider_id', selected_rider_id);
                    formData.append( 'scheduled_date', this.scheduled_date);

                    let uri = base_url+`v1/order-undispatch`;
                    axios.post(uri, formData)
                        .then(function () {
                            vm.alert_success = true;
                            vm.alert_error = false;
                            $('#orderUndispatchModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();
                            vm.order_no='';
                            vm.selected_rider_id='';
                            vm.scheduled_date='';
                            vm.custom_reason='';
                            vm.formSubmit();
                            vm.clearResponseMsg();

                        })
                        .catch(function (error) {
                            console.log(error);
                            vm.alert_error = true;
                            vm.alert_success = false;
                            $('#orderUndispatchModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        });

               },

            transitOrders(){
                let orders  = this.orders;
                let admin_id = this.causer_id;

                const vm = this;
                const formData = new FormData();
                formData.append( 'orders', JSON.stringify(orders));
                formData.append( 'admin_id', admin_id);

                let uri = base_url+`v1/order-transit`;
                    axios.post(uri, formData)
                        .then(function () {
                           alert("Transit Of Orders Confirmed Successfully!")
                            $('#transitModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();
                            vm.rider_id ="";
                            vm.formSubmit();

                        })
                        .catch(function (error) {
                            console.log(error);
                            this.transit_error = true;
                            this.transit_success = false;
                            $('#transitModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();

                        });

            },

            clearResponseMsg(){

            setTimeout(() => {
                this.alert_success = false;
            }, 2000);


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
                let order_no = this.order_no;
                let rider_id = this.rider_id;
                this.loader = true;
                let order_date = this.order_date;
                let custom_date = this.custom_date;
                let custom_start_date = this.custom_start_date;
                let custom_end_date = this.custom_end_date;


                if(this.rider_id == ''){

                    alert("Please select a rider");
                    this.alert_error = false;
                    this.alert_success = false;

                    this.loader = false;
                    return;
                }


                if(order_date == 'custom_date'){

                    if(this.custom_date == ''){
                        alert("Custom date not selected");
                        this.loader = false;
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
                formData.append( 'rider_id', rider_id);

                let uri = base_url+`v1/order-dispatched`;
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

        }
    }
</script>
