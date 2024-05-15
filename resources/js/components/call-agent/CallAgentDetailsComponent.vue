<template>

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
                        <h6 class="text-dark font-weight-bold mb-10">Call Agent Info:</h6>
                    </div>
                </div>
                <!--end::Row-->

                <!--begin::Group-->
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">Admin</label>
                    <div class="col-lg-9 col-xl-9">
                        <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="admin_id">
                            <option value="">Select Admin</option>
                            <option v-for="admin in admins" :value="admin.id">{{ admin.first_name }} {{ admin.last_name }}</option>
                        </select>
                    </div>
                </div>
                <!--end::Group-->
                <!--begin::Group-->
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">Client Name</label>
                    <div class="col-lg-9 col-xl-9">
                        <input class="form-control form-control-solid form-control-lg" name="client_name" type="text"
                               v-model="client_name" disabled/>
                    </div>
                </div>
                <!--end::Group-->

                <!--begin::Group-->
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">Phone Number</label>
                    <div class="col-lg-9 col-xl-9">
                        <input class="form-control form-control-solid form-control-lg" name="phone_number" type="text"
                               v-model="phone_number"/>
                    </div>
                </div>
                <!--end::Group-->

                <!--begin::Group-->
                <div class="form-group row">
                    <label class="col-xl-3 col-lg-3 col-form-label">Status</label>
                    <div class="col-lg-9 col-xl-9">
                        <select class="form-control form-control-solid form-control-lg" name="name" v-model="status">
                            <option value="">Select Status</option>
                            <option value="available">Available</option>
                            <option value="inactive">Inactive</option>
                            <option value="busy">Busy</option>
                        </select>
                    </div>
                </div>
                <!--end::Group-->


                <!--begin::Wizard Actions-->
                <div class="d-flex justify-content-between border-top pt-10 mt-15">
                    <div>
                        <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4"
                                data-wizard-type="action-submit">Edit
                        </button>
                        <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>

</template>

<script>
    export default {

        mounted() {
            this.fetchAdmins();
            this.fetchDetails();
        },

        props: {
            agent_id: String,
        },

        data() {
            return {
                client_name: '',
                status: '',
                phone_number: '',
                admin_id: '',
                sessionId: '',
                token: '',
                admins: [],

                alert_error: false,
                alert_success: false,
                loader: false,

            }
        },

        methods: {

            fetchAdmins() {
                let uri = base_url + `v1/admin-list`;
                axios.get(uri).then((response) => {
                    this.admins = response.data;
                });
            },

            formSubmit() {

                this.loader = true;
                var client_name = this.client_name;
                var phone_number = this.phone_number;

                var admin_id = this.admin_id;
                if (!admin_id) {
                    alert('Select Admin');
                    this.loader = false;
                    return
                }

                var status = this.status;
                if (!status) {
                    alert('Select Status');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append('id', this.agent_id);
                formData.append('client_name', client_name);
                formData.append('phone_number', phone_number);
                formData.append('phone_number', phone_number);
                formData.append('admin_id', admin_id);
                formData.append('status', status);

                let uri = base_url + `v1/call-agent-edit`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if (status === 1) {
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Record updated successfully!');
                            vm.fetchDetails();

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

            fetchDetails() {
                const vm = this;
                let uri = base_url + `v1/call-agent-details-2/` + this.agent_id;
                console.log(uri);

                axios.get(uri).then((response) => {
                    let call_agent = response.data;
                    if (call_agent) {
                        vm.client_name = call_agent.client_name;
                        vm.status = call_agent.status;
                        vm.phone_number = call_agent.phone_number;
                        vm.admin_id = call_agent.admin_id;
                        vm.sessionId = call_agent.sessionId;
                        vm.token = call_agent.token;
                    }
                });
            },

        }
    }
</script>
