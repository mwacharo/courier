<template>
    <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <!--begin::Wizard Step 1-->
                <div class="alert alert-success" role="alert" v-if="alert_success">Record added successfully!</div>
                <div class="alert alert-danger" role="alert" v-if="alert_error">Error adding record!</div>

                <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                    <h5 class="text-dark font-weight-bold mb-10">Inventory Details:</h5>

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
                        <label class="col-xl-3 col-lg-3 col-form-label">Quantity</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" quantity="quantity" type="text" v-model="quantity"/>
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

        props: {
            admin_id: String,
        },

        data(){
            return{
                quantity: '',
                sku: '',

                alert_error:false,
                alert_success:false,
                loader:false,

            }
        },

        methods: {

            formSubmit(){

                this.loader = true;

                var sku = this.sku;
                if (!sku) {
                    alert('Enter SKU');
                    this.loader = false;
                    return
                }

                var quantity = this.quantity;
                if (!quantity) {
                    alert('Enter quantity');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'quantity', quantity);
                formData.append( 'transaction_type', 1);
                formData.append( 'admin_id', this.admin_id);
                formData.append( 'sku', sku);

                let uri = base_url+`v1/inventory-outscan-create`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Record added successfully!');
                            window.location = response.data.redirect;

                        }else{

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
