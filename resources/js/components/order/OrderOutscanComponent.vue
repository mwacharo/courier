<template>
    <div class="col-xl-12 col-xxl-10">
        <!--begin::Wizard Form-->
        <form class="form" id="kt_form" @submit.prevent="formSubmit" @keydown.enter.prevent.self method="post">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!--begin::Wizard Step 1-->
                    <div class="alert alert-success" role="alert" v-if="alert_success">Record added successfully!</div>
                    <div class="alert alert-danger" role="alert" v-if="alert_error">No record found!</div>
                    <div class="alert alert-danger" role="alert" v-if="order_error">{{ order_error_message }}</div>


                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                        <h5 class="text-dark font-weight-bold mb-10">Order Details:</h5>

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Order No / Barcode</label>
                            <div class="col-lg-6 col-xl-6">
                                <input class="form-control form-control-solid form-control-lg" v-on:keyup.enter="searchOrder" name="order_no" type="text" v-model="order_no"/>
                            </div>
                            <div class="col-lg-2 col-xl-2">
                                <a href="#" class="btn btn-icon btn-warning btn-circle btn-lg" @click.prevent="searchOrder">

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
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Rider/Driver</label>
                            <div class="col-lg-8 col-lg-8">
                                <select class="form-control form-control-solid form-control-lg" name="name" type="text"
                                        v-model="rider_id">
                                    <option value="">Select Rider</option>
                                    <option v-for="rider in riders" :value="rider.id">{{ rider.first_name }} {{ rider.last_name }}</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Branch</label>
                            <div class="col-lg-8 col-lg-8">
                                <select class="form-control form-control-solid form-control-lg" name="branch_id"  v-model="branch_id">
                                    <option value="">Select Branch</option>
                                    <option v-for="branch in branches" :value="branch.id">{{ branch.name }}</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Group-->

                    </div>

                    <div class="border-top pt-10 mt-15" v-if="orders.length > 0">

                        <h5 class="text-dark font-weight-bold mb-10">Order Items</h5>

                        <!--begin::Group-->
                        <div class="form-group row">
                            <div class="col-lg-12 col-xl-12">
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
                                            <a v-bind:href='base_url_web_admin+"order-details/"+order.id'>View details</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <!--end::Group-->

                    </div>


                    <div class="d-flex justify-content-between border-top pt-10 mt-15">
                        <div>

                            <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4"
                                    data-wizard-type="action-submit">Scan Out
                            </button>
                            <div class="spinner-border spinner-border" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <!--end::Wizard Actions-->
                </div>
            </div>
        </form>
        <!--end::Wizard Form-->

    </div>


</template>

<script>
    export default {

        mounted() {
            this.fetchBranches();
            this.fetchRiders();
        },

        props: {
            causer_id: String,
        },

        data() {
            return {
                order_no: '',
                order_id: '',
                branch_id: '',
                rider_id: '',
                branches: [],
                orders: [],
                riders: [],

                order_error: false,
                alert_error: false,
                order_error_message: '',
                alert_success: false,
                loader: false,

                base_url_web_admin: '',

            }
        },

        methods: {

            fetchBranches() {
                let uri = base_url + `v1/branch-list`;
                axios.get(uri).then((response) => {
                    this.branches = response.data;
                });
            },

            fetchRiders() {
                let uri = base_url + `v1/rider-list`;
                axios.get(uri).then((response) => {
                    this.riders = response.data;
                });
            },

            searchOrder() {

                this.order_error = false;
                this.alert_error = false;
                this.order_error_message = '';

                if(this.orders.some(order => order.order_no === this.order_no)){
                    this.order_no = '';
                    alert("Order already scanned!");
                    return
                }


                const vm = this;
                const formData = new FormData();
                formData.append('order_no', this.order_no);
                let uri = base_url + `v1/inventory-order-search`;
                axios.post(uri, formData)
                    .then(function (response) {

                        if(Object.keys(response.data).length === 0) {
                            vm.order_no = '';
                            alert("Order not available");
                            return;
                        } else {
                            var order = response.data;
                            if(order.order_status == "order_pending"){
                                vm.order_error = true;
                                vm.order_error_message = "Order still pending";
                                vm.clearResponseMsg()
                                return;
                            }else if(order.order_status == "cancelled"){
                                vm.order_error = true;
                                vm.order_error_message = "Order was cancelled";
                                vm.clearResponseMsg()
                                return;
                            }else if(order.order_status == "dispatched"){
                                vm.order_error = true;
                                vm.order_error_message = "Order already dispatched";
                                vm.clearResponseMsg()
                                return;
                            }else if(order.order_status == "delivered"){
                                vm.order_error = true;
                                vm.order_error_message = "Order already delivered";
                                vm.clearResponseMsg()
                                return;
                            }
                            else if(order.order_status == "in_transit"){
                                vm.order_error = true;
                                vm.order_error_message = "Order already in transit";
                                vm.clearResponseMsg()
                                return;
                            }

                            vm.order_no = '';
                            vm.orders.push(order);
                        }

                    })
                    .catch(function (error) {
                        vm.order_no = '';
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });
            },


            formSubmit() {

                this.loader = true;
                var branch_id = this.branch_id;
                var rider_id = this.rider_id;

                if (this.orders.length == 0) {
                    alert('Scan atleast one order');
                    this.loader = false;
                    return
                }

                if (!branch_id) {
                    alert('Select branch');
                    this.loader = false;
                    return
                }

                if (!rider_id) {
                    alert('Select rider');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append('orders', JSON.stringify(this.orders));
                formData.append('branch_id', branch_id);
                formData.append('rider_id', rider_id);
                formData.append('causer_id', this.causer_id);

                let uri = base_url + `v1/order-outscan-create`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if (status === 1) {
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Order successfully dispatched!');
                            window.location = response.data.redirect;

                        }  else {

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

            clearResponseMsg(){

            setTimeout(() => {
                this.order_error = false;
            }, 3000);


            },
        }
    }
</script>
