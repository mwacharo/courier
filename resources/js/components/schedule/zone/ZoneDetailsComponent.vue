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
                                <h6 class="text-dark font-weight-bold mb-10">Zone Info:</h6>
                            </div>
                        </div>
                        <!--end::Row-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Name</label>
                            <div class="col-lg-8 col-xl-8">
                                <input class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="zone"/>
                            </div>
                        </div>

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Charge (Same Day Delivery)</label>
                            <div class="col-lg-8 col-xl-8">
                                <input class="form-control form-control-solid form-control-lg" name="same_day_charge" type="number" min="0" onwheel="this.blur()" v-model="same_day_charge"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Tax (Same Day Delivery)</label>
                            <div class="col-lg-8 col-xl-8">
                                <input class="form-control form-control-solid form-control-lg" name="same_day_tax" type="number" min="0" onwheel="this.blur()" v-model="same_day_tax"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Total Amount (Same Day Delivery)</label>
                            <div class="col-lg-8 col-xl-8">
                                <input class="form-control form-control-solid form-control-lg" name="same_day_total_amount" type="number" min="0" onwheel="this.blur()" v-model="same_day_total_amount"/>
                            </div>
                        </div>
                        <!--end::Group-->
                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Charge (Overnight)</label>
                            <div class="col-lg-8 col-xl-8">
                                <input class="form-control form-control-solid form-control-lg" name="overnight_charge" type="number" min="0" onwheel="this.blur()" v-model="overnight_charge"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Tax (Overnight)</label>
                            <div class="col-lg-8 col-xl-8">
                                <input class="form-control form-control-solid form-control-lg" name="overnight_tax" type="number" min="0" onwheel="this.blur()" v-model="overnight_tax"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Total Amount (Overnight)</label>
                            <div class="col-lg-8 col-xl-8">
                                <input class="form-control form-control-solid form-control-lg" name="overnight_total_amount" type="number" min="0" onwheel="this.blur()" v-model="overnight_total_amount"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Extra Weight</label>
                            <div class="col-lg-8 col-xl-8">
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
        },

        props: {
            zone_id: String,
            causer_id: String,
        },

        data(){
            return{
                same_day_charge: '',
                same_day_tax: '',
                same_day_total_amount: '',
                overnight_charge: '',
                overnight_tax: '',
                overnight_total_amount: '',
                extra_weight: '',
                zone: '',

                alert_error:false,
                alert_success:false,
                loader:false,

            }
        },

        methods: {

            fetchDetails(){
                let uri = base_url+`v1/zone-details/`+this.zone_id;
                axios.get(uri).then((response) => {

                    let schedule = response.data;
                    if(schedule){
                        this.same_day_charge = schedule.same_day_charge;
                        this.same_day_tax = schedule.same_day_tax;
                        this.same_day_total_amount = schedule.same_day_total_amount;
                        this.overnight_charge = schedule.overnight_charge;
                        this.overnight_tax = schedule.overnight_tax;
                        this.overnight_total_amount = schedule.overnight_total_amount;
                        this.zone = schedule.zone;
                        this.extra_weight = schedule.extra_weight;
                    }
                });
            },

            formSubmit(){

                this.loader = true;

                var zone = this.zone;
                if (!zone) {
                    alert('Enter zone');
                    this.loader = false;
                    return
                }


                var extra_weight = this.extra_weight;
                if (!extra_weight) {
                    alert('Enter extra weight');
                    this.loader = false;
                    return
                }

                var same_day_charge = this.same_day_charge;
                if (!same_day_charge) {
                    alert('Enter same_day_charge');
                    this.loader = false;
                    return
                }


                var same_day_tax = this.same_day_tax;
                if (!same_day_tax) {
                    alert('Enter same_day_tax');
                    this.loader = false;
                    return
                }

                var same_day_tax_charge = (same_day_tax/100) * parseFloat(same_day_charge);
                var same_day_total_amount = parseFloat(same_day_charge) + parseFloat(same_day_tax_charge);

                var overnight_charge = this.overnight_charge;
                if (!overnight_charge) {
                    alert('Enter overnight_charge');
                    this.loader = false;
                    return
                }


                var overnight_tax = this.overnight_tax;
                if (!overnight_tax) {
                    alert('Enter overnight_tax');
                    this.loader = false;
                    return
                }

                var overnight_tax_charge = (overnight_tax/100) * parseFloat(overnight_charge);
                var overnight_total_amount = parseFloat(overnight_charge) + parseFloat(overnight_tax_charge);


                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.zone_id);
                formData.append( 'zone', zone);
                formData.append( 'same_day_charge', same_day_charge);
                formData.append( 'same_day_tax', same_day_tax);
                formData.append( 'same_day_total_amount', same_day_total_amount);
                formData.append( 'overnight_charge', overnight_charge);
                formData.append( 'overnight_tax', overnight_tax);
                formData.append( 'overnight_total_amount', overnight_total_amount);
                formData.append( 'extra_weight', extra_weight);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/zone-edit`;
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
                formData.append( 'id', this.zone_id);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/zone-delete`;
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
