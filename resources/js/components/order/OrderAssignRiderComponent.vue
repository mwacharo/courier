<template>
    <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <!--begin::Wizard Step 1-->
                <div class="alert alert-success" role="alert" v-if="alert_success">Rider successfully assigned to order!</div>
                <div class="alert alert-danger" role="alert" v-if="alert_error">Error assigning rider!</div>

                <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                    <h5 class="text-dark font-weight-bold mb-10">Rider Details:</h5>

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Rider</label>
                        <div class="col-lg-9 col-xl-9">
                            <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="rider_id">
                                <option value="">Select Rider</option>
                                <option v-for="rider in riders" :value="rider.id">{{ rider.first_name }} {{ rider.last_name }}</option>
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
            this.fetchRiders();
            this.fetchDetails();
        },

        props: {
            order_id: String,
        },

        data(){
            return{
                rider_id: '',
                riders: [],

                alert_error:false,
                alert_success:false,
                loader:false,

            }
        },

        methods: {

            fetchRiders(){
                let uri = base_url+`v1/rider-list`;
                axios.get(uri).then((response) => {
                    this.riders = response.data;
                });
            },

            formSubmit(){

                this.loader = true;

                var rider_id = this.rider_id;
                if (!rider_id) {
                    alert('Select rider');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append( 'rider_id', rider_id);

                let uri = base_url+`v1/order-assign-rider`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Rider assigned to order!');
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

            fetchDetails(){

                const vm = this;
                let uri = base_url+`v1/order-details/`+this.order_id;
                axios.get(uri).then((response) => {

                    let order = response.data;
                    if(order){

                        this.rider_id = order.rider_id;

                    }
                });
            },
        }
    }
</script>
