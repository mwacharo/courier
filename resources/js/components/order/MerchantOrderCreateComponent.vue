<template>
    <div class="col-xl-12 col-xxl-10">
        <!--begin::Wizard Form-->
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!--begin::Wizard Step 1-->
                    <div class="alert alert-success" role="alert" v-if="alert_success">Record added successfully!</div>
                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error adding record!</div>

                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                        <h5 class="text-dark font-weight-bold mb-10">Order Details:</h5>

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Order No <br> <small class="text-danger">(Blank will autogenerate)</small></label>
                            <div class="col-lg-8 col-xl-8">
                                <input class="form-control form-control-solid form-control-lg" name="order_no"
                                       type="text" v-model="order_no"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Enable Cash On Delivery</label>
                            <div class="col-4">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="cash_on_delivery"/>
                                    <span></span>
                                </label>
                            </span>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row" v-if="cash_on_delivery">
                            <label class="col-xl-4 col-lg-4 col-form-label">Cash On Delivery Amount</label>
                            <div class="col-lg-8 col-lg-8">
                                <input class="form-control form-control-solid form-control-lg"
                                       name="cash_on_delivery_amount" type="number" min="0" onwheel="this.blur()" v-model="cash_on_delivery_amount"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <h5 class="text-dark font-weight-bold mb-10">Receiver Details:</h5>

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Name</label>
                            <div class="col-lg-8 col-lg-8">
                                <input class="form-control form-control-solid form-control-lg" name="receiver_name"
                                       type="text" v-model="receiver_name"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Address</label>
                            <div class="col-lg-8 col-lg-8">
                                <input class="form-control form-control-solid form-control-lg" name="receiver_address"
                                       type="text" v-model="receiver_address"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Email</label>
                            <div class="col-lg-8 col-lg-8">
                                <input class="form-control form-control-solid form-control-lg" name="receiver_email"
                                       type="text" v-model="receiver_email"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Phone</label>
                            <div class="col-lg-8 col-lg-8">
                                <input class="form-control form-control-solid form-control-lg" name="receiver_phone"
                                       type="number" min="0" onwheel="this.blur()" v-model="receiver_phone"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Phone (Alternative)</label>
                            <div class="col-lg-8 col-lg-8">
                                <input class="form-control form-control-solid form-control-lg"
                                       name="receiver_phone_alternative" type="number" min="0" onwheel="this.blur()"
                                       v-model="receiver_phone_alternative"/>
                            </div>
                        </div>
                        <!--end::Group-->

                    </div>

                    <div class="border-top pt-10 mt-15">

                        <h5 class="text-dark font-weight-bold mb-10">Order Items <small><a href="#" data-toggle="modal" data-target="#modalAddItem" class="text-danger">(Add Item)</a></small></h5>

                        <!--begin::Group-->
                        <div class="form-group row">
                            <div class="col-lg-12 col-xl-12">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total Weight</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in items">
                                        <td>{{ item.description }}</td>
                                        <td>Kshs {{ item.price }}</td>
                                        <td>{{ item.quantity }}</td>
                                        <td>{{ item.weight }}kg</td>
                                        <td><a href="#" data-toggle="modal" data-target="#modalEditItem" class="text-danger" @click.prevent="itemDetails(item, index)">View</a> | <a href="#" class="text-danger" @click.prevent="itemDelete(index)">Delete</a> </td>
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
                                    data-wizard-type="action-submit">Submit
                            </button>
                            <div class="spinner-border text-primary" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <!--end::Wizard Actions-->
                </div>
            </div>
        </form>
        <!--end::Wizard Form-->

        <!-- Modal-->
        <div class="modal fade" id="modalAddItem" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <form class="form" @submit.prevent="itemSubmit" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Item</h5>
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
                                            <label class="col-4 col-form-label">Use Inventory Product?</label>
                                            <div class="col-4">
                                            <span class="switch">
                                                <label>
                                                    <input type="checkbox" checked="checked" name="select"
                                                           v-model="item_inventory"/>
                                                    <span></span>
                                                </label>
                                            </span>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="item_inventory">
                                            <label class="col-xl-4 col-lg-4 col-form-label">SKU</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="item_sku" type="text" v-model="item_sku"/>
                                                <table class="table" v-if="inventories.length > 0">
                                                    <tbody>
                                                    <tr v-for="inventory in inventories">
                                                        <td><small><a href="#" @click.prevent="selectInventory(inventory)">{{ inventory.sku }} - {{ inventory.name }}</a></small></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Description</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_description" type="text" v-model="item_description" />
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Price</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="item_price" type="number" min="0" onwheel="this.blur()" v-model="item_price"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Quantity <small class="text-danger" v-if="item_inventory == true">(Available {{ item_inventory_quantity }}) </small></label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_quantity" type="number" min="0" onwheel="this.blur()" v-model="item_quantity"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Total Weight</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_weight" type="number" step="0.01" min="0" onwheel="this.blur()" v-model="item_weight"/>
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
                            <button type="submit" class="btn btn-primary font-weight-bold">Add Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="modalEditItem" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="itemEditSubmit" method="post">
                    <div class="modal-content modal-dialog-scrollable">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Item</h5>
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
                                        <div class="form-group row" v-if="item_inventory">
                                            <label class="col-xl-4 col-lg-4 col-form-label">SKU</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="item_sku" type="text" v-model="item_sku"/>
                                                <table class="table" v-if="inventories.length > 0">
                                                    <tbody>
                                                    <tr v-for="inventory in inventories">
                                                        <td><small><a href="#" @click.prevent="selectInventory(inventory)">{{ inventory.sku }} - {{ inventory.name }}</a></small></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <!--end::Group-->
                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Description</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_description" type="text" v-model="item_description" />
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Price</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_price" type="number" min="0" onwheel="this.blur()" v-model="item_price" />
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Quantity <small class="text-danger" v-if="item_inventory == true">(Available {{ item_inventory_quantity }}) </small></label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_quantity" type="number" min="0" onwheel="this.blur()" v-model="item_quantity"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Total Weight</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_weight" type="number" step="0.01" min="0" onwheel="this.blur()" v-model="item_weight"/>
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

        props: {
            merchant_id: String,
        },

        data() {
            return {
                order_no: '',
                cash_on_delivery: false,
                cash_on_delivery_amount: '',
                receiver_name: '',
                receiver_address: '',
                receiver_email: '',
                receiver_phone: '',
                receiver_phone_alternative: '',
                order_status: 'order_pending',

                item_inventory: false,
                item_inventory_product: null,
                item_inventory_id: '',
                item_inventory_quantity: 0,
                item_inventory_amount: 0,
                item_sku: '',
                item_index: '',
                item_description: '',
                item_price: '',
                item_quantity: '',
                item_weight: '',
                items: [],
                inventory_products: [],
                total_weight: 0,
                total_amount: 0,

                inventories: [],

                alert_error: false,
                alert_success: false,
                loader: false,

            }
        },

        methods: {

            searchInventoryProduct() {

                this.item_description = "";
                this.item_price = "";
                this.item_quantity = "";
                this.item_weight = "";
                this.item_inventory_id = "";
                this.item_inventory_quantity = "";
                this.item_inventory_product = null;

                const vm = this;
                const formData = new FormData();
                formData.append('sku', this.item_sku);
                formData.append('merchant_id', this.merchant_id);

                let uri = base_url + `v1/inventory-search`;
                axios.post(uri, formData)
                    .then(function (response) {

                        if(Object.keys(response.data).length === 0) {
                            alert("Product not available");
                        } else {
                            var available_quantity = response.data.quantity;
                            if(available_quantity==0){
                                alert("Inventory low");
                            }
                            vm.item_inventory_product = response.data;
                            vm.item_description = response.data.name;
                            vm.item_inventory_id = response.data.id;
                            vm.item_inventory_quantity = response.data.quantity;

                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });
            },

            selectInventory(inventory) {

                var available_quantity = inventory.quantity;
                if(available_quantity==0){
                    alert("Inventory low");
                }
                this.item_inventory_product = inventory;
                this.item_description = inventory.name;
                this.item_inventory_id = inventory.id;
                this.item_inventory_quantity = inventory.quantity;
                this.item_sku = inventory.sku;

            },

            fetchInventoryDetail(){

                const vm = this;
                let uri = base_url+`v1/inventory-details/`+this.item_inventory_id;
                axios.get(uri).then((response) => {

                    let inventory = response.data;
                    if(inventory){
                        vm.item_inventory_product = inventory;
                        vm.item_inventory_id = inventory.id;
                        vm.item_inventory_amount = inventory.amount;
                        vm.item_inventory_quantity = inventory.quantity;
                    }
                });
            },

            itemSubmit(){

                if (!this.item_description) {
                    alert('Enter description');
                    return
                }

                if (!this.item_quantity) {
                    alert('Enter quantity');
                    return
                }

                var sku = "";
                if(this.item_inventory == true){

                    if (!this.item_inventory_product.sku) {
                        alert('Enter SKU');
                        return
                    }

                    if(this.item_quantity > this.item_inventory_quantity){
                        alert("Quantity exceeds inventory quantity");
                        return;
                    }

                    sku = this.item_inventory_product.sku
                }

                var item = {};
                item["id"] = "";
                item["description"] = this.item_description;
                item["price"] = this.item_price;
                item["quantity"] = this.item_quantity;
                item["weight"] = this.item_weight;
                item["sku"] = this.item_sku;
                item["inventory_id"] = this.item_inventory_id;
                this.items.push(item);

                this.item_description = "";
                this.item_price = "";
                this.item_quantity = "";
                this.item_weight = "";
                this.item_sku = "";
                this.item_inventory = false;
                this.item_inventory_id = "";
                this.item_inventory_amount = "";
                this.item_inventory_quantity = "";
                this.item_inventory_product = null;

                $('#modalAddItem').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();


            },

            itemDetails(item, index){

                this.item_index = index;
                this.item_description = item.description;
                this.item_price = item.price;
                this.item_quantity = item.quantity;
                this.item_weight = item.weight;
                this.item_sku = item.sku;

                if(item.sku != ""){
                    this.item_inventory = true;
                    this.item_inventory_id = item.inventory_id;
                    this.fetchInventoryDetail();
                }
            },

            itemEditSubmit() {

                if (!this.item_description) {
                    alert('Enter description');
                    return
                }

                if (!this.item_quantity) {
                    alert('Enter quantity');
                    return
                }


                if(this.item_inventory == true){

                    if (!this.item_sku) {
                        alert('Enter SKU');
                        return
                    }

                    if(this.item_quantity > this.item_inventory_quantity){
                        alert("Quantity exceeds inventory quantity");
                        return;
                    }
                }

                this.items[this.item_index].description = this.item_description;
                this.items[this.item_index].price = this.item_price;
                this.items[this.item_index].quantity = this.item_quantity;
                this.items[this.item_index].weight = this.item_weight;
                this.items[this.item_index].inventory_id = this.item_inventory_id;

                if(this.item_inventory_product){
                    if(this.item_inventory_product.sku){
                        this.items[this.item_index].sku = this.item_inventory_product.sku;
                    }else{
                        this.items[this.item_index].sku = "";
                    }
                }

                this.item_description = "";
                this.item_price = "";
                this.item_quantity = "";
                this.item_weight = "";
                this.item_sku = "";
                this.item_inventory = false;
                this.item_inventory_id = "";
                this.item_inventory_amount = "";
                this.item_inventory_quantity = "";
                this.item_inventory_product = null;

                $('#modalEditItem').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();



            },

            itemDelete(index){
                this.items.splice(index, 1);

            },

            itemClose(){
                this.item_description = "";
                this.item_price = "";
                this.item_quantity = "";
                this.item_weight = "";
                this.item_sku = "";
                this.item_inventory = false;
                this.item_inventory_id = "";
                this.item_inventory_amount = "";
                this.item_inventory_quantity = "";
                this.item_inventory_product = null;
            },


            formSubmit() {

                this.loader = true;

                var order_no = this.order_no;
                var cash_on_delivery = this.cash_on_delivery;
                var cash_on_delivery_amount = this.cash_on_delivery_amount;
                var merchant_id = this.merchant_id;
                var receiver_name = this.receiver_name;
                var receiver_address = this.receiver_address;
                var receiver_email = this.receiver_email;
                var receiver_phone = this.receiver_phone;
                var receiver_phone_alternative = this.receiver_phone_alternative;
                var total_weight = this.total_weight;

                if(this.items.length == 0){
                    alert('Enter order items');
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append('order_no', order_no);
                formData.append('cash_on_delivery', cash_on_delivery);
                formData.append('cash_on_delivery_amount', cash_on_delivery_amount);
                formData.append('merchant_id', merchant_id);
                formData.append('receiver_name', receiver_name);
                formData.append('receiver_address', receiver_address);
                formData.append('receiver_email', receiver_email);
                formData.append('receiver_phone', receiver_phone);
                formData.append('receiver_phone_alternative', receiver_phone_alternative);
                formData.append('total_weight', total_weight);
                formData.append('items', JSON.stringify(this.items));

                let uri = base_url + `v1/merchant-order-create`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if (status === 1) {
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Record added successfully!');
                            window.location = response.data.redirect;

                        } else if (status === 3){

                            vm.loader = false;
                            vm.alert_error = true;
                            alert(response.data.message);

                        } else {

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
        },

        watch: {
            item_sku: function (val) {
                if(val.length > 2){

                    const vm = this;
                    const formData = new FormData();
                    formData.append('search_query', val);
                    formData.append('merchant_id', this.merchant_id);

                    let uri = base_url + `v1/merchant-inventory-search-name`;
                    axios.post(uri, formData)
                        .then(function (response) {

                            vm.inventories = response.data;

                        })
                        .catch(function (error) {
                            console.log(error);
                        });

                }
            },
        }
    }
</script>
