<template>

    <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
        <div class="tab-content">
            <!--begin::Tab-->
            <div class="tab-pane show active px-7" id="kt_user_edit_tab_1" role="tabpanel">

                <!--begin::Row-->
                <div class="row">
                    <div class="col-xl-2"></div>
                    <div class="col-xl-7 my-2">

                        <div class="alert alert-success" role="alert" v-if="alert_success">Record details updated!</div>
                        <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating record!</div>

                        <!--begin::Row-->
                        <div class="row">
                            <label class="col-3"></label>
                            <div class="col-9">
                                <h6 class="text-dark font-weight-bold mb-10">Outbound Schedule Info:</h6>
                            </div>
                        </div>
                        <!--end::Row-->

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
                            <label class="col-xl-3 col-lg-3 col-form-label">Tax</label>
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


                        <!--begin::Wizard Actions-->
                        <div class="d-flex justify-content-between border-top pt-10 mt-15">
                            <div>
                                <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4" data-wizard-type="action-submit">Edit</button>
                                <button type="button" class="btn btn-danger font-weight-bolder px-9 py-4" data-wizard-type="action-submit" @click.prevent="deleteDetails()">Delete</button>
                                <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Row-->
            </div>
            <!--end::Tab-->

        </div>
    </form>

</template>

<script>
    export default {

        mounted(){
            this.fetchDetails();
            this.fetchTowns();
        },

        props: {
            schedule_id: String,
            causer_id: String,
        },

        data(){
            return{
                charge: '',
                tax: '',
                extra_weight: '',
                total_amount: '',
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

            fetchDetails(){
                let uri = base_url+`v1/schedule-outbound-details/`+this.schedule_id;
                axios.get(uri).then((response) => {

                    let schedule = response.data;
                    if(schedule){
                        this.charge = schedule.charge;
                        this.tax = schedule.tax;
                        this.total_amount = schedule.total_amount;
                        this.from_id = schedule.from;
                        this.destination_id = schedule.destination;
                        this.extra_weight = schedule.extra_weight;
                    }
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
                formData.append( 'id', this.schedule_id);
                formData.append( 'from', from);
                formData.append( 'destination', destination);
                formData.append( 'charge', charge);
                formData.append( 'tax', tax);
                formData.append( 'total_amount', total_amount);
                formData.append( 'extra_weight', extra_weight);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/schedule-outbound-edit`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Record updated successfully!');

                        }else{

                            alert('Error updating record!');
                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        alert('Error updating record!');
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },

            deleteDetails(){

                this.loader = true;

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.schedule_id);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/schedule-outbound-delete`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Record deleted successfully!');
                            window.location = response.data.redirect;

                        }else{

                            alert('Error deleting record!');
                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        alert('Error deleting record!');
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            }
        }
    }
</script>
