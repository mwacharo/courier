@extends('app-admin')
@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-2">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Call Agents</h5>
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
                            <h3 class="card-label">Call Agents Management
                                <span class="d-block text-muted pt-2 font-size-sm">Call Agents management made easy</span>
                            </h3>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--begin: Datatable-->
                        <table class="datatable datatable-bordered datatable-head-custom" id="kt_datatable">
                            <thead>
                            <tr>
                                <th title="Field #1">No</th>
                                <th title="Field #2">AgentId</th>
                                <th title="Field #3">Admin</th>
                                <th title="Field #4">Availability</th>
                                <th title="Field #5">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($call_agents as $call_agent)
                                <tr>
                                    <td>No.{{ $loop->iteration }}</td>
                                    <td>{{ $call_agent['client_name'] }}</td>
                                    <td>{{ $call_agent['first_name'] }} {{ $call_agent['last_name'] }}</td>
                                    <td>{{ $call_agent['status'] }}</td>
                                    <td>
                                        @can('call-agent-details')
                                            <a href="{{ route('admin.call-agent.details', ['id' => $call_agent['id']] ) }}">View details</a>
                                        @endcan
                                    </td>

                                </tr>
                            @endforeach
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
    </div>

@endsection
