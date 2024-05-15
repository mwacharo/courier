<template>
    <form @submit.prevent="formSubmit" method="post">
        <!--begin::Body-->
        <div class="card-body">
            <!--begin::Row-->
            <div class="row">
                <div class="col-xl-2"></div>
                <div class="col-xl-7">
                    <!--begin::Row-->
                    <div class="row">
                        <label class="col-3"></label>
                        <div class="col-9">
                            <div class="alert alert-success" role="alert" v-if="alert_success">Record updated successfully!</div>
                            <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating record!</div>
                            <h6 class="text-dark font-weight-bold mb-10">Change Or Recover Your Password:</h6>
                        </div>
                    </div>
                    <!--end::Row-->
                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-form-label col-3 text-lg-right text-left">Current Password</label>
                        <div class="col-9">
                            <input class="form-control form-control-lg form-control-solid mb-1" type="password" value="Current password" v-model="current_password"/>
                        </div>
                    </div>
                    <!--end::Group-->
                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-form-label col-3 text-lg-right text-left">New Password</label>
                        <div class="col-9">
                            <input class="form-control form-control-lg form-control-solid" type="password" value="New password" v-model="new_password"/>
                        </div>
                    </div>
                    <!--end::Group-->
                    <!--begin::Group-->
                    <div class="form-group row">
                        <label class="col-form-label col-3 text-lg-right text-left">Verify Password</label>
                        <div class="col-9">
                            <input class="form-control form-control-lg form-control-solid" type="password" value="Verify password" v-model="verify_password"/>
                        </div>
                    </div>
                    <!--end::Group-->
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Body-->
        <!--begin::Footer-->
        <div class="card-footer pb-0">
            <div class="row">
                <div class="col-xl-2"></div>
                <div class="col-xl-7">
                    <div class="row">
                        <div class="col-3"></div>
                        <div class="col-9">
                            <button type="submit" class="btn btn-light-primary font-weight-bold">Save changes</button>
                            <button type="cancel" class="btn btn-clean font-weight-bold">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Footer-->
    </form>

</template>

<script>
    export default {

        props: {
            merchant_id: String,
        },

        data() {
            return {
                current_password: '',
                new_password: '',
                verify_password: '',

                alert_error: false,
                alert_success: false,
                loader: false,

            }
        },

        methods: {

            formSubmit() {

                this.loader = true;

                var current_password = this.current_password;
                if (!current_password) {
                    alert('Enter current password');
                    this.loader = false;
                    return
                }

                var new_password = this.new_password;
                if (!new_password) {
                    alert('Enter new password');
                    this.loader = false;
                    return
                }

                var verify_password = this.verify_password;
                if (!verify_password) {
                    alert('Enter verify password');
                    this.loader = false;
                    return
                }

                if(new_password != verify_password){
                    alert('New password and verify password dont match');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();

                formData.append('current_password', current_password);
                formData.append('new_password', new_password);
                formData.append('verify_password', verify_password);
                formData.append('merchant_id', this.merchant_id);

                let uri = base_url + `v1/merchant-password-change`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if (status === 1) {
                            vm.alert_success = true;
                            vm.loader = false;
                            vm.current_password = '';
                            vm.new_password = '';
                            vm.verify_password = '';
                            alert('Password successfully changed!');

                        } else {
                            var message = response.data.message;
                            alert(message);
                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);

                        alert('Error changing password!');
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },
        }
    }

</script>
