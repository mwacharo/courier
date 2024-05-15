<template>

    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <div class="card card-custom col-10">
                <!--begin::Header-->
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">Call Centre Management
                            <span class="d-block text-muted pt-2 font-size-sm">Call management made easy</span>
                        </h3>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body">

                    <div class="row">
                        <div class="col-xl-3">
                            <!--begin::Stats Widget 25-->
                            <div class="card card-custom bg-light-success card-stretch gutter-b">
                                <!--begin::Body-->
                                <div class="card-body">
                                    <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">{{ summary_total_agents}}</span>
                                    <span class="font-weight-bold text-muted  font-size-sm">Total Agents</span>
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Stats Widget 25-->
                        </div>
                        <div class="col-xl-3">
                            <!--begin::Stats Widget 26-->
                            <div class="card card-custom bg-light-danger card-stretch gutter-b">
                                <!--begin::ody-->
                                <div class="card-body">
                                    <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">{{ summary_available_agents }}</span>
                                    <span class="font-weight-bold text-muted font-size-sm">Available Agents</span>
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Stats Widget 26-->
                        </div>
                        <div class="col-xl-3">
                            <!--begin::Stats Widget 27-->
                            <div class="card card-custom bg-light-info card-stretch gutter-b">
                                <!--begin::Body-->
                                <div class="card-body">
                                    <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">{{ summary_busy_agents }}</span>
                                    <span class="font-weight-bold text-muted  font-size-sm">Busy Agents</span>
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Stats Widget 27-->
                        </div>
                        <div class="col-xl-3">
                            <!--begin::Stats Widget 28-->
                            <div class="card card-custom bg-light-warning card-stretch gutter-b">
                                <!--begin::Body-->
                                <div class="card-body">
                                    <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">{{ summary_call_duration }}s</span>
                                    <span class="font-weight-bold text-muted  font-size-sm">Call Duration (Today)</span>
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Stat: Widget 28-->
                        </div>
                    </div>
                    <!--End::Row-->

                    <!--Begin::Row-->
                    <div class="row">
                        <div class="col-xl-3">
                            <!--begin::Stats Widget 29-->
                            <div class="card card-custom bgi-no-repeat card-stretch gutter-b"
                                 style="background-position: right top; background-size: 30% auto; background-image: url(assets/media/svg/shapes/abstract-1.svg)">
                                <!--begin::Body-->
                                <div class="card-body">
                                    <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">{{ summary_inbound_call_completed }}</span>
                                    <span class="font-weight-bold text-muted  font-size-sm">Inbound Calls</span>
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Stats Widget 29-->
                        </div>
                        <div class="col-xl-3">
                            <!--begin::Stats Widget 30-->
                            <div class="card card-custom bg-info card-stretch gutter-b">
                                <!--begin::Body-->
                                <div class="card-body">
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 d-block">{{ summary_outbound_call_completed }}</span>
                                    <span class="font-weight-bold text-white  font-size-sm">Outbound Calls</span>
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Stats Widget 30-->
                        </div>
                        <div class="col-xl-3">
                            <!--begin::Stats Widget 31-->
                            <div class="card card-custom bg-danger card-stretch gutter-b">
                                <!--begin::Body-->
                                <div class="card-body">
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 d-block">{{ summary_call_missed }}</span>
                                    <span class="font-weight-bold text-white  font-size-sm">Missed Calls</span>
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Stats Widget 31-->
                        </div>
                        <div class="col-xl-3">
                            <!--begin::Stats Widget 32-->
                            <div class="card card-custom bg-dark card-stretch gutter-b">
                                <!--begin::Body-->
                                <div class="card-body">
                                    <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 mt-6 text-hover-primary d-block">{{ 0 }}</span>
                                    <span class="font-weight-bold text-white  font-size-sm">Queue</span>
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Stats Widget 32-->
                        </div>
                    </div>
                    <!--End::Row-->

                    <h4 style="margin-top: 50px;">Agent List (Currently displaying today) - <a href="#" data-toggle="modal" data-target="#filterAgentModal">Filter records</a></h4>
                    <table class="table" style="margin-top: 10px;">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Agent Id</th>
                            <th scope="col">Status</th>
                            <th scope="col">Total Calls</th>
                            <th scope="col">Inbound</th>
                            <th scope="col">Outbound</th>
                            <th scope="col">Pending</th>
                            <th scope="col">Scheduled</th>
                            <th scope="col">Cancelled</th>
                            <th scope="col">Dry Rate</th>
                            <th scope="col">Duration</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(agent_list_summary, index) in agent_list_summaries">
                            <td>{{ agent_list_summary.admin_name }}</td>
                            <td>{{ agent_list_summary.client_name }}</td>
                            <td>{{ agent_list_summary.status }}</td>
                            <td>{{ agent_list_summary.summary_call_completed }}</td>
                            <td>{{ agent_list_summary.summary_inbound_call_completed }}</td>
                            <td>{{ agent_list_summary.summary_outbound_call_completed }}</td>
                            <td>{{ agent_list_summary.summary_pending_order }}</td>
                            <td>{{ agent_list_summary.summary_scheduled_order }}</td>
                            <td>{{ agent_list_summary.summary_cancelled_order }}</td>
                            <td>{{ agent_list_summary.summary_delivery_order }}/{{ agent_list_summary.summary_delivery_order + agent_list_summary.summary_scheduled_order }} - ({{ agent_list_summary.summary_delivery_rate }}%)</td>
                            <td>{{ agent_list_summary.summary_call_duration }}</td>
                        </tr>
                        </tbody>

                    </table>

                    <h4 style="margin-top: 50px;">Call Waiting List</h4>
                    <table class="table" style="margin-top: 10px;">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Conference</th>
                            <th scope="col">Caller Phone</th>
                            <th scope="col">Start time</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(call_waiting_history, index) in call_waiting_histories">
                            <td>{{ call_waiting_history.conference }}</td>
                            <td>{{ call_waiting_history.callerNumber }}</td>
                            <td>{{ call_waiting_history.created_at }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <h4 style="margin-top: 50px;">Active/Ongoing Calls</h4>
                    <table class="table" style="margin-top: 10px;">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Agent</th>
                            <th scope="col">Id</th>
                            <th scope="col">Recording</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Direction</th>
                            <th scope="col">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(call_ongoing_history, index) in call_ongoing_histories">
                            <td>{{ call_ongoing_history.adminName }}</td>
                            <td>{{ call_ongoing_history.agentId }}</td>
                            <td>-</td>
                            <td>{{ call_ongoing_history.callerNumber }}</td>
                            <td>{{ call_ongoing_history.direction }}</td>
                            <td>{{ call_ongoing_history.created_at }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <h4 style="margin-top: 50px;">Call History (Currently displaying today) - <a href="#" data-toggle="modal" data-target="#filterCallHistoryModal">Filter records</a></h4>
                    <table class="table" style="margin-top: 10px;">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Agent</th>
                            <th scope="col">Id</th>
                            <th scope="col">Recording</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Direction</th>
                            <th scope="col">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(call_history, index) in call_histories">
                            <td>{{ call_history.adminName }}</td>
                            <td>{{ call_history.agentId }}</td>
                            <td>

                                <audio controls>
                                    <source :src="call_history.recordingUrl" type="audio/mpeg">
                                    Your browser does not support the audio tag.
                                </audio>

                            </td>
                            <td>{{ call_history.callerNumber }}</td>
                            <td>{{ call_history.direction }}</td>
                            <td>{{ call_history.created_at }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->

        <div class="modal fade" id="filterAgentModal" tabindex="-1" role="dialog" aria-labelledby="filterAgentModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <form class="form" @submit.prevent="formSubmitAgentFilter" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="filterAgentModalLabel">Filter Agent List</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!--begin::Group-->
                            <div class="form-group row">
                                <div class="col-lg-12 col-xl-12">
                                    <label>Date:</label>
                                    <select class="form-control" v-model="call_date" @change.prevent="selectedDate">
                                        <option value="all">All</option>
                                        <option value="today">Today</option>
                                        <option value="current_week">Current Week</option>
                                        <option value="last_week">Last Week</option>
                                        <option value="current_month">Current Month</option>
                                        <option value="current_year">Current Year</option>
                                        <option value="custom_date">Custom Date</option>
                                        <option value="custom_range">Custom Range</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Filter</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="filterCallHistoryModal" tabindex="-1" role="dialog" aria-labelledby="filterCallHistoryModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <form class="form" @submit.prevent="formSubmitCallHistoryFilter" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="filterCallHistoryModalLabel">Filter Call History</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="form" @submit.prevent="formSubmitCallHistoryFilter" method="post">

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <div class="col-lg-12 col-xl-12">
                                        <label>Agent Details:</label>
                                        <select class="form-control" v-model="search_agent_id">
                                            <option value="all">All</option>
                                            <option v-for="agent in agents" :value="agent.client_name">{{ agent.client_name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-12 col-xl-12">
                                        <label>Date:</label>
                                        <select class="form-control" v-model="call_date" @change.prevent="selectedDate">
                                            <option value="all">All</option>
                                            <option value="today">Today</option>
                                            <option value="current_week">Current Week</option>
                                            <option value="last_week">Last Week</option>
                                            <option value="current_month">Current Month</option>
                                            <option value="current_year">Current Year</option>
                                            <option value="custom_date">Custom Date</option>
                                            <option value="custom_range">Custom Range</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-12 col-xl-12">
                                        <label>Destination Phone:</label>
                                        <input class="form-control form-control-solid form-control-lg" type="text" v-model="search_destination_number"/>
                                    </div>
                                    <div class="col-lg-12 col-xl-12">
                                        <label>Caller Phone:</label>
                                        <input class="form-control form-control-solid form-control-lg" type="text" v-model="search_caller_number"/>
                                    </div>
                                </div>
                                <!--end::Group-->

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Filter</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

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
            this.fetchAgents ();
            this.fetchCallCentreSummary();
            this.fetchCallWaitingHistory();
            this.fetchCallOngoingHistory();
            this.fetchAgentListSummaryHistory();
            this.fetchCallHistory();
            this.fetchCallHistory();
        },

        data(){
            return{

                search_agent_id: 'all',
                call_date: 'all',
                custom_date: '',
                custom_start_date: '',
                custom_end_date: '',
                search_phone: '',
                search_caller_number: '',
                search_destination_number: '',

                agent_list_summaries: [],
                call_waiting_histories: [],
                call_ongoing_histories: [],
                call_histories: [],
                agents: [],

                summary_total_agents : 0,
                summary_available_agents : 0,
                summary_busy_agents : 0,
                summary_inbound_call_completed : 0,
                summary_outbound_call_completed : 0,
                summary_call_completed : 0,
                summary_call_duration : 0,
                summary_call_missed : 0,
                summary_call_waiting : 0,

                alert_error:false,
                alert_success:false,
                loader:false,

            }
        },

        methods: {

            fetchAgents() {
                let uri = base_url + `v1/call-agent-list`;
                axios.get(uri).then((response) => {
                    this.agents = response.data;
                });
            },

            fetchCallWaitingHistory() {
                let uri = base_url + `v1/report-call-waiting-history`;
                axios.get(uri).then((response) => {
                    this.call_waiting_histories = response.data;
                });
            },

            fetchCallOngoingHistory() {
                let uri = base_url + `v1/report-call-ongoing-history`;
                axios.get(uri).then((response) => {
                    this.call_ongoing_histories = response.data;
                });
            },

            fetchAgentListSummaryHistory() {
                let uri = base_url + `v1/report-call-agent-list-summary`;
                axios.get(uri).then((response) => {
                    this.agent_list_summaries = response.data;
                });
            },

            fetchCallHistory() {
                let uri = base_url + `v1/report-call-history-list`;
                axios.get(uri).then((response) => {
                    this.call_histories = response.data;
                });
            },

            fetchCallCentreSummary() {
                let uri = base_url + `v1/report-call-centre-summary`;
                axios.get(uri).then((response) => {
                    this.summary_total_agents = response.data.summary_total_agents;
                    this.summary_available_agents = response.data.summary_available_agents;
                    this.summary_busy_agents = response.data.summary_busy_agents;
                    this.summary_inbound_call_completed = response.data.summary_inbound_call_completed;
                    this.summary_outbound_call_completed = response.data.summary_outbound_call_completed;
                    this.summary_call_completed = response.data.summary_call_completed;
                    this.summary_call_duration = response.data.summary_call_duration;
                    this.summary_call_missed = response.data.summary_call_missed;
                    this.summary_call_waiting = response.data.summary_call_waiting;
                });
            },

            formSubmitAgentFilter(){

                $('#filterAgentModal').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();

                this.agent_list_summaries = [];

                var call_date = this.call_date;
                var custom_date = this.custom_date;
                var custom_start_date = this.custom_start_date;
                var custom_end_date = this.custom_end_date;

                if(call_date == 'custom_date'){

                    if(this.custom_date == ''){
                        alert("Custom date not selected");
                        return;
                    }

                }

                if(call_date == 'custom_range'){

                    if(this.custom_start_date == ''){
                        alert("Custom start date not selected");
                        return;
                    }

                    if(this.custom_end_date == ''){
                        alert("Custom end date not selected");
                        return;
                    }
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'call_date', call_date);
                formData.append( 'custom_date', custom_date);
                formData.append( 'custom_start_date', custom_start_date);
                formData.append( 'custom_end_date', custom_end_date);

                let uri = base_url+`v1/report-call-agent-list-summary-filter`;
                axios.post(uri, formData,)
                    .then(function (response) {
                        vm.agent_list_summaries = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

            formSubmitCallHistoryFilter(){

                $('#filterCallHistoryModal').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();

                this.call_histories = [];
                var call_date = this.call_date;
                var custom_date = this.custom_date;
                var custom_start_date = this.custom_start_date;
                var custom_end_date = this.custom_end_date;

                if(call_date == 'custom_date'){

                    if(this.custom_date == ''){
                        alert("Custom date not selected");
                        return;
                    }

                }

                if(call_date == 'custom_range'){

                    if(this.custom_start_date == ''){
                        alert("Custom start date not selected");
                        return;
                    }

                    if(this.custom_end_date == ''){
                        alert("Custom end date not selected");
                        return;
                    }
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'call_date', call_date);
                formData.append( 'custom_date', custom_date);
                formData.append( 'custom_start_date', custom_start_date);
                formData.append( 'custom_end_date', custom_end_date);
                formData.append( 'agent_id', this.search_agent_id);
                formData.append( 'caller_number', this.search_caller_number);
                formData.append( 'destination_number', this.search_destination_number);

                let uri = base_url+`v1/report-call-history-list-filter`;
                axios.post(uri, formData,)
                    .then(function (response) {
                        vm.call_histories = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

            selectedDate(){
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";

                if(this.call_date == 'custom_date'){
                    $('#modalCustomDate').modal('show');
                }else if(this.call_date == 'custom_range'){
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
                this.call_date = "all";
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";
            },

        }
    }
</script>
