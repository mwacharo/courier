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
                                <h6 class="text-dark font-weight-bold mb-10">Town Info:</h6>
                            </div>
                        </div>
                        <!--end::Row-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-form-label col-3 text-lg-right text-left">Name</label>
                            <div class="col-9">
                                <input class="form-control form-control-lg form-control-solid" type="text" v-model="name"/>
                            </div>
                        </div>

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
            this.fetchCountries();
        },

        props: {
            town_id: String,
            causer_id: String,
        },

        data(){
            return{
                name: '',
                country_id: '',
                countries: [],

                alert_error:false,
                alert_success:false,
                loader:false,

            }
        },

        methods: {

            fetchCountries(){
                let uri = base_url+`v1/country-list`;
                axios.get(uri).then((response) => {
                    this.countries = response.data;
                });
            },

            fetchDetails(){
                let uri = base_url+`v1/town-details/`+this.town_id;
                axios.get(uri).then((response) => {

                    let town = response.data;
                    if(town){
                        this.name = town.name;
                        this.country_id = town.country_id;
                    }
                });
            },

            formSubmit(){

                this.loader = true;

                var name = this.name;
                if (!name) {
                    alert('Enter name');
                    this.loader = false;
                    return
                }

                var country_id = this.country_id;
                if (!country_id) {
                    alert('Select Country');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.town_id);
                formData.append( 'name', name );
                formData.append( 'country_id', country_id);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/town-edit`;
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
                formData.append( 'id', this.town_id);

                let uri = base_url+`v1/town-delete`;
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
