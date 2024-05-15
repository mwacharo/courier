<template>
    <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <!--begin::Wizard Step 1-->
                <div class="alert alert-success" role="alert" v-if="alert_success">Record added successfully!</div>
                <div class="alert alert-danger" role="alert" v-if="alert_error">Error adding record!</div>

                <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                    <h5 class="text-dark font-weight-bold mb-10">New Inventory Details:</h5>

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">SKU</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="sku" type="text" v-model="sku"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Name</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="name"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Description</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="description" type="text" v-model="description"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Amount</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="amount" type="number" min="0" onwheel="this.blur()" v-model="amount"/>
                        </div>
                    </div>
                    <!--end::Group-->


                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Quantity</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" quantity="quantity" type="text" v-model="quantity"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Low Count</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="low_count" type="number" min="0" onwheel="this.blur()" v-model="low_count"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Image</label>
                        <div class="col-lg-9 col-xl-9">
                            <input id="image" class="form-control form-control-solid form-control-lg" name="file" type="file">
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Merchant</label>
                        <div class="col-lg-9 col-xl-9">
                            <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="merchant_id">
                                <option value="">Select Merchant</option>
                                <option v-for="merchant in merchants" :value="merchant.id">{{ merchant.name }}</option>
                            </select>
                        </div>
                    </div>
                    <!--end::Group-->

                </div>

                <div class="d-flex justify-content-between border-top pt-10 mt-15">
                    <div>
                        <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4" data-wizard-type="action-submit">Submit</button>
                        <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <!--end::Wizard Actions-->
            </div>
        </div>
    </form>
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
                sku: '',
                barcode: '',
                name: '',
                description: '',
                quantity: '',
                low_count: '',
                amount: '',
                merchant_id: '',
                merchants: [],

                alert_error:false,
                alert_success:false,
                loader:false,

            }
        },

        methods: {

            fetchMerchants(){
                let uri = base_url+`v1/merchant-list`;
                axios.get(uri).then((response) => {
                    this.merchants = response.data;
                });
            },

            formSubmit(){

                this.loader = true;

                var sku = this.sku;
                if (!sku) {
                    alert('Enter sku');
                    this.loader = false;
                    return
                }

                var barcode = this.barcode;

                var name = this.name;
                if (!name) {
                    alert('Enter name');
                    this.loader = false;
                    return
                }

                var description = this.description;
                if (!description) {
                    alert('Enter description');
                    this.loader = false;
                    return
                }

                var amount = this.amount;
                var low_count = this.low_count;

                var quantity = this.quantity;
                if (!quantity) {
                    alert('Enter quantity');
                    this.loader = false;
                    return
                }


                var merchant_id = this.merchant_id;
                if (!merchant_id) {
                    alert('Select Country');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                const image = document.querySelector( '#image' );
                formData.append( 'image', image.files[0] );
                formData.append( 'sku', sku);
                formData.append( 'barcode', barcode);
                formData.append( 'name', name);
                formData.append( 'description', description);
                formData.append( 'amount', amount);
                formData.append( 'quantity', quantity);
                formData.append( 'low_count', low_count);
                formData.append( 'merchant_id', merchant_id);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/inventory-create`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Record added successfully!');
                            window.location = response.data.redirect;

                        }else{
                            alert(response.data.message);
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
