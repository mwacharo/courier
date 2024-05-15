<template>
    <div class="col-xl-12 col-xxl-10">
        <!--begin::Wizard Form-->
        <form class="form" id="kt_form" @submit.prevent="formSubmit" @keydown.enter.prevent.self method="post">
            <div class="container row justify-content-center">

                    <!--begin::Wizard Step 1-->
                    <div class="alert alert-success" role="alert" v-if="alert_success">Order Checked In successfully!</div>
                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error adding record!</div>
                    <div class="alert alert-danger" role="alert" v-if="order_error">{{ order_error_message }}</div>


                    <div class="card-body">
                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label>Branch Details:</label>
                                    <select class="form-control" v-model="order_branch" @change.prevent="selectedDate">
                                        <option value="">Select Branch</option>
                                        <option v-for="branch in branches" :value="branch.id">{{ rider.rider_name }} {{ rider.rider_name }}</option>
                                    </select>
                                </div>

                                <div class="col-lg-6">
                                    <label>Agent Details:</label>
                                    <select class="form-control" v-model="agent_id">
                                        <option value="">Select Agent</option>
                                        <option v-for="rider in riders" :value="rider.id">{{ rider.rider_name }} {{ rider.rider_name }}</option>
                                    </select>
                                </div>

                                <div class="col-10 pt-3">
                                    <div class="form-group row">

                                        <div class="col-lg-6 col-xl-6">
                                            <input class="form-control form-control-solid form-control-lg" placeholder="Scan Barcode" v-on:keyup.prevent="getCustomReason" name="order_no" type="text" v-model="order_no"/>
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
                    <div class="border-top pt-10 mt-15" v-if="order_exist">

                        <h5 class="text-dark font-weight-bold mb-10">Order Items</h5>

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Return partial items?</label>
                            <div class="col-4">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select"
                                           v-model="return_partial_items"/>
                                    <span></span>
                                </label>
                            </span>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <div class="col-lg-12 col-xl-12">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Returned</th>
                                        <th>Total Weight</th>
                                        <th v-if="return_partial_items">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in items" v-if="item.inventory_id!=''">
                                        <td>{{ item.description }}</td>
                                        <td>Kshs {{ item.price }}</td>
                                        <td>{{ item.quantity }}</td>
                                        <td>{{ item.quantity_returned }}</td>
                                        <td>{{ item.weight }}kg</td>
                                        <td v-if="return_partial_items"><a href="#" data-toggle="modal" data-target="#modalReturnItem" class="text-danger" @click.prevent="itemReturnItem(item, index)">Return</a> </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <!--end::Group-->

                    </div>

                    <div class="border-top pt-10 mt-15" v-if="order_exist">

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
                                                <span class="font-size-lg font-weight-bolder mb-1">RETURN AMOUNT</span>
                                                <span class="font-size-h2 font-weight-boldest text-danger mb-1">Kshs {{ return_amount }}</span>
                                                <span>Order Amount: Kshs {{ amount }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Group-->

                    </div>

                    <div class="d-flex justify-content-between border-top pt-10 mt-15">
                        <div>

                            <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4"
                                    data-wizard-type="action-submit" v-if="enable_dispatch">Scan In
                            </button>
                            <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <!--end::Wizard Actions-->

            </div>
        </form>
        <!--end::Wizard Form-->

        <div class="modal fade" id="modalReturnItem" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="itemEditSubmit" method="post">
                    <div class="modal-content modal-dialog-scrollable">
                        <div class="modal-header">
                            <h5 class="modal-title">Return Item</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="itemClose">
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
                                            <label class="col-xl-4 col-lg-4 col-form-label">Quantity Returned</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="item_quantity" type="number" min="0" onwheel="this.blur()" v-model="item_quantity_returned"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-4 col-form-label">Items Spoilt</label>
                                            <div class="col-4">
                                                <span class="switch">
                                                    <label>
                                                        <input type="checkbox" checked="checked" name="select" v-model="item_spoilt"/>
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="itemClose">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Edit Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>


</template>

<script>
    export default {

        mounted() {
            this.fetchAgents();

        },

        props: {
            causer_id: String,
        },

        data() {
            return {
                order_no: '',
                order_id: '',
                sender_name: '',
                sender_phone: '',
                receiver_name: '',
                receiver_phone: '',
                rider_name: '',
                rider_phone: '',
                amount: '',
                return_amount: '',
                agent_id: '',
                remarks: '',
                item_count: 0,
                total_weight: 0,
                order_status: '',
                return_partial_items: false,
                order_exist: false,
                enable_dispatch: false,
                branches: [],
                agents: [],

                item_inventory_id: '',
                item_inventory_quantity: 0,
                item_inventory_amount: 0,
                item_sku: '',
                item_index: '',
                item_description: '',
                item_price: '',
                item_quantity: '',
                item_quantity_returned: '',
                item_spoilt: false,
                item_weight: '',
                items: [],

                order_error: false,
                order_error_message: '',
                alert_error: false,
                alert_success: false,
                loader: false,

            }
        },

        methods: {
            fetchAgents() {
                let uri = base_url + `v1/rider-list`;
                axios.get(uri).then((response) => {
                    this.agents = response.data;
                });
            },

            itemReturnItem(item, index){

                this.item_index = index;
                this.item_inventory_id = item.inventory_id;
                this.item_description = item.description;
                this.item_price = item.price;
                this.item_quantity = item.quantity;
                this.item_quantity_returned = item.quantity_returned;
                this.item_weight = item.weight;
                this.item_sku = item.sku;
                this.item_spoilt = item.spoilt;

            },

            itemEditSubmit() {

                if(this.return_partial_items == true){


                    if (!this.item_quantity_returned) {
                        alert('Enter Returned Quantity');
                        return
                    }

                    if(this.item_quantity < this.item_quantity_returned){
                        alert("Return quantity exceeds dispatched quantity");
                        return;
                    }

                }

                this.items[this.item_index].description = this.item_description;
                this.items[this.item_index].price = this.item_price;
                this.items[this.item_index].weight = this.item_weight;
                this.items[this.item_index].sku = this.item_sku;
                this.items[this.item_index].inventory_id = this.item_inventory_id;
                this.items[this.item_index].quantity = this.item_quantity;
                this.items[this.item_index].quantity_returned = this.item_quantity_returned;
                this.items[this.item_index].spoilt = this.item_spoilt;

                this.item_description = "";
                this.item_price = "";
                this.item_quantity = "";
                this.item_quantity_returned = "";
                this.item_weight = "";
                this.item_sku = "";
                this.item_inventory_id = "";
                this.item_spoilt = false;

                $('#modalReturnItem').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();

            },

            itemClose(){
                this.item_description = "";
                this.item_price = "";
                this.item_quantity = "";
                this.item_quantity_returned = "";
                this.item_weight = "";
                this.item_sku = "";
                this.item_spoilt = false;
            },

            searchOrder() {

                this.order_error = false;
                this.order_error_message = '';
                this.order_exist = false;
                this.enable_dispatch = false;
                this.order_id = "";
                this.sender_name = "";
                this.sender_phone = "";
                this.receiver_name = "";
                this.receiver_phone = "";
                this.rider_name = "";
                this.rider_phone = "";
                this.order_status = "";
                this.amount = "";

                const vm = this;
                const formData = new FormData();
                formData.append('order_no', this.order_no);

                let uri = base_url + `v1/order-search`;
                axios.post(uri, formData)
                    .then(function (response) {

                        if(Object.keys(response.data).length === 0) {
                            alert("Order not available");
                            vm.order_exist = false;
                            vm.order_exist = enable_dispatch;
                        } else {
                            vm.order_id = response.data.id;
                            vm.sender_name = response.data.sender_name;
                            vm.sender_phone = response.data.sender_phone;
                            vm.receiver_name = response.data.receiver_name;
                            vm.receiver_phone = response.data.receiver_phone;
                            vm.rider_name = response.data.rider_name;
                            vm.rider_phone = response.data.rider_phone;
                            vm.order_status = response.data.order_status;
                            vm.amount = response.data.amount;


                            if(response.data.is_sender_merchant == 0){
                                vm.enable_dispatch = false;
                                vm.order_error = true;
                                vm.order_error_message = "Order does not belong to merchant";
                                return;
                            }

                            if(response.data.returns_management_fee == 1){
                                vm.return_amount = Math.round(vm.amount / 2);
                            }else{
                                vm.return_amount = response.data.amount;
                            }

                            vm.order_exist = true;
                            vm.fetchOrderItems(vm.order_id);

                            if(response.data.inventory == 0){
                                vm.enable_dispatch = false;
                                vm.order_error = true;
                                vm.order_error_message = "Order does not have inventory items";
                                return;
                            }

                            if(response.data.order_status == "delivered"){
                                vm.enable_dispatch = false;
                                vm.order_error = true;
                                vm.order_error_message = "Order delivered";
                                return;
                            }else if(response.data.order_status == "order_pending"){
                                vm.enable_dispatch = false;
                                vm.order_error = true;
                                vm.order_error_message = "Order pending confirmation (Not dispatched yet)";
                                return;
                            }else if(response.data.order_status == "scheduled"){
                                vm.enable_dispatch = false;
                                vm.order_error = true;
                                vm.order_error_message = "Order pending dispatch (Not dispatched yet)";
                                return;
                            }else if(response.data.order_status == "returned"){
                                vm.enable_dispatch = false;
                                vm.order_error = true;
                                vm.order_error_message = "Order already returned";
                                return;
                            }

                            vm.enable_dispatch = true;

                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });
            },

            fetchOrderItems(order_id) {
                const vm = this;
                let uri = base_url+`v1/order-items/`+order_id;
                axios.get(uri).then((response) => {
                    var order_items = response.data;
                    for (var i = 0; i < order_items.length; i++){
                        var object = {};
                        object['id'] = order_items[i].id;
                        object['order_id'] = order_items[i].order_id;
                        object['description'] = order_items[i].description;
                        object['inventory_id'] = order_items[i].inventory_id;
                        object['inventory_product'] = order_items[i].inventory_product;
                        object['price'] = order_items[i].price;
                        object['quantity'] = order_items[i].quantity;
                        object['quantity_returned'] = order_items[i].quantity;
                        object['weight'] = order_items[i].weight;
                        object['spoilt'] = 0;
                        object['created_at'] = order_items[i].created_at;
                        object['updated_at'] = order_items[i].updated_at;
                        object['deleted_at'] = order_items[i].deleted_at;
                        vm.items.push(object);
                    }
                });
            },

            formSubmit() {

                const vm = this;
                this.loader = true;
                var order_no = this.order_no;
                var order_id = this.order_id;
                var branch_id = this.branch_id;
                var remarks = this.remarks;
                var return_amount = this.return_amount;
                var return_partial_items = this.return_partial_items;

                if (!order_id) {
                    alert('Enter order');
                    this.loader = false;
                    return
                }

                if (!branch_id) {
                    alert('Select branch');
                    this.loader = false;
                    return
                }

                if (!branch_id) {
                    alert('Enter return amount');
                    this.loader = false;
                    return
                }

                if(return_partial_items == true){
                    for (var i = 0; i < vm.items.length; i++){
                        if(vm.items[i].quantity_returned == ''){
                            alert('Enter returned quantity for ' + vm.items[i].description);
                            this.loader = false;
                            return
                        }
                    }
                }


                const formData = new FormData();
                formData.append('order_no', order_no);
                formData.append('order_id', order_id);
                formData.append('branch_id', branch_id);
                formData.append('remarks', remarks);
                formData.append('return_amount', return_amount);
                formData.append('return_partial_items', return_partial_items);
                formData.append('items', JSON.stringify(this.items));
                formData.append('causer_id', this.causer_id);

                let uri = base_url + `v1/order-inscan-create`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if (status === 1) {
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Order successfully returned!');
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
        }
    }
</script>
