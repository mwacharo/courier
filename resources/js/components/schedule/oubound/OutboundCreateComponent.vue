<template>
    <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <!--begin::Wizard Step 1-->
                <div class="alert alert-success" role="alert" v-if="alert_success">Record added successfully!</div>
                <div class="alert alert-danger" role="alert" v-if="alert_error">Error adding record!</div>

                <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                    <h5 class="text-dark font-w eight-bold mb-10">New Outbound Schedule Details:</h5>

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">From</label>
                        <div class="col-lg-9 col-xl-9">
                            <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="from_id">
                                <option value="">Select From</option>
                                <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                            </select>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Destination</label>
                        <div class="col-lg-9 col-xl-9">
                            <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="destination_id">
                                <option value="">Select Destination</option>
                                <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                            </select>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Charge</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="charge" type="number" min="0" onwheel="this.blur()" v-model="charge"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Tax in %</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="tax" type="number" min="0" onwheel="this.blur()" v-model="tax"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Total Amount (Auto calculated)</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="total_amount" type="number" min="0" onwheel="this.blur()" v-model="total_amount" disabled/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Extra Weight</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="extra_weight" type="number" min="0" onwheel="this.blur()" v-model="extra_weight"/>
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
            this.fetchTowns();
        },

        props: {
            causer_id: String,
        },


        data(){
            return{
                charge: '',
                tax: '',
                total_amount: '',
                extra_weight: '',
                from_id: '',
                destination_id: '',
                towns: [],

                alert_error:false,
                alert_success:false,
                loader:false,

            }
        },

        methods: {

            fetchTowns(){
                let uri = base_url+`v1/town-list`;
                axios.get(uri).then((response) => {
                    this.towns = response.data;
                });
            },

            formSubmit(){

                this.loader = true;

                var from = this.from_id;
                if (!from) {
                    alert('Select from');
                    this.loader = false;
                    return
                }

                var destination = this.destination_id;
                if (!destination) {
                    alert('Select destination');
                    this.loader = false;
                    return
                }

                var charge = this.charge;
                if (!charge) {
                    alert('Enter charge');
                    this.loader = false;
                    return
                }

                var tax = this.tax;
                if (!tax) {
                    alert('Enter tax');
                    this.loader = false;
                    return
                }

                var tax_charge = (tax/100) * parseFloat(charge);
                var total_amount = parseFloat(charge) + parseFloat(tax_charge);

                var extra_weight = this.extra_weight;
                if (!extra_weight) {
                    alert('Enter extra weight');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'from', from);
                formData.append( 'destination', destination);
                formData.append( 'charge', charge);
                formData.append( 'tax', tax);
                formData.append( 'total_amount', total_amount);
                formData.append( 'extra_weight', extra_weight);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/schedule-outbound-create`;
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
