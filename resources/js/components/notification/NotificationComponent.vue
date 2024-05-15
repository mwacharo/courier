<template>

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-2">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Notifications</h5>
                    <!--end::Page Title-->
                    <!--begin::Actions-->
                    <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-4 bg-gray-200"></div>
                    <span class="text-muted font-weight-bold mr-4"><a href="mailto:support@boxleocourier.com">Need Help? Contact Support</a></span>
                    <!--end::Actions-->
                </div>
                <!--end::Info-->

            </div>
        </div>
        <!--end::Subheader-->
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <!--begin::Card-->
                <div class="card card-custom col-10">
                    <!--begin::Header-->
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Notifications Management
                                <span class="d-block text-muted pt-2 font-size-sm">Notifications management made easy</span>
                            </h3>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--begin: Datatable-->
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th title="Field #1">No</th>
                                <th title="Field #2">Message</th>
                                <th title="Field #3">Status</th>
                                <th title="Field #4">Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(notification, index) in notifications">
                                <td>No.{{ index + 1 }}</td>
                                <td><a href="#" data-toggle="modal" data-target="#modalEditItem" @click.prevent="selectedNotification(notification)">{{ notification.message }}</a></td>
                                <td>
                                    <span class="badge badge-primary" v-if="notification.is_read == 1">Read</span>
                                    <span class="badge badge-danger" v-else>Unread</span>
                                </td>
                                <td>{{ notification.created_at }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <!--end: Datatable-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->

        <div class="modal fade" id="modalEditItem" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="itemEditSubmit" method="post">
                    <div class="modal-content modal-dialog-scrollable">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Item</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="itemClose">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body" style="height: 300px;">


                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Message</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="message" type="text" v-model="message" disabled/>
                                            </div>
                                        </div>
                                        <!--end::Group-->
                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Created at</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="created_at" type="text" v-model="created_at" disabled/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</template>

<script>
    export default {

        mounted(){
            this.fetchNotification();
        },

        data(){
            return{
                message: "",
                created_at: "",
                notification: null,
                notifications: [],
            }
        },

        methods: {

            fetchNotification(){
                let uri = base_url+`v1/notification-list`;
                axios.get(uri).then((response) => {
                    this.notifications = response.data;
                });
            },

            selectedNotification(notification){
                this.notification = notification;
                this.message = notification.message;
                this.created_at = notification.created_at;

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', notification.id);
                let uri = base_url+`v1/notification-edit`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.fetchNotification();
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }

        }
    }
</script>
