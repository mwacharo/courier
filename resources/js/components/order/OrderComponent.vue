<template>
    <div>
        <div class="d-flex justify-content-center">
            <div class="col-8 alert alert-warning text-center" role="alert" v-if="data_error">No Record Found!!</div>
        </div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="row justify-content-center">
                <div class="col-xl-11">

                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Order No:</label>
                                    <input class="form-control form-control-solid form-control-lg" name="sku" type="text" v-model="search_order_no"/>
                                </div>
                                <div class="col-lg-4">
                                    <label>Merchant Details:</label>
                                    <select class="form-control" v-model="search_merchant_id">
                                        <option value="all">All</option>
                                        <option v-for="merchant in merchants" :value="merchant.id">{{ merchant.name }}</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label>Date:</label>
                                    <select class="form-control" v-model="order_date" @change.prevent="selectedDate">
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
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Recipient Name:</label>
                                    <input class="form-control form-control-solid form-control-lg" name="search_recipient_name" type="text" v-model="search_recipient_name"/>
                                </div>
                                <div class="col-lg-4">
                                    <label>Order Status:</label>
                                    <select class="form-control" v-model="search_order_status">
                                        <option value="all">All</option>
                                        <option value="order_pending">Pending Confirmation</option>
                                        <option value="scheduled">Scheduled</option>
                                        <option value="awaiting_dispatch">Awaiting Dispatch</option>
                                        <option value="not_scanned">Not Scanned</option>
                                        <option value="dispatched">Dispatched</option>
                                        <option value="undispatched">Undispatched</option>
                                        <option value="in_transit">In Transit</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="returned">Returned</option>
                                        <option value="expired">Expired</option>
                                        <option value="out_of_stock">Out of stock</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label>Payment Status:</label>
                                    <select class="form-control" v-model="search_payment_status">
                                        <option value="all">All</option>
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Town:</label>
                                    <select class="form-control" v-model="search_town">
                                        <option value="all">All</option>
                                        <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label>Destination Type:</label>
                                    <select class="form-control" v-model="search_destination_type">
                                        <option value="all">All</option>
                                        <option value="1">Outbound Delivery</option>
                                        <option value="2">Inbound Delivery</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label>Agent Details:</label>
                                    <select class="form-control" v-model="search_agent_id">
                                        <option value="all">All</option>
                                        <option v-for="agent in agents" :value="agent.client_name">{{ agent.client_name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <label>Recipient Phone:</label>
                                    <input class="form-control form-control-solid form-control-lg" name="search_recipient_phone" type="text" v-model="search_recipient_phone"/>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-8">

                                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                    <div class="spinner-border text-primary" role="status" v-if="loader">
                                      <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </form>

        <table class="table table-bordered table-hover table-checkable" id="order_table" style="margin-top: 13px !important">
            <thead>
            <tr>
                <th colspan="2">Order</th>
                <th colspan="4">Shipping Information</th>
                <th colspan="1">Order Status</th>
                <th colspan="2">Actions</th>
            </tr>
            <tr>
                <th>#</th>
                <th>Order No</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Receiver phone & address</th>
                <th>Product</th>
                <th>Order Status</th>
                <th>Special Instructions</th>
                <th>Scheduled Date</th>
                <th>Status Date</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(order, index) in orders">
                <td>{{ index + 1 }}</td>
                <td>{{ order.order_no }}</td>
                <td>{{ order.sender_name }}</td>
                <td>{{ order.receiver_name }} <br><a href="#" @click.prevent="selectReceiverDetails(order)" data-toggle="modal" data-target="#receiverModal">(Edit details)</a></td>
                <td>
                    {{ order.receiver_address }} <br><a href="#" @click.prevent="selectLocationDetails(order)" data-toggle="modal" data-target="#locationModal">(Edit details)</a>
                    <br>
                    {{ order.receiver_phone }} <a href="#" class="text-danger" @click.prevent="makeCall(order.receiver_phone)">(Call)</a>
                    <br>
                    <span v-if="order.receiver_phone_alternative != null"> {{ order.receiver_phone_alternative }} <a href="#" class="text-danger" @click.prevent="makeCall(order.receiver_phone_alternative)">Alt (Call)</a></span>
                    <br>
                    <br>
                    Call Details
                    <br>
                    Agent: {{ order.agent_name }} <a href="#" @click.prevent="selectAgentDetails(order)" data-toggle="modal" data-target="#agentModal" v-if="call_agent_schedule === '1'">(Edit)</a>
                </td>
                <td>
                        <span v-for="item in order.items"> {{ item.description }} - Qty ({{ item.quantity }}) - </span> <br>
                        Amount: Kshs ({{ order.amount }})

                        <br><a href="#" @click.prevent="selectItemDetails(order)" data-toggle="modal" data-target="#itemModal">(Edit details)</a>
                </td>
                <td>

                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-if="order.order_status == 'order_pending'" class="text-warning">Order pending</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'scheduled'" class="text-info">Scheduled</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'awaiting_dispatch'" class=" text-info">Awaiting Dispatch</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'undispatched'">undispatched</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'dispatched'" class=" text-primary">Dispatched</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'in_transit'" class="text-info">In Transit</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'delivered'" class="text-success">Delivered</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'returned'">Returned</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'cancelled'" class="text-danger">Cancelled</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'not_dispatched'">Not Dispatched</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'expired'">Expired</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else-if="order.order_status == 'out_of_stock'">Out of stock</a>
                    <a href="#" @click.prevent="selectOrderStatus(order)" data-toggle="modal" data-target="#statusModal" v-else >{{order.order_status}}</a>
                </td>
                <td>
                    <a href="#" @click.prevent="selectSpecialInstruction(order)" data-toggle="modal" data-target="#specialInstructionModal" v-if="!order.special_instruction || order.special_instruction == 'null'">Click to add</a>
                    <a href="#" @click.prevent="selectSpecialInstruction(order)" data-toggle="modal" data-target="#specialInstructionModal" v-else>{{ order.special_instruction }}</a>
                </td>
                <td>
                    <a href="#" @click.prevent="selectScheduledDate(order)" data-toggle="modal" data-target="#scheduledDateModal">{{ order.scheduled_date }}</a>
                </td>
                <td v-if="change_order_status_date === '1'">
                    <a href="#" @click.prevent="selectStatusDate(order)" data-toggle="modal" data-target="#statusDateModal" v-if="order.status_date">{{ order.status_date }}</a>
                    <a href="#" @click.prevent="selectStatusDate(order)" data-toggle="modal" data-target="#statusDateModal" v-else>Add Status Date</a>
                </td>
                <td v-else>
                    {{ order.status_date }}
                </td>
                <td nowrap="nowrap">

                    <span class="text-success" v-if="order.print_count > 0">Print count ({{ order.print_count }})</span>
                    <span class="text-warning" v-else>Unprinted</span>
                    <br>

                    <a :href="base_url_web_admin+'order-details/'+order.id">View details</a>
                    <a :href="base_url_web_admin+'order-assign-rider/'+order.id" class="text-warning" v-if="order.is_sender_merchant == 0"><i class="flaticon2-delivery-truck text-warning"></i></a>
                    <a :href="base_url_web_admin+'order-assign-rider/'+order.id" class="text-warning" v-if="order.is_sender_merchant == 1 && order.rider_id"><i class="flaticon2-delivery-truck text-warning"></i></a>
                    <a :href="base_url_web_admin+'order-waybill/'+order.id" v-if="admin_role == 3 || admin_role==7" class="text-danger"><i class="flaticon2-printer text-danger"></i></a>
                </td>
            </tr>
            </tbody>

        </table>

        <div class="modal fade" id="statusModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="formSubmitChangeStatus" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="statusModalLabel">Order Status</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="closeStatusModal">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="alert alert-success" role="alert" v-if="alert_success">Order status updated!</div>
                                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating status!</div>

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <div class="form-group row" >
                                            <label class="col-xl-4 col-lg-4 col-form-label">Order Status</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="order_status" type="text" v-model="order_status">
                                                    <option v-if="admin_role==4" value="order_pending">Pending Confirmation</option>
                                                    <option v-if="admin_role==4 || admin_role==3" value="scheduled">Scheduled</option>
                                                    <option v-if="admin_role==7 || admin_role==3" value="not_dispatched">Not dispatched</option>
                                                    <option v-if="admin_role==5 || admin_role==7" value="delivered">Delivered</option>
                                                    <option v-if="admin_role==1 || admin_role==2" value="dispatched">Dispatched</option>
                                                    <option v-if="admin_role==1 || admin_role==2" value="undispatched">Undispatched</option>
                                                    <option v-if="admin_role==1 || admin_role==2" value="in_transit">In Transit</option>
                                                    <option v-if="admin_role==3 || admin_role==4" value="cancelled">Cancelled</option>
                                                    <option v-if="admin_role==5 || admin_role==7" value="returned">Returned</option>
                                                    <option v-if="admin_role==3" value="expired">Expired</option>
                                                    <option v-if="admin_role==3 || admin_role==4" value="out_of_stock">Out of stock</option>

                                                </select>

                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="order_status=='order_pending'">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Status Reason</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="status_reason" type="text" v-model="status_reason">
                                                    <option value="">Select Reason</option>
                                                    <option value="not_picking">Not Picking</option>
                                                    <option value="not_available">Not available</option>
                                                    <option value="will_call_later_date">Will call at later date</option>
                                                    <option value="wrong_order">Wrong Order</option>
                                                    <option value="hanged_up">Hanged Up</option>
                                                    <option value="silent_on_call">Silent On Call</option>
                                                    <option value="call_later_driving">Call later is driving</option>
                                                    <option value="call_later_meeting">Call later in a meeting</option>
                                                    <option value="line_busy">Line busy</option>
                                                    <option value="call_later_noisy_place">Call later in a noisy place</option>
                                                    <option value="call_tomorrow">Call back tomorrow</option>
                                                    <option value="custom_reason">Custom Reason</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="order_status=='delivery_pending' || order_status=='returned' || order_status=='cancelled'">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Status Reason</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="status_reason" type="text" v-model="status_reason">
                                                    <option value="">Select Reason</option>
                                                    <option value="product_too_expensive">Product too expensive</option>
                                                    <option value="product_too_small">Product too small</option>
                                                    <option value="order_took_long">Order took long</option>
                                                    <option value="got_alternative">Got Alternative</option>
                                                    <option value="will_reorder_financially">Will reorder when financially ready</option>
                                                    <option value="wrong_number">Wrong number</option>
                                                    <option value="incomplete_number">Incomplete number</option>
                                                    <option value="didnt_place_order">Didnt place order</option>
                                                    <option value="duplicate_order">Duplicate order</option>
                                                    <option value="changed_mind">Changed my mind</option>
                                                    <option value="excess_number">Excess number</option>
                                                    <option value="was_only_inquiring">Was only inquring</option>
                                                    <option value="custom_reason">Custom Reason</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="status_reason=='custom_reason'">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Custom Reason</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <input class="form-control form-control-solid form-control-lg" name="custom_reason" type="text" v-model="custom_reason"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="order_status=='scheduled' && is_sender_merchant==1">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Scheduled Date</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <input class="form-control form-control-solid form-control-lg" name="scheduled_date"
                                                       type="date" v-model="scheduled_date"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <!-- <div class="form-group row" v-if="order_status=='delivered' && is_sender_merchant==1">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Mpesa code</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <input class="form-control form-control-solid form-control-lg" name="mpesa_code"
                                                       type="text" v-model="mpesa_code"/>
                                            </div>
                                        </div> -->
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="closeStatusModal">Close</button>
                            <button type="submit" class="btn btn-danger font-weight-bold">Update changes</button>
                            <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="specialInstructionModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="formSubmitSpecialInstruction" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="specialInstructionModalLabel">Special Instruction</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="closeSpecialInstructionModal">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="alert alert-success" role="alert" v-if="alert_success">Order status updated!</div>
                                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating status!</div>

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Special Instruction</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <textarea rows="4" class="form-control form-control-solid form-control-lg" name="special_instruction" v-model="special_instruction"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="closeSpecialInstructionModal">Close</button>
                            <button type="submit" class="btn btn-danger font-weight-bold">Update changes</button>
                            <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="scheduledDateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="formSubmitScheduledDate" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="scheduledDateModalModalLabel">Scheduled Date</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="closeScheduledDateModal">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="alert alert-success" role="alert" v-if="alert_success">Order status updated!</div>
                                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating status!</div>

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Scheduled Date</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <input class="form-control form-control-solid form-control-lg" name="scheduled_date" type="date" v-model="scheduled_date"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="closeScheduledDateModal">Close</button>
                            <button type="submit" class="btn btn-danger font-weight-bold">Update changes</button>
                            <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>



        <div class="modal fade" id="statusDateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="formSubmitStatusDate" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="statusDateModalModalLabel">Status Date</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="closeStatusDateModal">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="alert alert-success" role="alert" v-if="alert_success">Order status updated!</div>
                                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating status!</div>

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Status Date</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <input class="form-control form-control-solid form-control-lg" name="status_date" type="date" v-model="status_date"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="closeStatusDateModal">Close</button>
                            <button type="submit" class="btn btn-danger font-weight-bold">Update changes</button>
                            <div class="spinner-border spinner-border" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="receiverModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="formSubmitReceiver" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="receiverModalLabel">Receiver Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="closeReceiverModal">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="alert alert-success" role="alert" v-if="alert_success">Order status updated!</div>
                                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating status!</div>

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Name</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <input class="form-control form-control-solid form-control-lg" name="receiver_name"
                                                    type="text" v-model="receiver_name"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="closeReceiverModal">Close</button>
                            <button type="submit" class="btn btn-danger font-weight-bold">Update changes</button>
                            <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="itemModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="formSubmitItem" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="itemModalLabel">Item Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="closeItemModal">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="alert alert-success" role="alert" v-if="alert_success">Order status updated!</div>
                                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating status!</div>


                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <div class="form-group row">
                                            <label class="col-4 col-form-label">Upsell</label>
                                            <div class="col-4">
                                                <span class="switch">
                                                    <label>
                                                        <input type="checkbox" name="select" v-model="upsell"/>
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="closeItemModal">Close</button>
                            <button type="submit" class="btn btn-danger font-weight-bold">Update changes</button>
                            <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="locationModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="formSubmitLocation" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="locationModalLabel">Location & Invoice Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="closeLocationModal">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="alert alert-success" role="alert" v-if="alert_success">Order status updated!</div>
                                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating status!</div>

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Destination Type</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="destination_type"
                                                        type="text" v-model="destination_type" >
                                                    <option value="1">Outbound Delivery (Out of Nairobi)</option>
                                                    <option value="2">Inbound Delivery</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Service Type</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="service_type"
                                                        type="text" v-model="service_type" >
                                                    <option value="">Select Service</option>
                                                    <option value="1" v-if="destination_type==2">Same-Day Delivery</option>
                                                    <option value="2">Overnight Delivery</option>
                                                    <option value="3">Pickup Delivery</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="destination_type==2">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Inbound Rate Type</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="inbound_rate_type"
                                                        type="text" v-model="inbound_rate_type" >
                                                    <option value="">Select Inbound Rate</option>
                                                    <option value="1">On-demand delivery charges</option>
                                                    <option value="2">Zone delivery charges</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="inbound_rate_type==1">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Approximate Distance (KM)</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <input class="form-control form-control-solid form-control-lg" name="delivery_distance"
                                                       type="number" min="0" onwheel="this.blur()" v-model="delivery_distance"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="service_type==3">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Pickup Country</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="pickup_country">
                                                    <option value="">Select Pickup Country</option>
                                                    <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="service_type==3">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Pickup Town</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="pickup_town">
                                                    <option value="">Select Pickup Town</option>
                                                    <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="service_type==3">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Pickup Address</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <input class="form-control form-control-solid form-control-lg" name="pickup_address"
                                                       type="text" v-model="pickup_address"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="destination_type==1">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Country</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="name" type="text"
                                                        v-model="receiver_country">
                                                    <option value="">Select Country</option>
                                                    <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="destination_type==1">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Town</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="receiver_town" >
                                                    <option value="">Select Town</option>
                                                    <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Address</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <input class="form-control form-control-solid form-control-lg" name="receiver_address"
                                                       type="text" v-model="receiver_address"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="destination_type==2 && inbound_rate_type==2">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Receiver Zone</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="zone_id"
                                                        type="text" v-model="zone_id" >
                                                    <option value="">Select Zone</option>
                                                    <option v-for="zone in zones" :value="zone.id">{{ zone.zone }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Total Amount <br>
                                                <a href="#" @click.prevent="calculateDeliveryCharge" class="text-danger">(Calculate)</a>
                                            </label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="total_amount " type="number" min="0" onwheel="this.blur()" v-model="total_amount"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="closeStatusDateModal">Close</button>
                            <button type="submit" class="btn btn-danger font-weight-bold">Update changes</button>
                            <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="agentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="form" @submit.prevent="formSubmitAgent" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="agentModalLabel">Agent Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="closeAgentModal">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-xl-10">
                                    <!--begin::Wizard Step 1-->

                                    <div class="alert alert-success" role="alert" v-if="alert_success">Order status updated!</div>
                                    <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating status!</div>

                                    <div class="my-5 step" data-wizard-type="step-content" data-wizard-state="current">

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Agent Details</label>
                                            <div class="col-lg-8 col-lg-8">
                                                <select class="form-control form-control-solid form-control-lg" name="destination_type" type="text" v-model="agent" >
                                                    <option value="">Select Agent</option>
                                                    <option v-for="agent in agents" :value="agent.client_name">{{ agent.client_name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="closeStatusDateModal">Close</button>
                            <button type="submit" class="btn btn-danger font-weight-bold">Update changes</button>
                            <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </form>
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

    const CancelToken = axios.CancelToken;
    const order_source = CancelToken.source();
    const filter_source = CancelToken.source();

    export default {

        mounted(){
            this.fetchOrders ();
            this.fetchCountries();
            this.fetchTowns();
            this.fetchZones();
            this.fetchAgents();
            this.fetchAdmins();
            this.fetchMerchants();
            this.fetchMpesaCodes();

        },

        props: {
            change_order_status_date: String,
            call_agent_schedule: String,
            causer_id: String,
            admin_role: Number,
        },

        data(){
            return{
                orders: [],
                mpesa_codes: [],
                agents: [],
                admins: [],

                order_id: '',
                order_status: 'order_pending',
                status_reason: '',
                custom_reason: '',
                scheduled_date: '',
                mpesa_code: '',
                status_date: '',
                is_sender_merchant: '',
                special_instruction: '',
                merchant_id: '',
                zone_id: '',
                pickup_country: '',
                pickup_town: '',
                pickup_address: '',
                sender_country: '',
                sender_town: '',
                receiver_country: '',
                receiver_town: '',
                receiver_address: '',
                receiver_name: '',
                destination_type: '1',
                delivery_distance: '',
                service_type: '',
                inbound_rate_type: '',
                upsell: false,
                agent: '',
                items: [],
                total_weight: 0,
                total_amount: 0,

                order_date: 'all',
                custom_date: '',
                custom_start_date: '',
                custom_end_date: '',
                client_type: 'all',
                search_service_type: 'all',
                search_order_status: 'all',
                search_payment_status: 'all',
                search_merchant_id: 'all',
                search_destination_type: 'all',
                search_town: 'all',
                search_order_no: '',
                search_recipient_name: '',
                search_recipient_phone: '',
                search_agent_id: 'all',

                base_url_web_admin: '',

                countries: [],
                towns: [],
                zones: [],
                merchants: [],

                alert_error:false,
                alert_success:false,
                loader:false,
                data_error:false,

            }
        },

        methods: {

            fetchOrders (){
                let uri = base_url+`v1/order-list`;
                axios.get(uri, {
                cancelToken: order_source.token
                }).then((response) => {
                        this.orders = response.data;
                        this.myTable();
                    });
                },
            fetchMpesaCodes (){
            let uri = base_url+`v1/mpesa-codes`;
            axios.get(uri, {

            }).then((response) => {
                    this.mpesa_codes = response.data;
                    this.myTable();
                });
            },

            fetchCountries() {
                let uri = base_url + `v1/country-list`;
                axios.get(uri).then((response) => {
                    this.countries = response.data;
                });
            },

            fetchTowns() {
                let uri = base_url + `v1/town-list`;
                axios.get(uri).then((response) => {
                    this.towns = response.data;
                });
            },

            fetchZones() {
                let uri = base_url + `v1/zone-list`;
                axios.get(uri).then((response) => {
                    this.zones = response.data;
                });
            },

            fetchMerchants() {
                let uri = base_url + `v1/merchant-list`;
                axios.get(uri).then((response) => {
                    this.merchants = response.data;

                });
            },

            fetchAgents() {
                let uri = base_url + `v1/call-agent-list`;
                axios.get(uri).then((response) => {
                    this.agents = response.data;
                });
            },
            fetchAdmins() {
                let uri = base_url + `v1/admin-list`;
                axios.get(uri).then((response) => {
                    this.admins = response.data;
                });
            },

            myTable(){
                $(document).ready( function () {
                    $('#order_table').DataTable();
                });
            },

            selectLocationDetails(order){

                this.alert_error = false;
                this.alert_success = false;
                this.loader = false;

                this.order_id = order.id;
                if(order.is_sender_merchant === 1){
                    this.is_sender_merchant = true;
                }
                this.merchant_id = order.merchant_id;
                this.destination_type = order.destination_type;
                this.zone_id = order.zone_id;
                this.delivery_distance = order.delivery_distance;
                this.service_type = order.service_type;
                this.inbound_rate_type = order.inbound_rate_type;
                this.sender_country = order.sender_country;
                this.sender_town = order.sender_town;
                this.receiver_country = order.receiver_country;
                this.receiver_town = order.receiver_town;
                this.receiver_address = order.receiver_address;
                this.pickup_country = order.pickup_country;
                this.pickup_town = order.pickup_town;
                this.pickup_address = order.pickup_address;
                this.total_amount = order.amount;

                this.fetchOrderItems();

            },

            closeLocationModal(){

                this.order_id = "";
                this.is_sender_merchant = "";
                this.merchant_id = "";
                this.destination_type = "";
                this.zone_id = "";
                this.delivery_distance = "";
                this.service_type = "";
                this.inbound_rate_type = "";
                this.sender_country = "";
                this.sender_town = "";
                this.receiver_country = "";
                this.receiver_town = "";
                this.receiver_address = "";
                this.pickup_country = "";
                this.pickup_town = "";
                this.pickup_address = "";
                this.total_amount = "";
                this.items = [];
            },

            selectItemDetails(order){

                this.alert_error = false;
                this.alert_success = false;
                this.loader = false;

                this.order_id = order.id;
                if(order.upsell === 1){
                    this.upsell = true;
                }
                this.fetchOrderItems();

            },

            closeItemModal(){
                this.order_id = "";
                this.upsell = false;
                this.items = [];
            },

            selectReceiverDetails(order){

                this.alert_error = false;
                this.alert_success = false;
                this.loader = false;

                this.order_id = order.id;
                this.receiver_name = order.receiver_name;
                this.fetchOrderItems();

            },

            closeReceiverModal(){
                this.order_id = "";
                this.receiver_name = "";
            },

            selectAgentDetails(order){

                this.alert_error = false;
                this.alert_success = false;
                this.loader = false;

                this.order_id = order.id;
                this.agent = order.agent;
                this.fetchOrderItems();

            },

            closeAgentModal(){
                this.order_id = "";
                this.agent = "";
            },


            selectOrderStatus(order){

                this.alert_error = false;
                this.alert_success = false;
                this.loader = false;

                this.order_id = order.id;
                this.order_status = order.order_status;
                this.status_reason = order.status_reason;
                this.custom_reason = order.custom_reason;
                this.scheduled_date = order.scheduled_date;
                this.is_sender_merchant = order.is_sender_merchant;
            },

            selectSpecialInstruction(order){

                this.alert_error = false;
                this.alert_success = false;
                this.loader = false;

                this.order_id = order.id;
                this.special_instruction = order.special_instruction;
            },

            selectScheduledDate(order){

                this.alert_error = false;
                this.alert_success = false;
                this.loader = false;

                this.order_id = order.id;
                this.scheduled_date = order.scheduled_date;
            },

            selectStatusDate(order){

                this.alert_error = false;
                this.alert_success = false;
                this.loader = false;

                this.order_id = order.id;
                this.status_date = order.status_date;
            },

            closeStatusModal(){
                this.order_id = "";
                this.order_status = "order_pending";
                this.status_reason = "";
                this.custom_reason = "";
                this.scheduled_date = "";
                this.is_sender_merchant = "";
            },

            closeSpecialInstructionModal(){
                this.order_id = "";
                this.special_instruction = "";
            },

            closeScheduledDateModal(){
                this.order_id = "";
                this.scheduled_date = "";
            },

            closeStatusDateModal(){
                this.order_id = "";
                this.status_date = "";
            },

            fetchOrderItems() {
                let uri = base_url+`v1/order-items/`+this.order_id;
                axios.get(uri).then((response) => {
                    this.items = response.data;
                });
            },


            selectedDate(){
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";

                if(this.order_date == 'custom_date'){
                    $('#modalCustomDate').modal('show');
                }else if(this.order_date == 'custom_range'){
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
                this.order_date = "all";
                this.custom_date = "";
                this.custom_start_date = "";
                this.custom_end_date = "";
            },


            formSubmit(){

                this.loader=true;
                this.data_error=false;
                order_source.cancel('Operation canceled by the user.');
                this.orders = [];
                var order_date = this.order_date;
                var custom_date = this.custom_date;
                var custom_start_date = this.custom_start_date;
                var custom_end_date = this.custom_end_date;
                var merchant_id = this.search_merchant_id;
                var destination_type = this.search_destination_type;
                var recipient_name = this.search_recipient_name;
                var recipient_phone = this.search_recipient_phone;
                var order_status = this.search_order_status;
                var payment_status = this.search_payment_status;
                var order_no = this.search_order_no;
                var town_id = this.search_town;
                var agent_id = this.search_agent_id;

                if(order_date == 'custom_date'){

                    if(this.custom_date == ''){
                        alert("Custom date not selected");
                        return;
                    }

                }

                if(order_date == 'custom_range'){

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
                formData.append( 'order_date', order_date);
                formData.append( 'custom_date', custom_date);
                formData.append( 'custom_start_date', custom_start_date);
                formData.append( 'custom_end_date', custom_end_date);
                formData.append( 'merchant_id', merchant_id);
                formData.append( 'recipient_name', recipient_name);
                formData.append( 'recipient_phone', recipient_phone);
                formData.append( 'order_status', order_status);
                formData.append( 'payment_status', payment_status);
                formData.append( 'order_no', order_no);
                formData.append( 'town_id', town_id);
                formData.append( 'destination_type', destination_type);
                formData.append( 'agent_id', agent_id);

                let uri = base_url+`v1/order-search-page`;
                axios.post(uri, formData,)
                    .then(function (response) {

                        vm.orders = response.data;
                        vm.loader=false;
                        vm.data_error=false;

                        console.log(response.data.length)

                        if(response.data.length < 1){
                            vm.data_error=true;
                            vm.clearResponseMsg();

                            vm.loader=false;

                        }


                    })
                    .catch(function (error) {
                        vm.loader=false;
                        console.log(error);
                    });

            },

            formSubmitLocation(){

                this.loader = true;

                var destination_type = this.destination_type;
                var delivery_distance = this.delivery_distance;
                var service_type = this.service_type;
                var inbound_rate_type = this.inbound_rate_type;
                var receiver_country = this.receiver_country;
                var receiver_town = this.receiver_town;
                var pickup_country = this.pickup_country;
                var pickup_town = this.pickup_town;
                var pickup_address = this.pickup_address;
                var receiver_address = this.receiver_address;
                var amount = this.total_amount;


                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append('destination_type', destination_type);
                formData.append('delivery_distance', delivery_distance);
                formData.append('service_type', service_type);
                formData.append('inbound_rate_type', inbound_rate_type);
                formData.append('pickup_country', pickup_country);
                formData.append('pickup_town', pickup_town);
                formData.append('pickup_address', pickup_address);
                formData.append('receiver_address', receiver_address);
                formData.append('receiver_country', receiver_country);
                formData.append('receiver_town', receiver_town);
                formData.append('amount', amount);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/order-update-location`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;

                            $('#locationModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();

                            vm.fetchOrders();
                            vm.closeLocationModal();
                        }else{

                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },

            formSubmitItem(){

                this.loader = true;
                var upsell = this.upsell;

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append('upsell', upsell);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/order-update-upsell`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;

                            $('#itemModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();

                            vm.fetchOrders();
                            vm.closeItemModal();
                        }else{

                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },
            mpesaCodeValidate(code){
                alert(code);

            this.loader = true;
            const vm = this;
            const formData = new FormData();
            formData.append( 'id', this.order_id);
            formData.append('upsell', upsell);
            formData.append('causer_id', this.causer_id);

            let uri = base_url+`v1/order-update-upsell`;
            axios.post(uri, formData)
                .then(function (response) {
                    var status = response.data.success;
                    if(status === 1){
                        vm.alert_success = true;
                        vm.loader = false;

                        $('#itemModal').modal('hide');
                        $(document.body).removeClass('modal-open');
                        $('.modal-backdrop').remove();

                        vm.fetchOrders();
                        vm.closeItemModal();
                    }else{

                        vm.loader = false;
                        vm.alert_error = true;
                    }

                })
                .catch(function (error) {
                    console.log(error);
                    vm.loader = false;
                    vm.alert_error = true;
                });

            },

            formSubmitReceiver(){

                this.loader = true;
                var receiver_name = this.receiver_name;

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append('receiver_name', receiver_name);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/order-update-receiver`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;

                            $('#receiverModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();

                            vm.fetchOrders();
                            vm.closeReceiverModal();
                        }else{

                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },

            formSubmitChangeStatus(){

                this.loader = true;

                var order_status = this.order_status;
                var status_reason = this.status_reason;
                var custom_reason = this.custom_reason;
                var scheduled_date = this.scheduled_date;
                var mpesa_code = this.mpesa_code;

                if(order_status == 'delivered' && is_sender_merchant == 1){
                    alert(this.mpesa_code);
                }


                var is_sender_merchant = this.is_sender_merchant;
                if(order_status == 'scheduled' && is_sender_merchant == 1){

                    if(!scheduled_date){
                        alert('Enter scheduled date');
                        this.loader = false;
                        return
                    }
                }

                if(status_reason == 'custom_reason'){

                    if(!custom_reason){
                        alert('Enter custom reason');
                        this.loader = false;
                        return
                    }
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append('order_status', order_status);
                formData.append('status_reason', status_reason);
                formData.append('custom_reason', custom_reason);
                formData.append('scheduled_date', scheduled_date);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/order-update-status`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;

                            if(vm.order_date !== 'all' || vm.client_type !== 'all' || vm.search_service_type !== 'all'
                                || vm.search_order_status !== 'all' || vm.search_payment_status !== 'all' || vm.search_merchant_id !== 'all'
                                || vm.search_town !== 'all' || vm.search_agent_id !== 'all' || vm.search_recipient_name !== '' || vm.search_order_no !== ''){

                                vm.formSubmit();

                            }else{
                                vm.fetchOrders();
                            }

                            $('#statusModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();

                            vm.closeStatusModal();
                        }else{

                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },

            formSubmitSpecialInstruction(){

                this.loader = true;

                var special_instruction = this.special_instruction;
                if(!special_instruction){
                    alert('Enter special instruction');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append('special_instruction', special_instruction);

                let uri = base_url+`v1/order-update-special-instruction`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;

                            $('#specialInstructionModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();

                            vm.fetchOrders();
                            vm.closeSpecialInstructionModal();
                        }else{

                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },

            formSubmitScheduledDate(){

                this.loader = true;

                var scheduled_date  = this.scheduled_date;
                if(!scheduled_date){
                    alert('Enter special instruction');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append('scheduled_date', scheduled_date);

                let uri = base_url+`v1/order-update-scheduled-date`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;

                            vm.scheduled_date = "";
                            $('#scheduledDateModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();

                            vm.fetchOrders();
                            vm.closeSpecialInstructionModal();
                        }else{

                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },

            formSubmitStatusDate(){

                this.loader = true;

                var status_date  = this.status_date;
                if(!status_date){
                    alert('Enter status date');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append('status_date', status_date);

                let uri = base_url+`v1/order-update-status-date`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;

                            vm.status_date = "";
                            $('#statusDateModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();

                            vm.fetchOrders();
                            vm.closeStatusDateModal();
                        }else{

                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },

            formSubmitAgent(){

                this.loader = true;
                var agent = this.agent;
                if(!agent){
                    alert('Select Agent');
                    this.loader = false;
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append('agent', agent);

                let uri = base_url+`v1/order-update-agent`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;

                            $('#agentModal').modal('hide');
                            $(document.body).removeClass('modal-open');
                            $('.modal-backdrop').remove();

                            vm.fetchOrders();
                            vm.closeAgentModal();
                        }else{

                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },

            calculateDeliveryCharge(){

                this.total_amount = 0;
                this.total_weight = 0;

                const vm = this;
                var total_weight = 0;
                for (var i = 0; i < this.items.length; i++){
                    total_weight = total_weight + parseFloat(this.items[i].weight);
                }
                this.total_weight = total_weight;

                if(this.destination_type == '1'){

                    if(vm.sender_town != ''){

                        if(vm.receiver_town != ''){

                            if(vm.is_sender_merchant == true){

                                if(vm.merchant_id == ''){
                                    alert('Select Merchant');
                                    return;
                                }

                            }

                            const formData = new FormData();
                            formData.append('is_sender_merchant', vm.is_sender_merchant);
                            formData.append('merchant_id', vm.merchant_id);
                            formData.append('from', vm.sender_town);
                            formData.append('destination', vm.receiver_town);
                            formData.append('weight', total_weight);

                            let uri = base_url + `v1/outbound-delivery-charge-calculator`;
                            axios.post(uri, formData)
                                .then(function (response) {

                                    var status = response.data.success;
                                    if (status === 1) {
                                        var amount = response.data.amount;
                                        vm.total_amount = amount;
                                    }else{
                                        vm.total_amount = 0;
                                    }

                                })
                                .catch(function (error) {
                                    console.log(error);
                                });

                        }
                    }


                }else if(this.destination_type == 2){

                    if(vm.service_type == 1){

                        if(vm.inbound_rate_type == 1){
                            // On demand delivery charges

                            if(vm.delivery_distance != ''){

                                if(vm.is_sender_merchant == true){

                                    if(vm.merchant_id == ''){
                                        alert('Select Merchant');
                                        return;
                                    }
                                }

                                const formData = new FormData();
                                formData.append('is_sender_merchant', vm.is_sender_merchant);
                                formData.append('merchant_id', vm.merchant_id);
                                formData.append('service_type', vm.service_type);
                                formData.append('inbound_rate_type', vm.inbound_rate_type);
                                formData.append('delivery_distance', vm.delivery_distance);
                                formData.append('weight', total_weight);

                                let uri = base_url + `v1/inbound-delivery-charge-calculator`;
                                axios.post(uri, formData)
                                    .then(function (response) {

                                        var status = response.data.success;
                                        if (status === 1) {
                                            var amount = response.data.amount;
                                            vm.total_amount = amount;
                                        }else{
                                            vm.total_amount = 0;
                                        }

                                    })
                                    .catch(function (error) {
                                        console.log(error);
                                    });
                            }

                        }else if(vm.inbound_rate_type == 2){
                            // Zone bound delivery charges

                            if(vm.zone_id != ''){

                                if(vm.is_sender_merchant == true){


                                    if(vm.merchant_id == ''){
                                        alert('Select Merchant');
                                        return;
                                    }
                                }

                                const formData = new FormData();
                                formData.append('is_sender_merchant', vm.is_sender_merchant);
                                formData.append('merchant_id', vm.merchant_id);
                                formData.append('service_type', vm.service_type);
                                formData.append('inbound_rate_type', vm.inbound_rate_type);
                                formData.append('zone', vm.zone_id);
                                formData.append('weight', total_weight);

                                let uri = base_url + `v1/inbound-delivery-charge-calculator`;
                                axios.post(uri, formData)
                                    .then(function (response) {

                                        var status = response.data.success;
                                        if (status === 1) {
                                            var amount = response.data.amount;
                                            vm.total_amount = amount;
                                        }else{
                                            vm.total_amount = 0;
                                        }

                                    })
                                    .catch(function (error) {
                                        console.log(error);
                                    });
                            }
                        }

                    }else{

                        if(vm.inbound_rate_type == 1){
                            // On demand delivery charges

                            if(vm.delivery_distance != ''){

                                if(vm.is_sender_merchant == true){


                                    if(vm.merchant_id == ''){
                                        alert('Select Merchant');
                                        return;
                                    }
                                }

                                const formData = new FormData();
                                formData.append('is_sender_merchant', vm.is_sender_merchant);
                                formData.append('merchant_id', vm.merchant_id);
                                formData.append('service_type', vm.service_type);
                                formData.append('inbound_rate_type', vm.inbound_rate_type);
                                formData.append('delivery_distance', vm.delivery_distance);
                                formData.append('weight', total_weight);

                                let uri = base_url + `v1/inbound-delivery-charge-calculator`;
                                axios.post(uri, formData)
                                    .then(function (response) {

                                        var status = response.data.success;
                                        if (status === 1) {
                                            var amount = response.data.amount;
                                            vm.total_amount = amount;
                                        }else{
                                            vm.total_amount = 0;
                                        }

                                    })
                                    .catch(function (error) {
                                        console.log(error);
                                    });
                            }

                        }else if(vm.inbound_rate_type == 2){
                            // Zone bound delivery charges

                            if(vm.zone_id != ''){

                                if(vm.is_sender_merchant == true){


                                    if(vm.merchant_id == ''){
                                        alert('Select Merchant');
                                        return;
                                    }
                                }

                                const formData = new FormData();
                                formData.append('is_sender_merchant', vm.is_sender_merchant);
                                formData.append('merchant_id', vm.merchant_id);
                                formData.append('service_type', vm.service_type);
                                formData.append('inbound_rate_type', vm.inbound_rate_type);
                                formData.append('zone', vm.zone_id);
                                formData.append('weight', total_weight);

                                let uri = base_url + `v1/inbound-delivery-charge-calculator`;
                                axios.post(uri, formData)
                                    .then(function (response) {

                                        var status = response.data.success;
                                        if (status === 1) {
                                            var amount = response.data.amount;
                                            vm.total_amount = amount;
                                        }else{
                                            vm.total_amount = 0;
                                        }

                                    })
                                    .catch(function (error) {
                                        console.log(error);
                                    });
                            }
                        }
                    }
                }

            },

            clearResponseMsg(){

            setTimeout(() => {
                this.data_error = false;
            }, 3000);


            },


            makeCall(val){
                this.$eventBus.$emit('make-call', val);
            }
        }
    }
</script>
