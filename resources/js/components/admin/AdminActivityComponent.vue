<template>
    <div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-xl-11">

                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label>Date:</label>
                                    <select class="form-control" v-model="activity_date" @change.prevent="selectedDate">
                                        <option value="all">All</option>
                                        <option value="today">Today</option>
                                        <option value="current_week">Current Week</option>
                                        <option value="last_week">Last Week</option>
                                        <option value="current_month">Current Month</option>
                                        <option value="current_year">Current Year</option>
                                        <option value="custom_date">
                                            <template v-if="custom_date">
                                                {{ custom_date }}
                                            </template>
                                            <template v-else>
                                                Custom Date
                                            </template>
                                        </option>
                                        <option value="custom_range">
                                            <template v-if="custom_start_date && custom_end_date">
                                                ({{ custom_start_date }}) to ({{ custom_end_date }})
                                            </template>
                                            <template v-else>
                                                Custom Range
                                            </template>

                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-6">
                                    <label>Department:</label>
                                    <select class="form-control" v-model="department_id">
                                        <option value="all">All</option>
                                        <option value="3">Operations</option>
                                        <option value="4">Customer service</option>
                                        <option value="5">Finance</option>
                                        <option value="6">IT</option>
                                        <option value="7">Transport</option>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label>Status:</label>
                                    <select class="form-control" v-model="status" @change.prevent="selectedDate">
                                        <option value="all">All</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">inactive</option>

                                    </select>
                                </div>

                                <div class="col-lg-6">
                                    <label>Admin Detail:</label>
                                    <select class="form-control" v-model="admin_id">
                                        <option value="all">All admins</option>
                                        <option v-for="admin in admins" :value="admin.id">{{ admin.first_name }} {{ admin.last_name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-8">
                                    <button class="btn btn-success mr-2" v-if="activities.length > 0">Generate XLS</button>
                                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                    <div class="spinner-border text-danger" role="status" v-if="loader">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Login Time</th>
                                <th>Last session</th>
                                <th>Login count</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(activity, index) in activities">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ activity.first_name }} {{ activity.last_name }}</td>
                                    <td>{{ activity.role }}</td>
                                    <td :class="{'text-success': activity.is_online, 'text-danger': !activity.is_online}">
                                        {{ activity.is_online ? 'online' : (activity.is_online === 0 ? 'offline' : '') }}</td>
                                    <td>{{ activity.login_time ? activity.login_time : 'N/A' }}</td>
                                    <td>{{ activity.last_login ? activity.last_login : 'N/A' }}</td>
                                    <td>{{ activity.login_count }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </form>

        <div class="modal fade" id="modalCustomDate" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
             aria-hidden="true">
                <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="customDateSubmit" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Custom Date</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="dateClose">
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
                                            <label class="col-xl-4 col-lg-4 col-form-label">Custom Date</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="custom_date" type="date" v-model="custom_date"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="dateClose">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Select Date</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="modalCustomRange" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
             aria-hidden="true">
                <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="customRangeSubmit" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Custom Range</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="dateClose">
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
                                            <label class="col-xl-4 col-lg-4 col-form-label">Start Date</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="custom_start_date" type="date" v-model="custom_start_date"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">End Date</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="custom_end_date" type="date" v-model="custom_end_date"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="dateClose">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Select Date</button>
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
            this.fetchAdmins();
        },

        props: {
            causer_id: String,
        },

        data(){
            return{
                activity_date: 'all',
                admin_id: 'all',
                custom_date: '',
                custom_start_date: '',
                custom_end_date: '',
                department_id: 'all',
                status: 'all',
                loader:false,
                base_url: '',
                admins: [],
                activities: [],
            }
        },

        methods: {

            fetchAdmins() {
                let uri = base_url + `v1/admin-list`;
                axios.get(uri).then((response) => {
                    this.admins = response.data;
                });
            },
            selectedDate(){
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";

                if(this.activity_date == 'custom_date'){
                    $('#modalCustomDate').modal('show');
                }else if(this.activity_date == 'custom_range'){
                    $('#modalCustomRange').modal('show');
                }

            },

            customDateSubmit(){

                if(this.custom_date == ''){
                    alert('Select custom date');
                    return;
                }

                $('#modalCustomDate').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();
            },

            customRangeSubmit(){

                if(this.custom_start_date == ''){
                    alert('Select start date');
                    return;
                }

                if(this.custom_end_date == ''){
                    alert('Select end date');
                    return;
                }

                $('#modalCustomRange').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();
            },

            dateClose(){
                this.activity_date = "all";
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";
            },

            formSubmit() {

            this.loader = true;
            var admin_id = this.admin_id;
            var activity_date = this.activity_date;
            var department_id = this.department_id;
            var status = this.status;

            const vm = this;
            const formData = new FormData();
            formData.append('admin_id', this.admin_id);
            formData.append('activity_date', activity_date);
            formData.append('department_id', department_id);
            formData.append('status', status);

            let uri = base_url + `v1/report-admin-activity`;
            axios.post(uri, formData)
                .then(function (response) {
                    vm.activities = response.data;
                    vm.loader = false;

                })
                .catch(function (error) {
                    console.log(error);
                    vm.loader = false;
                    vm.alert_error = true;
                });

            },

            generateExcel(){


            }
        }
    }
</script>

