@extends('app-admin')
@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-2">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Import Orders</h5>
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
                <div class="card card-custom card-transparent">
                    <div class="card-body p-0">
                        <div class="card card-custom card-shadowless rounded-top-0">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="row justify-content-center py-8 px-8 py-lg-15 px-lg-10">
                                    <div class="col-xl-12 col-xxl-10">
                                        <form class="form" id="kt_form" method="POST" action="{{ route('admin.order.import.upload') }}" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="row justify-content-center">
                                                <div class="col-xl-9">
                                                    <!--begin::Wizard Step 1-->

                                                    @if (session('success'))
                                                        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                                                    @endif

                                                    @if (session('error'))
                                                        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                                                    @endif

                                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">
                                                        <h5 class="text-dark font-weight-bold mb-10">Import Details:</h5>

                                                        <!--begin::Group-->
                                                        <div class="form-group row">
                                                            <label class="col-xl-3 col-lg-3 col-form-label">Excel file (xls or xlsx)<br>
                                                                <a href="{{ route('admin.download.order.template') }}" class="text-danger">Download template</a>
                                                            </label>                                                            <div class="col-lg-9 col-xl-9">
                                                                <input class="form-control form-control-solid form-control-lg" type="file" name="select_file"/>
                                                            </div>
                                                        </div>
                                                        <!--end::Group-->

                                                        <div class="form-group row">
                                                            <label class="col-xl-3 col-lg-3 col-form-label">Merchant Details</label>
                                                            <div class="col-lg-9 col-xl-9">
                                                                <select class="form-control" name="merchant_id" required>
                                                                    <option>Select Merchant</option>
                                                                    @foreach($merchants as $merchant)
                                                                        <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!--end::Group-->

                                                    </div>

                                                    <div class="d-flex justify-content-between border-top pt-10 mt-15">
                                                        <div>
                                                            <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4" data-wizard-type="action-submit">Submit</button>
                                                        </div>
                                                    </div>
                                                    <!--end::Wizard Actions-->
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                    </div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>

@endsection
