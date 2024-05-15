<template>
    <div class="col-xl-12 col-xxl-10">
        <!--begin::Wizard Form-->
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!--begin::Wizard Step 1-->

                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                        <h5 class="text-dark font-weight-bold mb-10">Order Details:</h5>

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Destination Type</label>
                            <div class="col-lg-8 col-lg-8">
                                <select class="form-control form-control-solid form-control-lg" name="destination_type"
                                        type="text" v-model="destination_type" >
                                    <option value="1">Outbound Delivery (Out of Nairobi)</option>
                                    <option value="2">Inbound Delivery</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Service Type</label>
                            <div class="col-lg-8 col-lg-8">
                                <select class="form-control form-control-solid form-control-lg" name="service_type"
                                        type="text" v-model="service_type" >
                                    <option value="">Select Service</option>
                                    <option value="1" v-if="destination_type==2">Same-Day Delivery</option>
                                    <option value="2">Scheduled Delivery</option>
                                    <option value="3">Overnight Delivery</option>
                                    <option value="4">Pickup Delivery</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row" v-if="destination_type==2">
                            <label class="col-xl-4 col-lg-4 col-form-label">Inbound Rate Type</label>
                            <div class="col-lg-8 col-lg-8">
                                <select class="form-control form-control-solid form-control-lg" name="inbound_rate_type"
                                        type="text" v-model="inbound_rate_type">
                                    <option value="">Select Inbound Rate</option>
                                    <option value="1">On-demand delivery charges</option>
                                    <option value="2">Zone delivery charges</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row" v-if="inbound_rate_type==1">
                            <label class="col-xl-4 col-lg-4 col-form-label">Approximate Distance (KM)</label>
                            <div class="col-lg-8 col-lg-8">
                                <input class="form-control form-control-solid form-control-lg" name="delivery_distance"
                                       type="number" min="0" onwheel="this.blur()" v-model="delivery_distance" />
                            </div>
                        </div>
                        <!--end::Group-->

                        <h5 class="text-dark font-weight-bold mb-10" v-if="destination_type==1">Receiver Details:</h5>

                        <!--begin::Group-->
                        <div class="form-group row" v-if="destination_type==1">
                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Country</label>
                            <div class="col-lg-8 col-lg-8">
                                <select class="form-control form-control-solid form-control-lg" name="name" type="text"
                                        v-model="receiver_country">
                                    <option value="">Select Country</option>
                                    <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row" v-if="destination_type==1">
                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Town</label>
                            <div class="col-lg-8 col-lg-8">
                                <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="receiver_town" >
                                    <option value="">Select Town</option>
                                    <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row" v-if="destination_type==2 && inbound_rate_type==2">
                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Zone</label>
                            <div class="col-lg-8 col-lg-8">
                                <select class="form-control form-control-solid form-control-lg" name="zone_id"
                                        type="text" v-model="zone_id" >
                                    <option value="">Select Zone</option>
                                    <option v-for="zone in zones" :value="zone.id">{{ zone.zone }}</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-4 col-lg-4 col-form-label">Approximate Weight</label>
                            <div class="col-lg-8 col-lg-8">
                                <input class="form-control form-control-solid form-control-lg" name="total_weight" type="number" min="0" onwheel="this.blur()" v-model="total_weight"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-4 col-form-label">Insurance?</label>
                            <div class="col-4">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select"
                                           v-model="insurance"/>
                                    <span></span>
                                </label>
                            </span>
                            </div>
                        </div>
                        <!--end::Group-->

                    </div>

                    <div class="border-top pt-10 mt-15" v-if="calculated">
                        <!--begin::Group-->
                        <div class="form-group row">
                            <div class="col-lg-12 col-xl-12">
                                <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
                                    <div class="col-md-11">
                                        <div class="d-flex justify-content-between flex-column flex-md-row font-size-lg">
                                            <div class="d-flex flex-column mb-10 mb-md-0">
                                                <div class="font-weight-bolder font-size-lg mb-3">ORDER SUMMARY</div>
                                                <div class="d-flex justify-content-between mb-3">
                                                    <span class="mr-15 font-weight-bold">Total Weight:</span>
                                                    <span class="text-right">{{ total_weight }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column text-md-right">
                                                <span class="font-size-lg font-weight-bolder mb-1">TOTAL AMOUNT</span>
                                                <span class="font-size-h2 font-weight-boldest text-danger mb-1">Kshs {{ total_amount }}</span>
                                                <span>Taxes Included</span>
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
                                    data-wizard-type="action-submit">Calculate
                            </button>
                            <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
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
            this.fetchCountries();
            this.fetchTowns();
            this.fetchZones();
        },

        props: {
            merchant_id: String,
        },

        data() {
            return {
                order_no: '',
                destination_type: '1',
                delivery_distance: '',
                service_type: '',
                inbound_rate_type: '',
                is_sender_merchant: false,
                merchant: '',
                merchant_id: '',
                sender_country: '',
                sender_town: '',
                receiver_country: '',
                receiver_town: '',
                total_weight: '0',
                insurance: false,
                calculated: false,
                loader: false,

                countries: [],
                towns: [],
                merchants: [],
                zones: [],

            }
        },

        methods: {

            fetchCountries() {
                let uri = base_url + `v1/country-list`;
                axios.get(uri).then((response) => {
                    this.countries = response.data;
                });
            },

            fetchTowns() {
                let uri = base_url + `v1/town-list`;
                axios.get(uri).then((response) => {
                    this.towns = response.data;
                });
            },

            fetchZones() {
                let uri = base_url + `v1/zone-list`;
                axios.get(uri).then((response) => {
                    this.zones = response.data;
                });
            },


            formSubmit(){

                this.loader = true;
                this.calculated = false;
                this.total_amount = 0;

                const vm = this;
                var total_weight = this.total_weight;

                if(this.destination_type == '1'){

                        if(vm.receiver_town != ''){

                            const formData = new FormData();
                            formData.append('merchant_id', vm.merchant_id);
                            formData.append('destination', vm.receiver_town);
                            formData.append('weight', total_weight);
                            formData.append('insurance', vm.insurance);
                            let uri = base_url + `v1/merchant-outbound-delivery-charge-calculator`;
                            axios.post(uri, formData)
                                .then(function (response) {

                                    vm.loader = false;
                                    var status = response.data.success;
                                    if (status === 1) {
                                        var amount = response.data.amount;
                                        vm.total_amount = amount;
                                        vm.calculated = true;
                                    }else{
                                        vm.total_amount = 0;
                                        vm.calculated = true;
                                    }

                                })
                                .catch(function (error) {
                                    console.log(error);
                                    vm.loader = false;
                                });

                        }


                }else if(this.destination_type == 2){

                    if(vm.service_type == 1){

                        if(vm.inbound_rate_type == 1){
                            // On demand delivery charges

                            if(vm.delivery_distance != ''){

                                const formData = new FormData();
                                formData.append('merchant_id', vm.merchant_id);
                                formData.append('service_type', vm.service_type);
                                formData.append('inbound_rate_type', vm.inbound_rate_type);
                                formData.append('delivery_distance', vm.delivery_distance);
                                formData.append('weight', total_weight);
                                formData.append('insurance', vm.insurance);
                                let uri = base_url + `v1/merchant-inbound-delivery-charge-calculator`;
                                axios.post(uri, formData)
                                    .then(function (response) {

                                        vm.loader = false;
                                        var status = response.data.success;
                                        if (status === 1) {
                                            var amount = response.data.amount;
                                            vm.total_amount = amount;
                                            vm.calculated = true;
                                        }else{
                                            vm.total_amount = 0;
                                            vm.calculated = true;
                                        }

                                    })
                                    .catch(function (error) {
                                        console.log(error);
                                        vm.loader = false;
                                    });

                            }

                        }else if(vm.inbound_rate_type == 2){
                            // Zone bound delivery charges

                            if(vm.zone_id != ''){

                                const formData = new FormData();
                                formData.append('merchant_id', vm.merchant_id);
                                formData.append('service_type', vm.service_type);
                                formData.append('inbound_rate_type', vm.inbound_rate_type);
                                formData.append('zone', vm.zone_id);
                                formData.append('weight', total_weight);
                                formData.append('insurance', vm.insurance);
                                let uri = base_url + `v1/merchant-inbound-delivery-charge-calculator`;
                                axios.post(uri, formData)
                                    .then(function (response) {

                                        vm.loader = false;
                                        var status = response.data.success;
                                        if (status === 1) {
                                            var amount = response.data.amount;
                                            vm.total_amount = amount;
                                            vm.calculated = true;
                                        }else{
                                            vm.total_amount = 0;
                                            vm.calculated = true;
                                        }

                                    })
                                    .catch(function (error) {
                                        console.log(error);
                                        vm.loader = false;
                                    });
                            }
                        }

                    }else{

                        if(vm.inbound_rate_type == 1){
                            // On demand delivery charges

                            if(vm.delivery_distance != ''){

                                const formData = new FormData();
                                formData.append('merchant_id', vm.merchant_id);
                                formData.append('service_type', vm.service_type);
                                formData.append('inbound_rate_type', vm.inbound_rate_type);
                                formData.append('delivery_distance', vm.delivery_distance);
                                formData.append('weight', total_weight);
                                formData.append('insurance', vm.insurance);
                                let uri = base_url + `v1/merchant-inbound-delivery-charge-calculator`;
                                axios.post(uri, formData)
                                    .then(function (response) {

                                        vm.loader = false;
                                        var status = response.data.success;
                                        if (status === 1) {
                                            var amount = response.data.amount;
                                            vm.total_amount = amount;
                                            vm.calculated = true;
                                        }else{
                                            vm.total_amount = 0;
                                            vm.calculated = true;
                                        }

                                    })
                                    .catch(function (error) {
                                        console.log(error);
                                        vm.loader = false;
                                    });
                            }

                        }else if(vm.inbound_rate_type == 2){
                            // Zone bound delivery charges

                            if(vm.zone_id != ''){

                                const formData = new FormData();
                                formData.append('merchant_id', vm.merchant_id);
                                formData.append('service_type', vm.service_type);
                                formData.append('inbound_rate_type', vm.inbound_rate_type);
                                formData.append('zone', vm.zone_id);
                                formData.append('weight', total_weight);
                                formData.append('insurance', vm.insurance);
                                let uri = base_url + `v1/merchant-inbound-delivery-charge-calculator`;
                                axios.post(uri, formData)
                                    .then(function (response) {

                                        vm.loader = false;
                                        var status = response.data.success;
                                        if (status === 1) {
                                            var amount = response.data.amount;
                                            vm.total_amount = amount;
                                            vm.calculated = true;
                                        }else{
                                            vm.total_amount = 0;
                                            vm.calculated = true;
                                        }

                                    })
                                    .catch(function (error) {
                                        console.log(error);
                                        vm.loader = false;
                                    });
                            }
                        }
                    }
                }

            },

        }
    }
</script>
