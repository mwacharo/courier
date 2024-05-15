@extends('app-merchant')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-2">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Dashboard</h5>
                    <!--end::Page Title-->
                    <!--begin::Actions-->
                    <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-4 bg-gray-200"></div>
                    <span class="text-muted font-weight-bold mr-4"><a href="mailto:support@boxleocourier.com">Need Help?
                            Contact Support</a></span>
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
                <!--begin::Dashboard-->

                @if ($inventory_low_count > 0)
                    <div class="alert alert-warning">Hi, {{ Auth::user()->name }}, you have ({{ $inventory_low_count }}) low
                        inventory stock items. Kindly check below for more action</div>
                @endif
                <!--begin::Row-->
                <div class="row">
                    <div class="col-lg-12 col-4">
                        <!--begin::Mixed Widget 1-->
                        <div class="card card-custom bg-gray-100 card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 bg-dark py-5">
                                <h3 class="card-title font-weight-bolder text-white">Boxleo Stats</h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body p-0 position-relative overflow-hidden">
                                <!--begin::Chart-->
                                <div class="card-rounded-bottom bg-dark mb-6" style="height: 300px">
                                    <div style="height: 260px">
                                        <canvas id="myChart" style="height: 100px;"></canvas>
                                    </div>

                                </div>
                                <!--end::Chart-->
                                <!--begin::Stats-->
                                <div class="card-spacer mt-n25">
                                    <!--begin::Row-->
                                    <div class="row m-0">
                                        <div class="col bg-light-warning px-6 py-8 rounded-xl mr-7 mb-7">
                                            <span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <rect fill="#000000" opacity="0.3" x="13" y="4"
                                                            width="3" height="16" rx="1.5" />
                                                        <rect fill="#000000" x="8" y="9" width="3"
                                                            height="11" rx="1.5" />
                                                        <rect fill="#000000" x="18" y="11" width="3"
                                                            height="9" rx="1.5" />
                                                        <rect fill="#000000" x="3" y="13" width="3"
                                                            height="7" rx="1.5" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <a href="#" class="text-dark font-weight-bold font-size-h6">Pending -
                                                {{ $pending_orders }} ({{ $pending_orders_percentage }}%)</a>
                                        </div>
                                        <div class="col bg-light-success px-6 py-8 rounded-xl mr-7 mb-7">
                                            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <rect fill="#000000" opacity="0.3" x="13" y="4"
                                                            width="3" height="16" rx="1.5" />
                                                        <rect fill="#000000" x="8" y="9" width="3"
                                                            height="11" rx="1.5" />
                                                        <rect fill="#000000" x="18" y="11" width="3"
                                                            height="9" rx="1.5" />
                                                        <rect fill="#000000" x="3" y="13"
                                                            width="3" height="7" rx="1.5" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <a href="#"
                                                class="text-dark font-weight-bold font-size-h6 mt-2">Delivered -
                                                {{ $delivered_orders }} ({{ $delivered_orders_percentage }}%)</a>
                                        </div>
                                        <div class="col bg-light-danger px-6 py-8 rounded-xl mr-7 mb-7">
                                            <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24"
                                                            height="24" />
                                                        <rect fill="#000000" opacity="0.3" x="13"
                                                            y="4" width="3" height="16"
                                                            rx="1.5" />
                                                        <rect fill="#000000" x="8" y="9"
                                                            width="3" height="11" rx="1.5" />
                                                        <rect fill="#000000" x="18" y="11"
                                                            width="3" height="9" rx="1.5" />
                                                        <rect fill="#000000" x="3" y="13"
                                                            width="3" height="7" rx="1.5" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <a href="#"
                                                class="text-dark font-weight-bold font-size-h6 mt-2">Cancelled -
                                                {{ $cancelled_orders }} ({{ $cancelled_orders_percentage }}%)</a>
                                        </div>
                                        <div class="col bg-light-info px-6 py-8 rounded-xl mb-7 mb-7">
                                            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24"
                                                            height="24" />
                                                        <rect fill="#000000" opacity="0.3" x="13"
                                                            y="4" width="3" height="16"
                                                            rx="1.5" />
                                                        <rect fill="#000000" x="8" y="9"
                                                            width="3" height="11" rx="1.5" />
                                                        <rect fill="#000000" x="18" y="11"
                                                            width="3" height="9" rx="1.5" />
                                                        <rect fill="#000000" x="3" y="13"
                                                            width="3" height="7" rx="1.5" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <a href="#"
                                                class="text-dark font-weight-bold font-size-h6 mt-2">Scheduled -
                                                {{ $scheduled_orders }} ({{ $scheduled_orders_percentage }}%)</a>
                                        </div>
                                    </div>

                                    <div class="row m-0">
                                        <div class="col bg-light-primary px-6 py-8 rounded-xl mr-7 mb-7">
                                            <span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24"
                                                            height="24" />
                                                        <rect fill="#000000" opacity="0.3" x="13"
                                                            y="4" width="3" height="16"
                                                            rx="1.5" />
                                                        <rect fill="#000000" x="8" y="9"
                                                            width="3" height="11" rx="1.5" />
                                                        <rect fill="#000000" x="18" y="11"
                                                            width="3" height="9" rx="1.5" />
                                                        <rect fill="#000000" x="3" y="13"
                                                            width="3" height="7" rx="1.5" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <a href="#" class="text-info font-weight-bold font-size-h6">Dispatched -
                                                {{ $dispatched_orders }} ({{ $dispatched_orders_percentage }}%)</a>
                                        </div>
                                        <div class="col bg-success-o-20 px-6 py-8 rounded-xl mr-7 mb-7">
                                            <span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24"
                                                            height="24" />
                                                        <rect fill="#000000" opacity="0.3" x="13"
                                                            y="4" width="3" height="16"
                                                            rx="1.5" />
                                                        <rect fill="#000000" x="8" y="9"
                                                            width="3" height="11" rx="1.5" />
                                                        <rect fill="#000000" x="18" y="11"
                                                            width="3" height="9" rx="1.5" />
                                                        <rect fill="#000000" x="3" y="13"
                                                            width="3" height="7" rx="1.5" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <a href="#" class="text-info font-weight-bold font-size-h6">In Transit -
                                                {{ $inTransit_orders }} ({{ $inTransit_orders_percentage }}%)</a>
                                        </div>
                                        <div class="col bg-light-primary px-6 py-8 rounded-xl mr-7 mb-7">
                                            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24"
                                                            height="24" />
                                                        <rect fill="#000000" opacity="0.3" x="13"
                                                            y="4" width="3" height="16"
                                                            rx="1.5" />
                                                        <rect fill="#000000" x="8" y="9"
                                                            width="3" height="11" rx="1.5" />
                                                        <rect fill="#000000" x="18" y="11"
                                                            width="3" height="9" rx="1.5" />
                                                        <rect fill="#000000" x="3" y="13"
                                                            width="3" height="7" rx="1.5" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <a href="#"
                                                class="text-primary font-weight-bold font-size-h6 mt-2">Delivered Pending -
                                                {{ $delivery_pending_orders }}
                                                ({{ $delivery_pending_orders_percentage }}%)</a>
                                        </div>
                                        <div class="col bg-light-dark px-6 py-8 rounded-xl mr-7 mb-7">
                                            <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24"
                                                            height="24" />
                                                        <rect fill="#000000" opacity="0.3" x="13"
                                                            y="4" width="3" height="16"
                                                            rx="1.5" />
                                                        <rect fill="#000000" x="8" y="9"
                                                            width="3" height="11" rx="1.5" />
                                                        <rect fill="#000000" x="18" y="11"
                                                            width="3" height="9" rx="1.5" />
                                                        <rect fill="#000000" x="3" y="13"
                                                            width="3" height="7" rx="1.5" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <a href="#"
                                                class="text-info font-weight-bold font-size-h6 mt-2">Returned -
                                                {{ $returned_orders }} ({{ $returned_orders_percentage }}%)</a>
                                        </div>
                                        <div class="col bg-light-primary px-6 py-8 rounded-xl mb-7 mb-7">
                                            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24"
                                                            height="24" />
                                                        <rect fill="#000000" opacity="0.3" x="13"
                                                            y="4" width="3" height="16"
                                                            rx="1.5" />
                                                        <rect fill="#000000" x="8" y="9"
                                                            width="3" height="11" rx="1.5" />
                                                        <rect fill="#000000" x="18" y="11"
                                                            width="3" height="9" rx="1.5" />
                                                        <rect fill="#000000" x="3" y="13"
                                                            width="3" height="7" rx="1.5" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <a href="#" class="text-info font-weight-bold font-size-h6 mt-2">Total -
                                                {{ $total_orders }}</a>
                                        </div>
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Stats-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Mixed Widget 1-->
                    </div>


                </div>
                <!--end::Row-->

                <!--begin::Row-->
                <div class="row">
                    <div class="col-lg-3">
                        <!--begin::Mixed Widget 14-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bolder text-dark">Low Inventory Stock</h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-2">

                                @foreach ($inventories as $inventory)
                                    @if ($inventory->quantity < $inventory->low_count)
                                        <div class="d-flex align-items-center mb-2">
                                            <!--begin::Bullet-->
                                            <span class="bullet bullet-bar bg-danger align-self-stretch"></span>
                                            <!--end::Bullet-->
                                            <!--begin::Checkbox-->
                                            <label
                                                class="checkbox checkbox-lg checkbox-light-danger checkbox-single flex-shrink-0 m-0 mx-4">
                                                <input type="checkbox" value="1" />
                                                <span></span>
                                            </label>
                                            <!--end::Checkbox:-->
                                            <!--begin::Title-->
                                            <div class="d-flex flex-column flex-grow-1">
                                                <a href="#"
                                                    class="text-dark-75 text-hover-primary font-weight-bold font-size-lg mb-1">{{ $inventory->name }}</a>
                                                <span class="text-muted font-weight-bold">Inventory count
                                                    {{ $inventory->quantity }}</span>
                                            </div>
                                            <!--end::Text-->
                                        </div>
                                    @endif
                                @endforeach
                                <!--end::Item-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Mixed Widget 14-->
                    </div>
                    <div class="col-lg-9">
                        <!--begin::Advance Table Widget 2-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bolder text-dark">Recent Orders</span>
                                    <span class="text-muted mt-3 font-weight-bold font-size-sm">Recent orders made by
                                        customers</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-3 pb-0">
                                <!--begin::Table-->
                                <div class="table-responsive">
                                    <table class="table table-borderless table-vertical-center">
                                        <thead>
                                            <tr>
                                                <th class="p-0"></th>
                                                <th class="p-0"></th>
                                                <th class="p-0"></th>
                                                <th class="p-0"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                <tr>
                                                    <td>
                                                        <span
                                                            class="text-dark-75 font-weight-bolder d-block font-size-lg">Order
                                                            No</span>
                                                        <span
                                                            class="text-danger text-muted font-weight-500">{{ $order['order_no'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-dark-75 font-weight-bolder d-block font-size-lg">Receiver</span>
                                                        <span
                                                            class="text-muted font-weight-500">{{ $order['receiver_name'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-dark-75 font-weight-bolder d-block font-size-lg">Address</span>
                                                        <span
                                                            class="text-muted font-weight-500">{{ $order['receiver_address'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-dark-75 font-weight-bolder d-block font-size-lg">Status</span>
                                                        <span
                                                            class="label label-lg label-light-primary label-inline">{{ $order['order_status'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-dark-75 font-weight-bolder d-block font-size-lg">Date</span>
                                                        <span
                                                            class="text-muted font-weight-500">{{ $order['created_at'] }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!--end::Table-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Advance Table Widget 2-->
                    </div>
                </div>
                <!--end::Row-->
                <!--end::Dashboard-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
@endsection
