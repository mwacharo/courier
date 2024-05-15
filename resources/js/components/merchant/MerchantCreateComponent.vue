<template>
    <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <!--begin::Wizard Step 1-->
                <div class="alert alert-success" role="alert" v-if="alert_success">Record added successfully!</div>
                <div class="alert alert-danger" role="alert" v-if="alert_error">Error adding record!</div>

                <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                    <h5 class="text-dark font-weight-bold mb-10">New Merchant Details:</h5>

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Merchant Prefix</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="merchant_prefix" type="text"
                                   v-model="merchant_prefix"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Name</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="first_name" type="text"
                                   v-model="name"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Merchant Type</label>
                        <div class="col-lg-9 col-xl-9">
                            <select class="form-control form-control-solid form-control-lg" name="name" type="text"
                                    v-model="merchant_type">
                                <option value="">Select Merchant Type</option>
                                <option value="0">Individual</option>
                                <option value="1">Company</option>
                            </select>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Address</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="address"
                                   type="text" v-model="address"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Phone Number</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="phone_number"
                                   type="number" min="0" onwheel="this.blur()" v-model="phone_number"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Email</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="email" type="email"
                                   v-model="email"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-3 col-form-label">Enable Cash On Delivery</label>
                        <div class="col-3">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="enable_cash_on_delivery_fee"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row" v-if="enable_cash_on_delivery_fee">
                        <label class="col-xl-3 col-lg-3 col-form-label">Cash On Delivery Amount</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="cash_on_delivery_fee"
                                   type="number" min="0" step=".01" onwheel="this.blur()" v-model="cash_on_delivery_fee"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-3 col-form-label">Enable Delivery Fee Nairobi</label>
                        <div class="col-3">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="enable_delivery_fee_nairobi"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row" v-if="enable_delivery_fee_nairobi">
                        <label class="col-xl-3 col-lg-3 col-form-label">Delivery Fee Nairobi Amount</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="delivery_fee_nairobi"
                                   type="number" min="0" onwheel="this.blur()" v-model="delivery_fee_nairobi"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-3 col-form-label">Enable Delivery Fee Outbound</label>
                        <div class="col-3">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="enable_delivery_fee_outbound"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row" v-if="enable_delivery_fee_outbound">
                        <label class="col-xl-3 col-lg-3 col-form-label">Delivery Fee Outbound Amount</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="delivery_fee_outbound"
                                   type="number" min="0" onwheel="this.blur()" v-model="delivery_fee_outbound"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-3 col-form-label">Enable Returns Management Fee</label>
                        <div class="col-3">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="enable_returns_management_fee"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-3 col-form-label">Enable Warehousing Fee</label>
                        <div class="col-3">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="enable_warehousing_fee"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row" v-if="enable_warehousing_fee">
                        <label class="col-xl-3 col-lg-3 col-form-label">Warehousing Fee Amount</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="warehousing_fee"
                                   type="number" min="0" onwheel="this.blur()" v-model="warehousing_fee"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-3 col-form-label">Enable Packaging Fee</label>
                        <div class="col-3">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="enable_packaging_fee"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row" v-if="enable_packaging_fee">
                        <label class="col-xl-3 col-lg-3 col-form-label">Packaging Fee Amount</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="packaging_fee"
                                   type="number" min="0" onwheel="this.blur()" v-model="packaging_fee"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-3 col-form-label">Enable Call Centre Fee</label>
                        <div class="col-3">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="enable_call_centre_fee"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row" v-if="enable_call_centre_fee">
                        <label class="col-xl-3 col-lg-3 col-form-label">Call Centre Fee Amount</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="call_centre_fee"
                                   type="number" min="0" onwheel="this.blur()" v-model="call_centre_fee"/>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-3 col-form-label">Enable Label Printing Fee</label>
                        <div class="col-3">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="enable_label_printing_fee"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row" v-if="enable_label_printing_fee">
                        <label class="col-xl-3 col-lg-3 col-form-label">Label Printing Fee Amount</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="label_printing_fee"
                                   type="number" min="0" onwheel="this.blur()" v-model="label_printing_fee"/>
                        </div>
                    </div>
                    <!--end::Group-->


                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Contract(PDF)</label>
                        <div class="col-lg-9 col-xl-9">
                            <input id="contract" class="form-control form-control-solid form-control-lg" name="contract" type="file">
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Country</label>
                        <div class="col-lg-9 col-xl-9">
                            <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="country_id">
                                <option value="">Select Country</option>
                                <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
                            </select>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Branch</label>
                        <div class="col-lg-9 col-xl-9">
                            <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="town_id">
                                <option value="">Select Branch</option>
                                <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                            </select>
                        </div>
                    </div>
                    <!--end::Group-->

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Agent</label>
                        <div class="col-lg-9 col-xl-9">
                            <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="admin_id">
                                <option value="">Select Agent</option>
                                <option v-for="admin in admins" :value="admin.id">{{ admin.first_name }} {{ admin.last_name }}</option>
                            </select>
                        </div>
                    </div>
                    <!--end::Group-->

                    <div class="form-group row">
                        <label class="col-3 col-form-label">Enabled</label>
                        <div class="col-3">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="enabled"/>
                                    <span></span>
                                </label>
                            </span>
                        </div>
                    </div>
                    <!--end::Group-->

                    <div class="d-flex justify-content-between border-top pt-10 mt-15">
                        <div>
                            <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4"
                                    data-wizard-type="action-submit">Submit
                            </button>
                            <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
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
            this.fetchCountries();
            this.fetchBranches();
            this.fetchAdmins();
        },

        props: {
            causer_id: String,
        },

        data() {
            return {
                merchant_prefix: '',
                name: '',
                merchant_type: '',
                address: '',
                phone_number: '',
                email: '',
                enable_cash_on_delivery_fee: false,
                cash_on_delivery_fee: '',
                enable_delivery_fee_nairobi: false,
                delivery_fee_nairobi: '',
                enable_delivery_fee_outbound: false,
                delivery_fee_outbound: '',
                enable_returns_management_fee: false,
                enable_warehousing_fee: false,
                warehousing_fee: '',
                enable_packaging_fee: false,
                packaging_fee: '',
                enable_call_centre_fee: false,
                call_centre_fee: '',
                enable_label_printing_fee: false,
                label_printing_fee: '',
                enabled: false,
                country_id: '',
                countries: [],
                town_id: '',
                admin_id: '',
                towns: [],
                admins: [],

                alert_error: false,
                alert_success: false,
                loader: false,

            }
        },

        methods: {


            fetchCountries(){
                let uri = base_url+`v1/country-list`;
                axios.get(uri).then((response) => {
                    this.countries = response.data;
                });
            },

            fetchAdmins(){
                let uri = base_url+`v1/admin-list`;
                axios.get(uri).then((response) => {
                    this.admins = response.data;
                });
            },

            fetchBranches(){
                let uri = base_url+`v1/town-list`;
                axios.get(uri).then((response) => {
                    this.towns = response.data;
                });
            },

            formSubmit() {

                this.loader = true;

                var merchant_prefix = this.merchant_prefix;
                if (!merchant_prefix) {
                    alert('Enter merchant prefix');
                    this.loader = false;
                    return
                }

                var name = this.name;
                if (!name) {
                    alert('Enter name');
                    this.loader = false;
                    return
                }

                var merchant_type = this.merchant_type;
                if (!merchant_type) {
                    alert('Enter merchant type');
                    this.loader = false;
                    return
                }

                var address = this.address;
                if (!address) {
                    alert('Enter email');
                    this.loader = false;
                    return
                }

                var email = this.email;
                if (!email) {
                    alert('Enter email');
                    this.loader = false;
                    return
                }

                var phone_number = this.phone_number;
                if (!phone_number) {
                    alert('Enter phone number');
                    this.loader = false;
                    return
                }

                var country_id = this.country_id;
                if (!country_id) {
                    alert('Select Country');
                    this.loader = false;
                    return
                }

                var town_id = this.town_id;
                if (!town_id) {
                    alert('Select Town');
                    this.loader = false;
                    return
                }

                var enable_cash_on_delivery_fee = this.enable_cash_on_delivery_fee;
                var cash_on_delivery_fee = this.cash_on_delivery_fee;
                if(enable_cash_on_delivery_fee == true){
                    if (!cash_on_delivery_fee) {
                        alert('Enter cash on delivery');
                        this.loader = false;
                        return
                    }
                }

                var enable_delivery_fee_nairobi = this.enable_delivery_fee_nairobi;
                var delivery_fee_nairobi = this.delivery_fee_nairobi;
                if(enable_delivery_fee_nairobi == true){
                    if (!delivery_fee_nairobi) {
                        alert('Enter delivery fee nairobi');
                        this.loader = false;
                        return
                    }
                }

                var enable_delivery_fee_outbound = this.enable_delivery_fee_outbound;
                var delivery_fee_outbound = this.delivery_fee_outbound;
                if(enable_delivery_fee_outbound == true){
                    if (!delivery_fee_outbound) {
                        alert('Enter delivery fee outbound');
                        this.loader = false;
                        return
                    }
                }

                var enable_returns_management_fee = this.enable_returns_management_fee;

                var enable_warehousing_fee = this.enable_warehousing_fee;
                var warehousing_fee = this.warehousing_fee;
                if(enable_warehousing_fee == true){
                    if (!warehousing_fee) {
                        alert('Enter warehousing fee');
                        this.loader = false;
                        return
                    }
                }

                var enable_packaging_fee = this.enable_packaging_fee;
                var packaging_fee = this.packaging_fee;
                if(enable_packaging_fee == true){
                    if (!packaging_fee) {
                        alert('Enter packaging fee');
                        this.loader = false;
                        return
                    }
                }

                var enable_call_centre_fee = this.enable_call_centre_fee;
                var call_centre_fee = this.call_centre_fee;
                if(enable_call_centre_fee == true){
                    if (!call_centre_fee) {
                        alert('Enter call centre fee');
                        this.loader = false;
                        return
                    }
                }

                var enable_label_printing_fee = this.enable_label_printing_fee;
                var label_printing_fee = this.label_printing_fee;
                if(enable_label_printing_fee == true){
                    if (!label_printing_fee) {
                        alert('Enter label printing fee');
                        this.loader = false;
                        return
                    }
                }

                var admin_id = this.admin_id;
                var enabled = this.enabled;

                const vm = this;
                const formData = new FormData();
                const contract = document.querySelector( '#contract' );
                formData.append( 'contract', contract.files[0] );
                formData.append('merchant_prefix', merchant_prefix);
                formData.append('name', name);
                formData.append('merchant_type', merchant_type);
                formData.append('address', address);
                formData.append('email', email);
                formData.append('phone_number', phone_number);
                formData.append('enable_cash_on_delivery_fee', enable_cash_on_delivery_fee);
                formData.append('cash_on_delivery_fee', cash_on_delivery_fee);
                formData.append('enable_delivery_fee_nairobi', enable_delivery_fee_nairobi);
                formData.append('delivery_fee_nairobi', delivery_fee_nairobi);
                formData.append('enable_delivery_fee_outbound', enable_delivery_fee_outbound);
                formData.append('delivery_fee_outbound', delivery_fee_outbound);
                formData.append('enable_returns_management_fee', enable_returns_management_fee);
                formData.append('enable_warehousing_fee', enable_warehousing_fee);
                formData.append('warehousing_fee', warehousing_fee);
                formData.append('enable_packaging_fee', enable_packaging_fee);
                formData.append('packaging_fee', packaging_fee);
                formData.append('enable_call_centre_fee', enable_call_centre_fee);
                formData.append('call_centre_fee', call_centre_fee);
                formData.append('enable_label_printing_fee', enable_label_printing_fee);
                formData.append('label_printing_fee', label_printing_fee);
                formData.append('enabled', enabled);
                formData.append( 'country_id', country_id);
                formData.append( 'town_id', town_id);
                formData.append( 'admin_id', admin_id);
                formData.append('causer_id', this.causer_id);

                let uri = base_url + `v1/merchant-create`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if (status === 1) {
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Record added successfully!');
                            window.location = response.data.redirect;

                        } else {

                            alert('Error adding record!');
                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);

                        alert('Error adding record!');
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },
        }
    }

</script>
