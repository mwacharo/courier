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
                                <h6 class="text-dark font-weight-bold mb-10">Country Info:</h6>
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
            country_id: String,
            causer_id: String,
        },

        data(){
            return{
                name: '',

                alert_error:false,
                alert_success:false,
                loader:false,

            }
        },

        methods: {

            fetchDetails(){
                let uri = base_url+`v1/country-details/`+this.country_id;
                axios.get(uri).then((response) => {

                    let country = response.data;
                    if(country){
                        this.name = country.name;
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

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.country_id);
                formData.append( 'name', name );
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/country-edit`;
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
                formData.append( 'id', this.country_id);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/country-delete`;
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
