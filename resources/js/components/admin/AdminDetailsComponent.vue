<template>
    <div>
        <div class="d-flex justify-content-center">
            <div class="col-8 alert alert-warning text-center" role="alert" v-if="data_error">No Record Found!!</div>
    </div>

    <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">

                <div class="row">
                    <div class="col-xl-2"></div>
                    <div class="col-xl-7 my-2">

                        <div class="alert alert-success" role="alert" v-if="alert_success">Record details updated!</div>
                        <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating record!</div>
                        <!--begin::Row-->
                        <div class="row">
                            <label class="col-3"></label>
                            <div class="col-9">
                                <h6 class="text-dark font-weight-bold mb-10">Administrator Info:</h6>
                            </div>
                        </div>
                        <!--end::Row-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-3 col-form-label">First Name</label>
                            <div class="col-lg-9 col-xl-9">
                                <input class="form-control form-control-solid form-control-lg" name="first_name" type="text"
                                       v-model="first_name"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-3 col-form-label">Last Name</label>
                            <div class="col-lg-9 col-xl-9">
                                <input class="form-control form-control-solid form-control-lg" name="last_name" type="text"
                                       v-model="last_name"/>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-3 col-form-label">National ID</label>
                            <div class="col-lg-9 col-xl-9">
                                <input class="form-control form-control-solid form-control-lg" name="national_id"
                                       type="number" min="0" onwheel="this.blur()" v-model="national_id"/>
                            </div>
                        </div>
                        <!--end::Group-->
                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-3 col-form-label">Date of Birth</label>
                            <div class="col-lg-9 col-xl-9">
                                <input class="form-control form-control-solid form-control-lg" name="date_of_birth"
                                       type="date" v-model="date_of_birth"/>
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
                            <label class="col-xl-3 col-lg-3 col-form-label">Profile Image</label>
                            <div class="col-lg-9 col-xl-9">
                                <input id="image" class="form-control form-control-solid form-control-lg" name="file" type="file">
                                <a v-if="profile_image!=null" @click="deleteImage" class="text-red">Delete Image</a>
                            </div>
                        </div>
                        <!--end::Group-->

                        <!--begin::Group-->
                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-3 col-form-label">Role</label>
                            <div class="col-lg-9 col-xl-9">
                                <select class="form-control form-control-solid form-control-lg" name="name" type="text"
                                        v-model="role">
                                        <option value="">Select Role</option>
                                        <option value="2">Administrator</option>
                                        <option value="3">Operations</option>
                                        <option value="4">Customer Service</option>
                                        <option value="5">Finance</option>
                                        <option value="6">IT</option>
                                        <option value="7">Transport</option>
                                </select>
                            </div>
                        </div>

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

                        <!--begin::Wizard Actions-->
                        <div class="d-flex justify-content-between border-top pt-10 mt-15">
                            <div>
                                <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4" data-wizard-type="action-submit">Update</button>
                                <button type="button" class="btn btn-danger font-weight-bolder px-9 py-4" data-wizard-type="action-submit" @click.prevent="deleteDetails()">Delete</button>
                                <button type="button" class="btn btn-warning font-weight-bolder px-9 py-4" data-wizard-type="action-submit" @click.prevent="resetPassword()">Reset Password</button>
                                <div class="spinner-border spinner-border" role="status" v-if="loader">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

    </form>

    </div>
</template>

<script>
    export default {

        mounted(){
            this.fetchDetails();
        },

        props: {
            id: String,
            causer_id: String,
        },

        data(){
            return{
                first_name: '',
                last_name: '',
                date_of_birth: '',
                national_id: '',
                phone_number: '',
                email: '',
                role: '',
                profile_image: '',
                enabled: false,

                alert_error:false,
                alert_success:false,
                loader:false,

            }
        },

        methods: {

            fetchDetails(){
                let uri = base_url+`v1/admin-details/`+this.id;
                axios.get(uri).then((response) => {

                    let admin = response.data;
                    if(admin){
                        this.first_name = admin.first_name;
                        this.last_name = admin.last_name;
                        this.date_of_birth = admin.date_of_birth;
                        this.national_id = admin.national_id;
                        this.phone_number = admin.phone_number;
                        this.email = admin.email;
                        this.role = admin.role;
                        this.enabled = admin.enabled;
                        this.profile_image = admin.profile_image;

                    }
                });
            },

            formSubmit(){

                this.loader = true;

                var first_name = this.first_name;
                if (!first_name) {
                    alert('Enter first name');
                    this.loader = false;
                    return
                }

                var last_name = this.last_name;
                if (!last_name) {
                    alert('Enter last name');
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

                var role = this.role;
                if (!role) {
                    alert('Select role');
                    this.loader = false;
                    return
                }

                var date_of_birth = this.date_of_birth;
                var national_id = this.national_id;
                var enabled = this.enabled;

                const vm = this;
                const formData = new FormData();
                const image = document.querySelector( '#image' );
                formData.append( 'image', image.files[0] );
                formData.append('id', this.id);
                formData.append('first_name', first_name);
                formData.append('last_name', last_name);
                formData.append('date_of_birth', date_of_birth);
                formData.append('national_id', national_id);
                formData.append('email', email);
                formData.append('phone_number', phone_number);
                formData.append('enabled', enabled);
                formData.append('role', role);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/admin-edit`;
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
                formData.append( 'id', this.id);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/admin-delete`;
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

            },

            resetPassword(){

                this.loader = true;

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.id);

                let uri = base_url+`v1/admin-reset-password`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Password reset successfully!');

                        }else{

                            alert('Error resetting password!');
                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        alert('Error resetting password!');
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },

            deleteImage(){

                this.loader = true;
                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.id);

                let uri = base_url+`v1/admin-delete-image`;
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
                        vm.data_error = true;
                    });

            }
        }
    }
</script>
