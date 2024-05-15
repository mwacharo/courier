@extends('app-admin')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-2">
                    <!--begin::Page Title-->
                    <h5 class="text-warning font-weight-bold mt-2 mb-2 mr-5">Dashboard</h5>
                    <!--end::Page Title-->
                    <!--begin::Actions-->
                    <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-4 bg-gray-200"></div>
                    
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
                <!--begin::Row-->
                <div class="row">
                    <div class="col-lg-12">
                        <!--begin::Mixed Widget 1-->
                        <div class="card card-custom bg-gray-100 card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 bg-dark py-5">
                                <h5 class="card-title font-weight-bolder text-primary">BOXLEO {!! '&nbsp;' !!} <span
                                        class="text-warning"> {{ now()->year }} </span>{!! '&nbsp;' !!} STATS</h5>
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
                                            <span>
                                                <i class="fa-solid text-warning fa-cubes"></i>
                                            </span><br>
                                            <a href="admin/order-undispatched"
                                                class="text-primary font-weight-bold font-size-h6">Undispatched-
                                                {{ $undispatched_orders }}</a>
                                        </div>

                                        <div class="col bg-light-warning px-6 py-8 rounded-xl mr-7 mb-7">
                                            <span>
                                                <i class="fa-solid fa-person-biking text-info"></i>
                                            </span><br>
                                            <a href="admin/report-intransit"
                                                class="text-primary font-weight-bold font-size-h6 mt-2">In Transit -
                                                {{ $intransit_orders }}</a>
                                        </div>

                                        <div class="col bg-light-warning px-6 py-8 rounded-xl mr-7 mb-7">
                                            <span>
                                                <i class="fa-solid fa-check text-success"></i>
                                            </span><br>
                                            <a href="#"
                                                class="text-primary font-weight-bold font-size-h6 mt-2">Delivered -
                                                {{ $delivered_orders }}</a>
                                        </div>



                                        <div class="col bg-light-warning px-6 py-8 rounded-xl mb-7 mb-7">
                                            <span>
                                                <i class="fa-solid text-danger fa-calculator"></i>
                                            </span><br>
                                            <a href="#" class="text-primary font-weight-bold font-size-h6 mt-2">Total
                                                Orders- {{ $total_orders }}</a>
                                        </div>
                                    </div>
                                    <!--end::Row-->
                                    <!--begin::Row-->
                                    <div class="row m-0">
                                        <div class="col bg-light-success px-6 py-8 rounded-xl mr-7">
                                            <span>
                                                <i class="fa-solid text-primary fa-people-roof"></i>
                                            </span><br>

                                            <a href="#"
                                                class="text-warning font-weight-bold font-size-h6 mt-2">Merchants -
                                                {{ $total_merchants }}</a>
                                        </div>

                                        <div class="col bg-light-success px-6 py-8 rounded-xl mr-7">
                                            <span>
                                                <i class="fa-solid text-primary fa-table-list"></i>
                                            </span><br>

                                            <a href="#"
                                                class="text-warning font-weight-bold font-size-h6 mt-2">Scheduled-
                                                {{ $scheduled_orders }}</a>
                                        </div>

                                        <div class="col bg-light-success px-6 py-8 rounded-xl mr-7">
                                            <span>
                                                <i class="fa-solid fa-shopping-bag  text-primary "></i>
                                            </span><br>
                                            <a href=""
                                                class="text-warning font-weight-bold font-size-h6 mt-2">Dispatched -
                                                {{ $dispatched_orders }}</a>
                                        </div>



                                        <div class="col bg-light-success px-6 py-8 rounded-xl mr-7">
                                            <span>
                                                <i class="fa-solid text-primary fa-boxes-packing"></i>
                                            </span><br>

                                            <a href="#"
                                                class="text-warning font-weight-bold font-size-h6 mt-2">Inventory -
                                                {{ $total_inventories }}</a>
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
                <div class="row">
                    <div class="col-12 order-2 order-1">
                        <!--begin::Advance Table Widget 2-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bolder text-dark">New Orders</span>
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
                                                            class="text-dark-75 font-weight-bolder d-block font-size-lg">Sender</span>
                                                        <span
                                                            class="text-muted font-weight-500">{{ $order['sender_name'] }}</span>
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
                <!--begin::Row-->
                <div class="row">

                    <div class="col-4">
                        <!--begin::Mixed Widget 14-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bolder text-dark">Low Inventory Stock</h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-2">

                                @foreach ($low_stocks as $low_stock)
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
                                                class="text-dark-75 text-hover-primary font-weight-bold font-size-lg mb-1">{{ $low_stock->name }}</a>
                                            <span class="text-muted font-weight-bold">Inventory count
                                                {{ $low_stock->quantity }}</span>
                                        </div>
                                        <!--end::Text-->
                                    </div>
                                @endforeach
                                <!--end::Item-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Mixed Widget 14-->
                    </div>
                    <div class="col-lg-8">
                        <!--begin::Advance Table Widget 4-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 py-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bolder text-dark">Latest Inventory History</span>
                                    <span class="text-muted mt-3 font-weight-bold font-size-sm">Recent inscan & outscan
                                        actions</span>
                                </h3>
                                <div class="card-toolbar">
                                    <a href="{{ route('admin.order.inscan') }}"
                                        class="btn btn-info font-weight-bolder font-size-sm mr-3">Inscan</a>
                                    <a href="{{ route('admin.order.outscan') }}"
                                        class="btn btn-danger font-weight-bolder font-size-sm">Outscan</a>
                                </div>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-0 pb-3">
                                <div class="tab-content">
                                    <!--begin::Table-->
                                    <div class="table-responsive">
                                        <table
                                            class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                            <thead>
                                                <tr class="text-left text-uppercase">
                                                    <th style="min-width: 200px" class="pl-7">
                                                        <span class="text-dark-75">Item</span>
                                                    </th>
                                                    <th style="min-width: 100px">Merchant</th>
                                                    <th style="min-width: 100px">Amount</th>
                                                    <th style="min-width: 100px">Quantity</th>
                                                    <th style="min-width: 130px">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($inventory_histories as $inventory_history)
                                                    @if ($loop->iteration < 6)
                                                        <tr>
                                                            <td class="pl-0 py-8">
                                                                <div class="d-flex align-items-center">
                                                                    <div>
                                                                        <a href="#"
                                                                            class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $inventory_history['name'] }}</a>
                                                                        <span class="text-muted font-weight-bold d-block">
                                                                            {{ strtoupper($inventory_history['sku']) }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $inventory_history['merchant_name'] }}</span>
                                                                <span class="text-muted font-weight-bold"><a
                                                                        href="{{ route('admin.merchant.details', ['id' => $inventory_history['merchant_id']]) }}">View
                                                                        details</a></span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="text-dark-75 font-weight-bolder d-block font-size-lg">Kshs
                                                                    {{ $inventory_history['amount'] }}</span>
                                                                <span class="text-muted font-weight-bold">Paid</span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $inventory_history['quantity'] }}</span>
                                                                <span class="text-muted font-weight-bold">Bal
                                                                    {{ $inventory_history['balance'] }}</span>
                                                            </td>
                                                            <td>
                                                                {{ $inventory_history['transaction_type'] }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--end::Table-->
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Advance Table Widget 4-->
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
