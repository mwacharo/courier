<template>
    <div>
        <form class="form" id="kt_form" @submit.prevent="formSubmit" method="post">
            <div class="tab-content">
                <!--begin::Tab-->
                <div class="tab-pane show active px-7" id="kt_user_edit_tab_1" role="tabpanel">

                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-8 my-2">

                            <div class="alert alert-success" role="alert" v-if="alert_success">Record details updated!</div>
                            <div class="alert alert-danger" role="alert" v-if="alert_error">Error updating record!</div>
                            <!--begin::Row-->
                            <div class="row">
                                <label class="col-3"></label>
                                <div class="col-9">
                                    <h6 class="text-dark font-weight-bold mb-10">Order Info:</h6>
                                </div>
                            </div>
                            <!--end::Row-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Order No <br><small class="text-danger">(Blank will autogenerate)</small></label>
                                <div class="col-lg-8 col-xl-8">
                                    <input class="form-control form-control-solid form-control-lg" name="order_no"
                                           type="text" v-model="order_no" disabled/>
                                    <barcode v-bind:value="order_no" :options="{ lineColor: '#0275d8', text: 'Scan'}" v-if="order_no"></barcode>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Sender Merchant?</label>
                                <div class="col-4">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select"
                                           v-model="is_sender_merchant"/>
                                    <span></span>
                                </label>
                            </span>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Destination Type</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="destination_type"
                                            type="text" v-model="destination_type">
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
                                    <select class="form-control form-control-solid form-control-lg" name="service_type" type="text" v-model="service_type">
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
                                           type="number" min="0" onwheel="this.blur()" v-model="delivery_distance" />
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Enable Cash On Delivery</label>
                                <div class="col-4">
                            <span class="switch">
                                <label>
                                    <input type="checkbox" checked="checked" name="select" v-model="cash_on_delivery"/>
                                    <span></span>
                                </label>
                            </span>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="cash_on_delivery">
                                <label class="col-xl-4 col-lg-4 col-form-label">Cash On Delivery Amount</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg"
                                           name="cash_on_delivery_amount" type="number" min="0" onwheel="this.blur()" v-model="cash_on_delivery_amount"/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
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
                            <!--end::Group-->

                            <h5 class="text-dark font-weight-bold mb-10">Sender Details:</h5>

                            <!--begin::Group-->
                            <div class="form-group row" v-if="is_sender_merchant">
                                <label class="col-xl-4 col-lg-4 col-form-label">Merchant Details</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="name" type="text"
                                            v-model="merchant" @change.prevent="selectedMerchant(merchant)">
                                        <option value="">Select Merchant</option>
                                        <option v-for="merchant in merchants" :value="merchant">{{ merchant.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Sender Name</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="sender_name"
                                           type="text" v-model="sender_name" v-bind:disabled="is_sender_merchant"/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Sender Address</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="sender_address"
                                           type="text" v-model="sender_address" v-bind:disabled="is_sender_merchant"/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Sender Email</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="sender_email"
                                           type="text" v-model="sender_email" v-bind:disabled="is_sender_merchant"/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Sender Phone</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="sender_phone"
                                           type="number" min="0" onwheel="this.blur()" v-model="sender_phone" v-bind:disabled="is_sender_merchant"/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Sender Phone (Alternative)</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg"
                                           name="sender_phone_alternative" type="number" min="0" onwheel="this.blur()"
                                           v-model="sender_phone_alternative" v-bind:disabled="is_sender_merchant"/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="destination_type==1">
                                <label class="col-xl-4 col-lg-4 col-form-label">Sender Country</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="sender_country" v-bind:disabled="is_sender_merchant">
                                        <option value="">Select Country</option>
                                        <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row" v-if="destination_type==1">
                                <label class="col-xl-4 col-lg-4 col-form-label">Sender Town</label>
                                <div class="col-lg-8 col-lg-8">
                                    <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="sender_town" v-bind:disabled="is_sender_merchant" >
                                        <option value="">Select Town</option>
                                        <option v-for="town in towns" :value="town.id">{{ town.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <!--end::Group-->

                            <h5 class="text-dark font-weight-bold mb-10">Pickup Details:</h5>

                            <!--begin::Group-->
                            <div class="form-group row">
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
                            <div class="form-group row">
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
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Pickup Address</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="pickup_address"
                                           type="text" v-model="pickup_address"/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <h5 class="text-dark font-weight-bold mb-10">Receiver Details:</h5>

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Name</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_name"
                                           type="text" v-model="receiver_name"/>
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
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Email</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_email"
                                           type="text" v-model="receiver_email" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Phone</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_phone"
                                           type="number" min="0" onwheel="this.blur()" v-model="receiver_phone" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Phone (Alternative)</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg"
                                           name="receiver_phone_alternative" type="number" min="0" onwheel="this.blur()"
                                           v-model="receiver_phone_alternative"/>
                                </div>
                            </div>
                            
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
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Latitude</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_latitude"
                                           type="number" min="0" onwheel="this.blur()" id="location_latitude" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->

                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-xl-4 col-lg-4 col-form-label">Receiver Longitude</label>
                                <div class="col-lg-8 col-lg-8">
                                    <input class="form-control form-control-solid form-control-lg" name="receiver_longitude"
                                           type="number" min="0" onwheel="this.blur()" id="location_longitude" disabled/>
                                </div>
                            </div>
                            <!--end::Group-->


                            <div class="pac-card" id="pac-card">
                                <div>
                                    <div id="title">
                                        Autocomplete search
                                    </div>
                                    <div id="type-selector" class="pac-controls">
                                        <input type="radio" name="type" id="changetype-all" checked="checked">
                                        <label for="changetype-all">All</label>

                                        <input type="radio" name="type" id="changetype-establishment">
                                        <label for="changetype-establishment">Establishments</label>

                                        <input type="radio" name="type" id="changetype-address">
                                        <label for="changetype-address">Addresses</label>

                                        <input type="radio" name="type" id="changetype-geocode">
                                        <label for="changetype-geocode">Geocodes</label>
                                    </div>
                                    <div id="strict-bounds-selector" class="pac-controls">
                                        <input type="checkbox" id="use-strict-bounds" value="">
                                        <label for="use-strict-bounds">Strict Bounds</label>
                                    </div>
                                </div>
                                <div id="pac-container">
                                    <input id="pac-input" type="text"
                                           placeholder="Enter a location">
                                </div>
                            </div>
                            <div id="map" style="height: 300px;"></div>
                            <div id="infowindow-content">
                                <img src="" width="16" height="16" id="place-icon">
                                <span id="place-name"  class="title"></span><br>
                                <span id="place-address"></span>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-8 my-2">

                            <div class="border-top pt-10 mt-15">

                                <h5 class="text-dark font-weight-bold mb-10">Order Items <small><a href="#" data-toggle="modal" data-target="#modalAddItem" class="text-danger">(Add Item)</a></small></h5>

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <div class="col-lg-12 col-xl-12">
                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Total Weight</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(item, index) in items">
                                                <td>{{ item.description }}</td>
                                                <td>Kshs {{ item.price }}</td>
                                                <td>{{ item.quantity }}</td>
                                                <td>{{ item.weight }}kg</td>
                                                <td><a href="#" data-toggle="modal" data-target="#modalEditItem" class="text-danger" @click.prevent="itemDetails(item, index)">View</a> | <a href="#" class="text-danger" @click.prevent="itemDelete(index, item)">Delete</a> </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                <!--end::Group-->


                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-8 my-2">

                            <div class="border-top pt-10 mt-15">

                                <h5 class="text-dark font-weight-bold mb-10">Shipment Details</h5>

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Special Instructions</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <textarea class="form-control form-control-solid form-control-lg" name="special_instruction" type="text" v-model="special_instruction" rows="4"></textarea>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Rider/Driver</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="name" type="text"
                                                v-model="rider_id">
                                            <option value="">Select Rider</option>
                                            <option v-for="rider in riders" :value="rider.id">{{ rider.first_name }} {{ rider.last_name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Payment Type</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="name" type="text" v-model="payment_type">
                                            <option value="">Select Payment Type</option>
                                            <option value="1">Cash</option>
                                            <option value="2">Invoice</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Order Status</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="order_status" type="text" v-model="order_status">
                                          <option value="order_pending">Pending Confirmation</option>
                                                    <option value="scheduled">Scheduled</option>
                                                    <option value="awaiting_dispatch">Awaiting Dispatch</option>
                                                    <option value="delivered">Delivered</option>
                                                    <option value="dispatched">Dispatched</option>
                                                    <option value="not_dispatched">Not Dispatched</option>
                                                    <option value="delivery_pending">Delivery Pending</option>
                                                    <option value="cancelled">Cancelled</option>
                                                    <option value="returned">Returned</option>
                                                    <option value="expired">Expired</option>
                                                    <option value="out_of_stock">Out of stock</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row" v-if="order_status=='order_pending' || order_status=='delivery_pending' || order_status=='returned' || order_status=='cancelled'">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Status Reason</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="status_reason" type="text" v-model="status_reason">
                                             <option value="">Select Reason</option>
                                                    <option value="wrong_order">Wrong Order</option>
                                                    <option value="duplicate_order">Duplicate Order</option>
                                                    <option value="will_reorder">Will Reorder When Ready</option>
                                                    <option value="incomplete_no">Incomplete No</option>
                                                    <option value="not_available">Not available</option>
                                                    <option value="call_back">Call back</option>
                                                    <option value="not_picking">Not Picking</option>
                                                    <option value="hanged_up">Hanged Up</option>
                                                    <option value="too_long">Too Long</option>
                                                    <option value="got_alternative">Got Alternative</option>
                                                    <option value="client_offline">Client Offline</option>
                                                     <option value="out_of_destination">Out of destination</option>
                                                    <option value="expired">Expired</option>
                                                    <option value="out_of_stock">Out Of Stock</option>
                                                    <option value="custom_reason">Custom Reason</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row" v-if="order_status=='scheduled' && service_type!=1">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Scheduled Date</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <input class="form-control form-control-solid form-control-lg" name="scheduled_date"
                                               type="date" v-model="scheduled_date"/>
                                    </div>
                                </div>
                                <!--end::Group-->


                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Payment Status</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="payment_status" type="text" v-model="payment_status">
                                            <option value="0">Pending</option>
                                            <option value="1">Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row" v-if="payment_type == '1' && payment_status == '1'">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Payment Method</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <select class="form-control form-control-solid form-control-lg" name="payment_method"
                                                type="text" v-model="payment_method">
                                            <option value="1">Cash Payment</option>
                                            <option value="2">Mpesa Payment</option>
                                            <option value="3">Cash & Mpesa Payment</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row" v-if="payment_type == '1' && payment_status == '1' && payment_method == 1">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Cash Amount</label>
                                    <div class="col-lg-8 col-xl-8">
                                        <input class="form-control form-control-solid form-control-lg" name="cash_amount " type="number" min="0" onwheel="this.blur()" v-model="cash_amount"/>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row" v-if="payment_type == '1' && payment_status == '1' && payment_method == 2">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Mpesa Amount</label>
                                    <div class="col-lg-8 col-xl-8">
                                        <input class="form-control form-control-solid form-control-lg" name="mpesa_amount " type="number" min="0" onwheel="this.blur()" v-model="mpesa_amount"/>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row" v-if="payment_type == '1' && payment_status == '1' && payment_method == 3">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Paid Amount</label>
                                    <div class="col-lg-8 col-xl-8">
                                        <input class="form-control form-control-solid form-control-lg" name="cash_mpesa_amount " type="number" min="0" onwheel="this.blur()" v-model="cash_mpesa_amount"/>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row" v-if="payment_type == '1' && payment_status == '1' && payment_method != 1">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Transaction Code</label>
                                    <div class="col-lg-8 col-lg-8">
                                        <input class="form-control form-control-solid form-control-lg" name="transaction_code" type="text" v-model="transaction_code"/>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-4 col-lg-4 col-form-label">Total Amount</label>
                                    <div class="col-lg-8 col-xl-8">
                                        <input class="form-control form-control-solid form-control-lg" name="total_amount " type="number" min="0" onwheel="this.blur()" v-model="total_amount" :disabled="change_order_amount === '0'"/>
                                    </div>
                                </div>
                                <!--end::Group-->

                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-8 my-2">

                            <div class="border-top pt-10 mt-15">

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <div class="col-lg-12 col-xl-12">
                                        <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
                                            <div class="col-md-11">
                                                <div class="d-flex justify-content-between flex-column flex-md-row font-size-lg">
                                                    <div class="d-flex flex-column mb-10 mb-md-0">
                                                        <div class="font-weight-bolder font-size-lg mb-3">ORDER SUMMARY</div>
                                                        <div class="d-flex justify-content-between mb-3">
                                                            <span class="mr-15 font-weight-bold">Total Items:</span>
                                                            <span class="text-right">{{ items.length }} </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-3">
                                                            <span class="mr-15 font-weight-bold">Total Weight:</span>
                                                            <span class="text-right">{{ total_weight }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column text-md-right">
                                                        <span class="font-size-lg font-weight-bolder mb-1">TOTAL AMOUNT</span>
                                                        <span class="font-size-h2 font-weight-boldest text-danger mb-1">Kshs {{ total_amount }}</span>
                                                        <span>Taxes Included</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Group-->

                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-8 my-2">

                            <div class="d-flex justify-content-between border-top pt-10 mt-15">
                                <div>
                                    <button type="submit" class="btn btn-success font-weight-bolder px-9 py-4" data-wizard-type="action-submit">Edit</button>
                                    <button type="button" class="btn btn-danger font-weight-bolder px-9 py-4" data-wizard-type="action-submit" @click.prevent="cancelButton()">Cancel</button>
                                    <div class="spinner-border spinner-border-sm" role="status" v-if="loader">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>


                    <!--end::Row-->
                </div>
                <!--end::Tab-->

            </div>
        </form>

        <div class="modal fade" id="modalAddItem" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <form class="form" @submit.prevent="itemSubmit" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Item</h5>
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
                                            <label class="col-4 col-form-label">Use Inventory Product?</label>
                                            <div class="col-4">
                                            <span class="switch">
                                                <label>
                                                    <input type="checkbox" checked="checked" name="select"
                                                           v-model="item_inventory"/>
                                                    <span></span>
                                                </label>
                                            </span>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row" v-if="item_inventory">
                                            <label class="col-xl-4 col-lg-4 col-form-label">SKU</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="item_sku" type="text" v-model="item_sku"/>
                                                <table class="table" v-if="inventories.length > 0">
                                                    <tbody>
                                                    <tr v-for="inventory in inventories">
                                                        <td><small><a href="#" @click.prevent="selectInventory(inventory)">{{ inventory.sku }} - {{ inventory.name }}</a></small></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Description</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_description" type="text" v-model="item_description" :disabled="item_inventory == true"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Price</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_price" type="number" min="0" onwheel="this.blur()" v-model="item_price"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Quantity <small class="text-danger" v-if="item_inventory == true">(Available {{ item_inventory_quantity }}) </small></label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_quantity" type="number" min="0" onwheel="this.blur()" v-model="item_quantity"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Total Weight</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_weight" type="number" step="0.01" min="0" onwheel="this.blur()" v-model="item_weight"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="itemClose">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Add Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
                                        <div class="form-group row" v-if="item_inventory">
                                            <label class="col-xl-4 col-lg-4 col-form-label">SKU</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg" name="item_sku" type="text" v-model="item_sku"/>
                                                <table class="table" v-if="inventories.length > 0">
                                                    <tbody>
                                                    <tr v-for="inventory in inventories">
                                                        <td><small><a href="#" @click.prevent="selectInventory(inventory)">{{ inventory.sku }} - {{ inventory.name }}</a></small></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Description</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_description" type="text" v-model="item_description" :disabled="item_inventory == true"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Price</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_price" type="number" min="0" onwheel="this.blur()" v-model="item_price"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Quantity <small class="text-danger" v-if="item_inventory == true">(Available {{ item_inventory_quantity }}) </small></label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_quantity" type="number" min="0" onwheel="this.blur()" v-model="item_quantity"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                        <!--begin::Group-->
                                        <div class="form-group row">
                                            <label class="col-xl-4 col-lg-4 col-form-label">Total Weight</label>
                                            <div class="col-lg-8 col-xl-8">
                                                <input class="form-control form-control-solid form-control-lg"
                                                       name="item_weight" type="number" step="0.01" min="0" onwheel="this.blur()" v-model="item_weight"/>
                                            </div>
                                        </div>
                                        <!--end::Group-->

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" @click.prevent="itemClose">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary font-weight-bold">update Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</template>

<script>
    import VueBarcode from "vue-barcode";

    export default {

        mounted(){
            this.fetchCountries();
            this.fetchTowns();
            this.fetchZones();
            this.fetchBranches();
            this.fetchMerchants();
            this.fetchRiders();
            this.fetchDetails();
            this.fetchOrderItems();
        },

        components: {
            'barcode': VueBarcode
        },


        props: {
            order_id: String,
            causer_id: String,
            change_order_amount: String,
        },

        data(){
            return{
                order_no: '',
                destination_type: '1',
                delivery_distance: '',
                service_type: '',
                inbound_rate_type: '',
                upsell: false,
                cash_on_delivery: false,
                cash_on_delivery_amount: '',
                is_sender_merchant: false,
                merchant: '',
                merchant_id: '',
                sender_name: '',
                sender_address: '',
                sender_email: '',
                sender_phone: '',
                sender_phone_alternative: '',
                sender_country: '',
                sender_town: '',
                pickup_country: '',
                pickup_town: '',
                pickup_address: '',
                receiver_name: '',
                receiver_address: '',
                receiver_email: '',
                receiver_phone: '',
                receiver_phone_alternative: '',
                receiver_country: '',
                receiver_gender: '',
                receiver_town: '',
                receiver_latitude: '',
                receiver_longitude: '',
                special_instruction: '',
                payment_type: '',
                amount: '',
                insurance: false,
                order_status: 'order_pending',
                payment_status: '0',
                payment_method: '',
                cash_amount: '',
                mpesa_amount: '',
                cash_mpesa_amount: '',
                transaction_code: '',
                status_reason: '',
                rider_id: '',
                branch_id: '',
                zone_id: '',
                booking_date: '',
                booking_time: '',
                scheduled_date: '',
                admin_id: '',

                item_inventory: false,
                item_inventory_product: null,
                item_inventory_id: '',
                item_inventory_quantity: 0,
                item_inventory_amount: 0,
                item_id: '',
                item_sku: '',
                item_index: '',
                item_description: '',
                item_price: '',
                item_quantity: '',
                item_weight: '',
                items: [],
                inventory_products: [],
                total_weight: 0,
                total_amount: 0,

                merchants: [],
                riders: [],
                countries: [],
                towns: [],
                branches: [],
                zones: [],
                inventories: [],

                alert_error: false,
                alert_success: false,
                loader: false,
            }
        },

        methods: {

            fetchMerchants() {
                let uri = base_url + `v1/merchant-list`;
                axios.get(uri).then((response) => {
                    this.merchants = response.data;
                });
            },

            fetchRiders() {
                let uri = base_url + `v1/rider-list`;
                axios.get(uri).then((response) => {
                    this.riders = response.data;
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

            fetchBranches() {
                let uri = base_url + `v1/branch-list`;
                axios.get(uri).then((response) => {
                    this.branches = response.data;
                });
            },

            cancelButton(){
                window.history.back();
            },

            selectedMerchant(merchant){
                this.merchant_id = merchant.id;
                this.sender_country = merchant.country_id;
                this.sender_town = merchant.town_id;
                this.sender_name = merchant.name;
                this.sender_phone = merchant.phone_number;
                this.sender_address = merchant.address;
                this.sender_email = merchant.email;
            },

            selectInventory(inventory) {

                var available_quantity = inventory.quantity;
                if(available_quantity==0){
                    alert("Inventory low");
                }
                this.item_inventory_product = inventory;
                this.item_description = inventory.name;
                this.item_inventory_id = inventory.id;
                this.item_inventory_quantity = inventory.quantity;
                this.item_sku = inventory.sku;

            },

            fetchInventoryDetail(){

                const vm = this;
                let uri = base_url+`v1/inventory-details/`+this.item_inventory_id;
                axios.get(uri).then((response) => {

                    let inventory = response.data;
                    if(inventory){
                        vm.item_inventory_product = inventory;
                        vm.item_inventory_id = inventory.id;
                        vm.item_inventory_amount = inventory.amount;
                        vm.item_inventory_quantity = inventory.quantity;
                    }
                });
            },


            itemSubmit(){

                if (!this.item_description) {
                    alert('Enter description');
                    return
                }

                if (!this.item_quantity) {
                    alert('Enter quantity');
                    return
                }

                var sku = "";
                if(this.item_inventory == true){

                    if (!this.item_inventory_product.sku) {
                        alert('Enter SKU');
                        return
                    }

                    if(this.item_quantity > this.item_inventory_quantity){
                        alert("Quantity exceeds inventory quantity");
                        return;
                    }

                    sku = this.item_inventory_product.sku
                    sku = this.item_inventory_product.sku
                }

                var item = {};
                item["id"] = "";
                item["description"] = this.item_description;
                item["price"] = this.item_price;
                item["quantity"] = this.item_quantity;
                item["weight"] = this.item_weight;
                item["sku"] = sku;
                item["inventory_id"] = this.item_inventory_id;
                this.items.push(item);

                this.item_description = "";
                this.item_price = "";
                this.item_quantity = "";
                this.item_weight = "";
                this.item_sku = "";
                this.item_inventory = false;
                this.item_inventory_id = "";
                this.item_inventory_amount = "";
                this.item_inventory_quantity = "";
                this.item_inventory_product = null;

                $('#modalAddItem').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();


            },

            itemDetails(item, index){

                console.log(item);
                this.item_index = index;
                this.item_description = item.description;
                this.item_price = item.price;
                this.item_quantity = item.quantity;
                this.item_weight = item.weight;
                this.item_sku = item.sku;

                if(item.inventory_product == '0'){
                    this.item_inventory = false;
                }else{
                    this.item_inventory = true;
                }

                if(item.id){
                    this.item_id = item.id;
                }


            },

            itemEditSubmit() {

                if (!this.item_description) {
                    alert('Enter description');
                    return
                }

                if (!this.item_quantity) {
                    alert('Enter quantity');
                    return
                }

                if(this.item_inventory == true){

                    if (!this.item_sku) {
                        alert('Enter SKU');
                        return
                    }

                    if(this.item_quantity > this.item_inventory_quantity){
                        alert("Quantity exceeds inventory quantity");
                        return;
                    }
                }

                if(this.item_id){
                    this.items[this.item_index].id = this.item_id;
                }

                this.items[this.item_index].description = this.item_description;
                this.items[this.item_index].price = this.item_price;
                this.items[this.item_index].quantity = this.item_quantity;
                this.items[this.item_index].weight = this.item_weight;
                this.items[this.item_index].sku = this.item_sku;

                if(this.item_inventory_product){
                    if(this.item_inventory_product.sku){
                        this.items[this.item_index].sku = this.item_inventory_product.sku;
                    }else{
                        this.items[this.item_index].sku = "";
                    }
                }


                this.items[this.item_index].inventory_id = this.item_inventory_id;
                this.items[this.item_index].id = this.item_id;

                this.item_description = "";
                this.item_price = "";
                this.item_quantity = "";
                this.item_weight = "";
                this.item_id = "";
                this.item_sku = "";
                this.item_inventory = false;
                this.item_inventory_id = "";
                this.item_inventory_amount = "";
                this.item_inventory_quantity = "";
                this.item_inventory_product = null;

                $('#modalEditItem').modal('hide');
                $(document.body).removeClass('modal-open');
                $('.modal-backdrop').remove();


            },

            itemDelete(index, item){
                this.items.splice(index, 1);
                this.deleteItemDetails(item.id)
            },

            itemClose(){
                this.item_description = "";
                this.item_price = "";
                this.item_quantity = "";
                this.item_weight = "";
                this.item_sku = "";
                this.item_id = "";
                this.item_inventory = false;
                this.item_inventory_id = "";
                this.item_inventory_amount = "";
                this.item_inventory_quantity = "";
                this.item_inventory_product = null;
            },


            fetchDetails(){

                const vm = this;
                let uri = base_url+`v1/order-details/`+this.order_id;
                axios.get(uri).then((response) => {

                    let order = response.data;
                    if(order){

                        this.order_no = order.order_no;
                        this.destination_type = order.destination_type;
                        this.delivery_distance = order.delivery_distance;
                        this.service_type = order.service_type;
                        this.inbound_rate_type = order.inbound_rate_type;
                        this.upsell = order.upsell;
                        this.cash_on_delivery = order.cash_on_delivery;
                        this.cash_on_delivery_amount = order.cash_on_delivery_amount;
                        this.is_sender_merchant = order.is_sender_merchant;
                        this.merchant_id = order.merchant_id;
                        this.sender_name = order.sender_name;
                        this.sender_address = order.sender_address;
                        this.sender_email = order.sender_email;
                        this.sender_phone = order.sender_phone;
                        this.sender_phone_alternative = order.sender_phone_alternative;
                        this.sender_country = order.sender_country;
                        this.sender_town = order.sender_town;
                        this.receiver_name = order.receiver_name;
                        this.receiver_address = order.receiver_address;
                        this.receiver_email = order.receiver_email;
                        this.receiver_phone = order.receiver_phone;
                        this.receiver_gender = order.receiver_gender;
                        this.receiver_phone_alternative = order.receiver_phone_alternative;
                        this.receiver_country = order.receiver_country;
                        this.receiver_town = order.receiver_town;
                        this.receiver_latitude = order.receiver_latitude;
                        this.receiver_longitude = order.receiver_longitude;
                        this.scheduled_date = order.scheduled_date;
                        this.special_instruction = order.special_instruction;
                        this.payment_type = order.payment_type;
                        this.insurance = order.insurance;
                        this.order_status = order.order_status;
                        this.status_reason = order.status_reason;
                        this.payment_status = order.payment_status;
                        this.payment_method = order.payment_method;
                        this.cash_amount = order.cash_amount;
                        this.mpesa_amount = order.mpesa_amount;
                        this.cash_mpesa_amount = order.cash_mpesa_amount;
                        this.transaction_code = order.transaction_code;
                        this.pickup_country = order.pickup_country;
                        this.pickup_town = order.pickup_town;
                        this.pickup_address = order.pickup_address;
                        this.rider_id = order.rider_id;
                        this.zone_id = order.zone_id;
                        this.admin_id = order.admin_id;
                        this.total_weight = order.total_weight;
                        this.total_amount = order.amount;

                        for (var i = 0; i < vm.merchants.length; i++) {
                            if (vm.merchants[i]['id'] === vm.merchant_id) {
                                vm.merchant = vm.merchants[i]
                            }
                        }

                    }
                });
            },

            fetchOrderItems() {
                let uri = base_url+`v1/order-items/`+this.order_id;
                axios.get(uri).then((response) => {
                    this.items = response.data;
                });
            },

            formSubmit(){

                this.loader = true;

                var order_no = this.order_no;
                var destination_type = this.destination_type;
                var delivery_distance = this.delivery_distance;
                var service_type = this.service_type;
                var inbound_rate_type = this.inbound_rate_type;
                var upsell = this.upsell;
                var cash_on_delivery = this.cash_on_delivery;
                var cash_on_delivery_amount = this.cash_on_delivery_amount;
                var is_sender_merchant = this.is_sender_merchant;
                var merchant_id = this.merchant_id;
                var sender_name = this.sender_name;
                var sender_address = this.sender_address;
                var sender_email = this.sender_email;
                var sender_phone = this.sender_phone;
                var sender_phone_alternative = this.sender_phone_alternative;
                var sender_country = this.sender_country;
                var sender_town = this.sender_town;
                var pickup_country = this.pickup_country;
                var pickup_town = this.pickup_town;
                var pickup_address = this.pickup_address;
                var receiver_name = this.receiver_name;
                var receiver_address = this.receiver_address;
                var receiver_email = this.receiver_email;
                var receiver_phone = this.receiver_phone;
                var receiver_gender = this.receiver_gender;
                var receiver_phone_alternative = this.receiver_phone_alternative;
                var receiver_country = this.receiver_country;
                var receiver_town = this.receiver_town;
                var scheduled_date = this.scheduled_date;
                var special_instruction = this.special_instruction;
                var payment_type = this.payment_type;
                var insurance = this.insurance;
                var order_status = this.order_status;
                var status_reason = this.status_reason;
                var payment_status = this.payment_status;
                var payment_method = this.payment_method;
                var cash_amount = this.cash_amount;
                var mpesa_amount = this.mpesa_amount;
                var cash_mpesa_amount = this.cash_mpesa_amount;
                var transaction_code = this.transaction_code;
                var rider_id = this.rider_id;
                var zone_id = this.zone_id;
                var admin_id = this.admin_id;
                var total_weight = this.total_weight;
                var amount = this.total_amount;
                var location_latitude = document.getElementById("location_latitude").value;
                var location_longitude = document.getElementById("location_longitude").value;


                if (!destination_type) {
                    destination_type = "1";
                }

                if (!payment_type) {
                    payment_type = "1";
                }

                if(this.items.length == 0){
                    alert('Enter order items');
                    this.loader = false;
                    return
                }

                console.log(this.items.length);
                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append('order_no', order_no);
                formData.append('destination_type', destination_type);
                formData.append('delivery_distance', delivery_distance);
                formData.append('service_type', service_type);
                formData.append('inbound_rate_type', inbound_rate_type);
                formData.append('upsell', upsell);
                formData.append('cash_on_delivery', cash_on_delivery);
                formData.append('cash_on_delivery_amount', cash_on_delivery_amount);
                formData.append('is_sender_merchant', is_sender_merchant);
                formData.append('merchant_id', merchant_id);
                formData.append('sender_name', sender_name);
                formData.append('sender_address', sender_address);
                formData.append('sender_email', sender_email);
                formData.append('sender_phone', sender_phone);
                formData.append('sender_phone_alternative', sender_phone_alternative);
                formData.append('sender_country', sender_country);
                formData.append('sender_town', sender_town);
                formData.append('pickup_country', pickup_country);
                formData.append('pickup_town', pickup_town);
                formData.append('pickup_address', pickup_address);
                formData.append('receiver_name', receiver_name);
                formData.append('receiver_address', receiver_address);
                formData.append('receiver_email', receiver_email);
                formData.append('receiver_phone', receiver_phone);
                formData.append('receiver_phone_alternative', receiver_phone_alternative);
                formData.append('receiver_country', receiver_country);
                formData.append('receiver_town', receiver_town);
                formData.append('receiver_gender', receiver_gender);
                formData.append('receiver_latitude', location_latitude);
                formData.append('receiver_longitude', location_longitude);
                formData.append('scheduled_date', scheduled_date);
                formData.append('special_instruction', special_instruction);
                formData.append('payment_type', payment_type);
                formData.append('insurance', insurance);
                formData.append('order_status', order_status);
                formData.append('status_reason', status_reason);
                formData.append('payment_status', payment_status);
                formData.append('payment_method', payment_method);
                formData.append('cash_amount', cash_amount);
                formData.append('mpesa_amount', mpesa_amount);
                formData.append('cash_mpesa_amount', cash_mpesa_amount);
                formData.append('transaction_code', transaction_code);
                formData.append('rider_id', rider_id);
                formData.append('zone_id', zone_id);
                formData.append('admin_id', admin_id);
                formData.append('total_weight', total_weight);
                formData.append('amount', amount);
                formData.append('items', JSON.stringify(this.items));
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/order-edit`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Record updated successfully!');

                        }else{

                            alert('Error updating record!');
                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        alert('Error updating record!');
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },

            deleteDetails(){

                this.loader = true;

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', this.order_id);
                formData.append('causer_id', this.causer_id);

                let uri = base_url+`v1/order-delete`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.alert_success = true;
                            vm.loader = false;
                            alert('Record deleted successfully!');
                            window.location = response.data.redirect;

                        }else{

                            alert('Error deleting record!');
                            vm.loader = false;
                            vm.alert_error = true;
                        }

                    })
                    .catch(function (error) {
                        alert('Error deleting record!');
                        console.log(error);
                        vm.loader = false;
                        vm.alert_error = true;
                    });

            },


            deleteItemDetails(id){

                if(id == ""){

                    this.fetchOrderItems();
                    alert('Item deleted successfully!');

                }else{

                    this.loader = true;
                    const vm = this;
                    const formData = new FormData();
                    formData.append( 'id', id);

                    let uri = base_url+`v1/order-item-delete`;
                    axios.post(uri, formData)
                        .then(function (response) {
                            var status = response.data.success;
                            if(status === 1){
                                vm.alert_success = true;
                                vm.loader = false;
                                vm.fetchOrderItems();
                                alert('Item deleted successfully!');

                            }else{

                                alert('Error deleting record!');
                                vm.loader = false;
                                vm.alert_error = true;
                            }

                        })
                        .catch(function (error) {
                            alert('Error deleting record!');
                            console.log(error);
                            vm.loader = false;
                            vm.alert_error = true;
                        });

                }

            },

        },

        watch: {
            item_sku: function (val) {
                if(val.length > 2){
                    let uri = base_url + `v1/inventory-search-name/`+ val;
                    axios.get(uri).then((response) => {
                        this.inventories = response.data;
                    });
                }
            },
        }
    }
</script>
