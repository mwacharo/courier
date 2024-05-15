<template>
    <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <!--begin::Wizard Step 1-->
                <div class="alert alert-success" role="alert" v-if="alert_success">Record added successfully!</div>
                <div class="alert alert-danger" role="alert" v-if="alert_error">Error adding record!</div>

                <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                    <h5 class="text-dark font-weight-bold mb-10">New Country Details:</h5>

                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-xl-3 col-lg-3 col-form-label">Name</label>
                        <div class="col-lg-9 col-xl-9">
                            <input class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="name"/>
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
                formData.append( 'name', name );
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/country-create`;
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
