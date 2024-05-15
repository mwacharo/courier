<template>

    <!--begin::Quick Panel-->
    <main>
        <!--begin::Header-->
        <div class="offcanvas-header offcanvas-header-navs d-flex align-items-center justify-content-between mb-5">
            <ul class="nav nav-bold nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-primary flex-grow-1 px-10"
                role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#kt_quick_panel_call_summary">Summary</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#kt_quick_panel_call_area">Phone</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#kt_quick_panel_call_history">History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#kt_quick_panel_order_history">Orders</a>
                </li>
            </ul>
            <div class="offcanvas-close mt-n1 pr-5">
                <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_panel_close">
                    <i class="ki ki-close icon-xs text-muted"></i>
                </a>
            </div>
        </div>
        <!--end::Header-->

        <!--begin::Content-->
        <div class="offcanvas-content px-10">
            <div class="tab-content">
                <!--begin::Tabpane-->
                <div class="tab-pane fade show pt-3 pr-5 mr-n5 active" id="kt_quick_panel_call_summary" role="tabpanel">

                    <div class="alert alert-success" role="alert" v-if="connection_active">Active connection</div>
                    <div class="alert alert-warning" role="alert" v-else-if="connection_active === false && status === true">Sorry connection currently unavailable.  <a href="#" @click.prevent="initiateCallCentre">Click here to reconnect</a></div>

                    <!--begin::Section-->
                    <div class="mb-5">
                        <h5 class="font-weight-bold mb-5">Quick stats <a href="#" class="text-danger" style="font-size: 10px;" @click.prevent="refreshStatistics()">(Click to refresh)</a></h5>
                        <!--begin: Item-->
                        <div class="d-flex align-items-center bg-light-warning rounded p-5 mb-5">
			            <span class="svg-icon svg-icon-warning mr-5">
			                <span class="svg-icon svg-icon-lg"><!--begin::Svg Icon | path:assets/media/svg/icons/Home/Library.svg--><svg
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z"
              fill="#000000"/>
        <rect fill="#000000" opacity="0.3"
              transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519) "
              x="16.3255682" y="2.94551858" width="3" height="18" rx="1"/>
    </g>
</svg><!--end::Svg Icon--></span>			            </span>

                            <div class="d-flex flex-column flex-grow-1 mr-2">
                                <a href="#" class="font-weight-normal text-dark-75 text-hover-primary font-size-lg mb-1">Calls Completed</a>
                                <span class="text-muted font-size-sm">{{ summary_call_completed }}</span>
                            </div>
                        </div>
                        <!--end: Item-->

                        <!--begin: Item-->
                        <div class="d-flex align-items-center bg-light-success rounded p-5 mb-5">
			            <span class="svg-icon svg-icon-success mr-5">
			                <span class="svg-icon svg-icon-lg"><!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Write.svg--><svg
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
              fill="#000000" fill-rule="nonzero"
              transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) "/>
        <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
              fill="#000000" fill-rule="nonzero" opacity="0.3"/>
    </g>
</svg><!--end::Svg Icon--></span>			            </span>
                            <div class="d-flex flex-column flex-grow-1 mr-2">
                                <a href="#" class="font-weight-normal text-dark-75 text-hover-primary font-size-lg mb-1">Calls Missed</a>
                                <span class="text-muted font-size-sm">{{ summary_call_missed }}</span>
                            </div>

                        </div>
                        <!--end: Item-->

                        <!--begin: Item-->
                        <div class="d-flex align-items-center bg-light-danger rounded p-5 mb-5">
			            <span class="svg-icon svg-icon-danger mr-5">
			                <span class="svg-icon svg-icon-lg"><!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Group-chat.svg--><svg
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z"
              fill="#000000"/>
        <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z"
              fill="#000000" opacity="0.3"/>
    </g>
</svg><!--end::Svg Icon--></span>			            </span>
                            <div class="d-flex flex-column flex-grow-1 mr-2">
                                <a href="#" class="font-weight-normel text-dark-75 text-hover-primary font-size-lg mb-1">Calls Waiting</a>
                                <span class="text-muted font-size-sm">{{ 0 }}</span>
                            </div>

                        </div>
                        <!--end: Item-->

                        <!--begin: Item-->
                        <div class="d-flex align-items-center bg-light-info rounded p-5">
			            <span class="svg-icon svg-icon-info mr-5">
			                <span class="svg-icon svg-icon-lg"><!--begin::Svg Icon | path:assets/media/svg/icons/General/Attachment2.svg--><svg
                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M11.7573593,15.2426407 L8.75735931,15.2426407 C8.20507456,15.2426407 7.75735931,15.6903559 7.75735931,16.2426407 C7.75735931,16.7949254 8.20507456,17.2426407 8.75735931,17.2426407 L11.7573593,17.2426407 L11.7573593,18.2426407 C11.7573593,19.3472102 10.8619288,20.2426407 9.75735931,20.2426407 L5.75735931,20.2426407 C4.65278981,20.2426407 3.75735931,19.3472102 3.75735931,18.2426407 L3.75735931,14.2426407 C3.75735931,13.1380712 4.65278981,12.2426407 5.75735931,12.2426407 L9.75735931,12.2426407 C10.8619288,12.2426407 11.7573593,13.1380712 11.7573593,14.2426407 L11.7573593,15.2426407 Z"
              fill="#000000" opacity="0.3"
              transform="translate(7.757359, 16.242641) rotate(-45.000000) translate(-7.757359, -16.242641) "/>
        <path d="M12.2426407,8.75735931 L15.2426407,8.75735931 C15.7949254,8.75735931 16.2426407,8.30964406 16.2426407,7.75735931 C16.2426407,7.20507456 15.7949254,6.75735931 15.2426407,6.75735931 L12.2426407,6.75735931 L12.2426407,5.75735931 C12.2426407,4.65278981 13.1380712,3.75735931 14.2426407,3.75735931 L18.2426407,3.75735931 C19.3472102,3.75735931 20.2426407,4.65278981 20.2426407,5.75735931 L20.2426407,9.75735931 C20.2426407,10.8619288 19.3472102,11.7573593 18.2426407,11.7573593 L14.2426407,11.7573593 C13.1380712,11.7573593 12.2426407,10.8619288 12.2426407,9.75735931 L12.2426407,8.75735931 Z"
              fill="#000000"
              transform="translate(16.242641, 7.757359) rotate(-45.000000) translate(-16.242641, -7.757359) "/>
        <path d="M5.89339828,3.42893219 C6.44568303,3.42893219 6.89339828,3.87664744 6.89339828,4.42893219 L6.89339828,6.42893219 C6.89339828,6.98121694 6.44568303,7.42893219 5.89339828,7.42893219 C5.34111353,7.42893219 4.89339828,6.98121694 4.89339828,6.42893219 L4.89339828,4.42893219 C4.89339828,3.87664744 5.34111353,3.42893219 5.89339828,3.42893219 Z M11.4289322,5.13603897 C11.8194565,5.52656326 11.8194565,6.15972824 11.4289322,6.55025253 L10.0147186,7.96446609 C9.62419433,8.35499039 8.99102936,8.35499039 8.60050506,7.96446609 C8.20998077,7.5739418 8.20998077,6.94077682 8.60050506,6.55025253 L10.0147186,5.13603897 C10.4052429,4.74551468 11.0384079,4.74551468 11.4289322,5.13603897 Z M0.600505063,5.13603897 C0.991029355,4.74551468 1.62419433,4.74551468 2.01471863,5.13603897 L3.42893219,6.55025253 C3.81945648,6.94077682 3.81945648,7.5739418 3.42893219,7.96446609 C3.0384079,8.35499039 2.40524292,8.35499039 2.01471863,7.96446609 L0.600505063,6.55025253 C0.209980772,6.15972824 0.209980772,5.52656326 0.600505063,5.13603897 Z"
              fill="#000000" opacity="0.3"
              transform="translate(6.014719, 5.843146) rotate(-45.000000) translate(-6.014719, -5.843146) "/>
        <path d="M17.9142136,15.4497475 C18.4664983,15.4497475 18.9142136,15.8974627 18.9142136,16.4497475 L18.9142136,18.4497475 C18.9142136,19.0020322 18.4664983,19.4497475 17.9142136,19.4497475 C17.3619288,19.4497475 16.9142136,19.0020322 16.9142136,18.4497475 L16.9142136,16.4497475 C16.9142136,15.8974627 17.3619288,15.4497475 17.9142136,15.4497475 Z M23.4497475,17.1568542 C23.8402718,17.5473785 23.8402718,18.1805435 23.4497475,18.5710678 L22.0355339,19.9852814 C21.6450096,20.3758057 21.0118446,20.3758057 20.6213203,19.9852814 C20.2307961,19.5947571 20.2307961,18.9615921 20.6213203,18.5710678 L22.0355339,17.1568542 C22.4260582,16.76633 23.0592232,16.76633 23.4497475,17.1568542 Z M12.6213203,17.1568542 C13.0118446,16.76633 13.6450096,16.76633 14.0355339,17.1568542 L15.4497475,18.5710678 C15.8402718,18.9615921 15.8402718,19.5947571 15.4497475,19.9852814 C15.0592232,20.3758057 14.4260582,20.3758057 14.0355339,19.9852814 L12.6213203,18.5710678 C12.2307961,18.1805435 12.2307961,17.5473785 12.6213203,17.1568542 Z"
              fill="#000000" opacity="0.3"
              transform="translate(18.035534, 17.863961) scale(1, -1) rotate(45.000000) translate(-18.035534, -17.863961) "/>
    </g>
</svg><!--end::Svg Icon--></span>			            </span>

                            <div class="d-flex flex-column flex-grow-1 mr-2">
                                <a href="#" class="font-weight-normel text-dark-75 text-hover-primary font-size-lg mb-1">Call Period</a>
                                <span class="text-muted font-size-sm">{{ summary_call_duration }} seconds</span>
                            </div>
                        </div>
                        <!--end: Item-->
                    </div>

                    <form class="form" style="padding-top: 20px;">
                        <!--begin::Section-->
                        <div>
                            <h5 class="font-weight-bold mb-3">Status Settings</h5>
                            <div class="form-group mb-0 row align-items-center">
                                <label class="col-8 col-form-label">Enable availability:</label>
                                <div class="col-4 d-flex justify-content-end">
								<span class="switch switch-sm" :class="{'switch-success': status, 'switch-secondary': !status}">
									<label>
										<input type="checkbox" checked="checked" name="select" v-model="status" @change.prevent="updateAgentStatus()"/>
										<span></span>
									</label>
								</span>
                                </div>
                            </div>
                        </div>
                        <!--end::Section-->
                    </form>

                    <div style="padding-top: 20px;">
                        <h5 class="font-weight-bold mt-5">Call Queue <a href="#" class="text-danger" style="font-size: 10px;" @click.prevent="refreshStatistics()">(Click to refresh)</a></h5>
                        <div class="navi navi-icon-circle navi-spacer-x-0">

<ol v-if="call_waiting_histories.length > 0 && this.connection_active">
    <li v-for="(call_waiting_history, index) in call_waiting_histories" :key="index" :class="{ 'bg-color1': index % 2 === 0, 'bg-color2': index % 2 !== 0 }">
        <a href="#" class="navi-item">
        <div class="navi-link rounded">
            <div class="navi-text">
            <div class="font-weight-bold font-size-lg text-warning">
                Phone: {{ call_waiting_history.callerNumber }} : <button class="btn btn-secondary" @click.prevent="dequeueCalls(call_waiting_history.callerNumber)"> Answer</button>
            </div>
            </div>
        </div>
        </a>
    </li>
</ol>
<p v-else class="text-warning">No available calls in queue</p>
</div>
                    </div>

                    <!--end::Section-->
                </div>
                <!--end::Tabpane-->

                <!--begin::Tabpane-->
                <div class="tab-pane fade pt-2 pr-5 mr-n5" id="kt_quick_panel_call_area" role="tabpanel">

                    <div class="alert alert-success" role="alert" v-if="connection_active">Active connection</div>
                    <div class="alert alert-warning" role="alert" v-else-if="connection_active === false && status === true">Sorry connection currently unavailable.  <a href="#" @click.prevent="initiateCallCentre">Click here to reconnect</a></div>


                    <div class="login-form">
                        <!--begin::Form-->
                        <form class="form" id="kt_login_singin_form" action="">
                            <!--begin::Title-->

                            <div class="pb-2 pb-lg-15">
                                <h4 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">Call Dialer</h4>
                            </div>
                            <!--begin::Title-->

                            <!--begin::Form group-->
                            <div class="form-group">
                                <input class="form-control bg-dark-o-55 h-auto py-7 px-6 rounded-lg border-0" type="text" name="pin" autocomplete="off" v-model="dialer_phone"/>
                            </div>
                            <!--end::Form group-->

                            <div class="row">
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber(1)" :disabled="call_transfer_active = true">
                                        <div class="card-body text-center">
                                            <b>1</b>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber(2)" :disabled="call_transfer_active = true">
                                        <div class="card-body text-center">
                                            2
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber(3)" :disabled="call_transfer_active = true">
                                        <div class="card-body text-center">
                                            3
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber(4)" :disabled="call_transfer_active = true">
                                        <div class="card-body text-center">
                                            4
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber(5)" :disabled="call_transfer_active = true">
                                        <div class="card-body text-center">
                                            5
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber(6)" :disabled="call_transfer_active = true">
                                        <div class="card-body text-center">
                                            6
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber(7)" :disabled="call_transfer_active = true">
                                        <div class="card-body text-center">
                                            7
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber(8)" :disabled="call_transfer_active = true">
                                        <div class="card-body text-center">
                                            8
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber(9)" :disabled="call_transfer_active = true">
                                        <div class="card-body text-center">
                                            9
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3" v-if="call_transfer === false">
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber('#')">
                                        <div class="card-body text-center">
                                            #
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickNumber(0)">
                                        <div class="card-body text-center">
                                            0
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickDelete()">
                                        <div class="card-body text-center">
                                           Del
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3" v-else-if="call_transfer === true">
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickDial('#')">
                                        <div class="card-body text-center">
                                            #
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickDial(0)">
                                        <div class="card-body text-center">
                                            0
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card text-white bg-dark" @click.prevent="clickDial('*')">
                                        <div class="card-body text-center">
                                           *
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-6">
                                <div class="col-6" v-if="call_button === true && call_active === false && answer_button === false" @click.prevent="callDialer()">
                                    <a href="#" class="btn btn-success btn-block">
                                        <i class="flaticon2-phone"></i> Call
                                    </a>
                                </div>

                                <div class="col-6" v-if="call_button === true && call_active === true && answer_button === false">
                                    <a href="#" class="btn btn-secondary btn-block">
                                        <i class="flaticon2-phone"></i> Call
                                    </a>
                                </div>

                                <div class="col-6" v-if="answer_button === true && call_active === false && call_button === false" @click.prevent="callAnswer()">
                                    <a href="#" class="btn btn-success btn-block">
                                        <i class="flaticon2-phone"></i> Answer
                                    </a>
                                </div>

                                <div class="col-6" v-if="answer_button === true && call_active === true && call_button === false">
                                    <a href="#" class="btn btn-secondary btn-block">
                                        <i class="flaticon2-phone"></i> Answer
                                    </a>
                                </div>

                                <div class="col-6" v-if="call_active === true" @click.prevent="callHangup()">
                                    <a href="#" class="btn btn-danger btn-block">
                                        <i class="flaticon2-cancel"></i> End
                                    </a>
                                </div>

                                <div class="col-6" v-if="call_active === false">
                                    <a href="#" class="btn btn-danger btn-block">
                                        <i class="flaticon2-cancel"></i> End
                                    </a>
                                </div>

                            </div>

                            <div class="row mt-3">
                                <div class="col-6" v-if="mute_mutton === false" @click.prevent="callMute()">
                                    <a href="#" class="btn btn-warning btn-block">
                                        <i class="flaticon2-speaker"></i> Mute
                                    </a>
                                </div>

                                <div class="col-6" v-if="mute_mutton === true" @click.prevent="callUnmute()">
                                    <a href="#" class="btn btn-secondary btn-block">
                                        <i class="flaticon2-speaker"></i> Unmute
                                    </a>
                                </div>

                                <div class="col-6" v-if="hold_mutton === false" @click.prevent="callHold()">
                                    <a href="#" class="btn btn-warning btn-block">
                                        <i class="flaticon2-businesswoman"></i> Hold
                                    </a>
                                </div>

                                <div class="col-6" v-if="hold_mutton === true" @click.prevent="callUnhold">
                                    <a href="#" class="btn btn-secondary btn-block">
                                        <i class="flaticon2-businesswoman"></i> Resume
                                    </a>
                                </div>
                            </div>

                            <div class="row mt-3" v-if="call_transfer === true">
                                <div class="col-12">
                                    <h5 class="font-weight-bold mt-5">Transfer Call</h5>
                                    <select class="form-control" v-model="transfer_client_name" data-style="btn-success">
                                        <option value="">Select agent</option>
                                        <option v-for="agent in agents" :value="agent.client_name" v-if="agent.client_name !== client_name">{{ agent.client_name }}</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-3">
                                    <a href="#" class="btn btn-primary btn-block" @click.prevent="callTransfer" v-if="call_transfer === true">
                                        <i class="flaticon2-reply"></i> Complete
                                    </a>
                                </div>
                            </div>


                        </form>
                        <!--end::Form-->
                    </div>

                    <div style="padding-top: 20px;">

                        <h5 class="font-weight-bold mt-5">Call Queue <a href="#" class="text-danger" style="font-size: 10px;" @click.prevent="refreshStatistics()">(Click to refresh)</a></h5>


                        <div class="navi navi-icon-circle navi-spacer-x-0">

                            <ol v-if="call_waiting_histories.length > 0 && this.connection_active">
                                <li v-for="(call_waiting_history, index) in call_waiting_histories" :key="index" :class="{ 'bg-color1': index % 2 === 0, 'bg-color2': index % 2 !== 0 }">
                                    <a href="#" class="navi-item">
                                    <div class="navi-link rounded">
                                        <div class="navi-text">
                                        <div class="font-weight-bold font-size-lg text-warning">
                                            Phone: {{ call_waiting_history.callerNumber }} : <button class="btn btn-secondary" @click.prevent="dequeueCalls(call_waiting_history.callerNumber)"> Answer</button>
                                        </div>
                                        </div>
                                    </div>
                                    </a>
                                </li>
                            </ol>
                            <p v-else class="text-warning">No available calls in the queue</p>
                        </div>
                    </div>

                </div>
                <!--end::Tabpane-->

                <!--begin::Tabpane-->
                <div class="tab-pane fade pt-3 pr-5 mr-n5" id="kt_quick_panel_call_history" role="tabpanel">

                    <div class="alert alert-success" role="alert" v-if="connection_active">Active connection</div>
                    <div class="alert alert-warning" role="alert" v-else-if="connection_active === false && status === true">Sorry connection currently unavailable.  <a href="#" @click.prevent="initiateCallCentre">Click here to reconnect</a></div>


                    <div class="navi navi-icon-circle navi-spacer-x-0">
                        <!--begin::Item-->
                        <a href="#" class="navi-item" v-for="(call_history, index) in call_histories.slice(0, 10)">
                            <div class="navi-link rounded" v-if="call_history.direction == 'Inbound'">
                                <div class="navi-text">
                                    <div class="font-weight-bold font-size-lg">
                                        Phone: {{ call_history.callerNumber }} - INBOUND
                                    </div>
                                    <div class="text-success">
                                        {{ call_history.created_at }} (Call period: {{ call_history.durationInSeconds }}s)
                                    </div>
                                </div>
                            </div>

                            <div class="navi-link rounded" v-else>
                                <div class="navi-text">
                                    <div class="font-weight-bold font-size-lg">
                                        Phone: {{ call_history.callerNumber }} - OUTBOUND
                                    </div>
                                    <div class="text-danger">
                                        {{ call_history.created_at }} (Call period: {{ call_history.durationInSeconds }}s)
                                    </div>
                                </div>
                            </div>
                        </a>


                    </div>
                </div>
                <!--end::Tabpane-->

                <!--begin::Tabpane-->
                <div class="tab-pane fade pt-3 pr-5 mr-n5" id="kt_quick_panel_order_history" role="tabpanel">

                    <div class="alert alert-success" role="alert" v-if="connection_active">Active connection</div>
                    <div class="alert alert-warning" role="alert" v-else-if="connection_active === false && status === true">Sorry connection currently unavailable.  <a href="#" @click.prevent="initiateCallCentre">Click here to reconnect</a></div>


                    <div class="navi navi-icon-circle navi-spacer-x-0">
                        <!--begin::Item-->
                        <a href="#" class="navi-item" v-for="(order, index) in orders.slice(0, 10)" :key="index">
                            <div class="navi-link rounded">
                                <div class="navi-text">
                                    <div class="font-weight-bold font-size-lg">
                                        Order No: {{ order.order_no }} - Merchant: {{ order.sender_name }}</div>
                                    <div class="text-success">
                                        {{ order.created_at }} (Status: {{ order.order_status }})
                                    </div>
                                </div>
                            </div>
                        </a>


                    </div>
                </div>
                <!--end::Tabpane-->
            </div>
        </div>
        <!--end::Content-->

    </main>
    <!--end::Quick Panel-->

</template>

<script>

    import Africastalking from 'africastalking-client'

    export default {

        props: {
            admin_id: String,
        },

        mounted() {
            this.fetchAgents();
            this.fetchCallAgentDetails();
            this.fetchCallSummary();
            this.fetchWaitingHistory();
            this.fetchCallHistory();
        },

        created() {
            this.$eventBus.$on('make-call', this.getPhoneNumber)
        },

        data(){
            return{

                incoming: true,
                outgoing: true,
                first_name: '',
                last_name: '',
                role: '',
                token: '',
                status: false,
                sessionId: '',
                phone_number: '',
                client_name: '',
                client: null,

                dialer_phone: '',
                active_phone_number: '',

                connection_active: false,
                call_button: true,
                answer_button: false,
                call_active: false,
                mute_mutton: false,
                hold_mutton: false,
                call_transfer: false,
                call_transfer_active: false,
                transfer_client_name: '',

                summary_call_completed: 0,
                summary_call_missed: 0,
                summary_call_waiting: 0,
                summary_call_duration: 0,

                call_waiting_histories: [],
                call_histories: [],
                agents: [],
                orders: [],

                alert_error:false,
                alert_success:false,
                loader:false,

            }
        },

        methods: {

            fetchAgents() {
                let uri = base_url + `v1/call-agent-available-list`;
                axios.get(uri).then((response) => {
                    this.agents = response.data;
                });
            },

            fetchCallAgentDetails(){

                let uri = base_url+`v1/call-agent-details/`+ this.admin_id;
                axios.get(uri).then((response) => {

                    let admin = response.data;
                    if(admin){
                        this.first_name = admin.first_name;
                        this.last_name = admin.last_name;
                        this.role = admin.role;
                        this.enabled = admin.enabled;
                        this.profile_image = admin.profile_image;
                        this.token = admin.token;
                        this.sessionId = admin.sessionId;
                        this.client_name = admin.client_name;
                        this.phone_number = admin.phone_number;

                        if(admin.status === 'available'){
                            this.status = true;
                            this.generateToken();
                        }else if(admin.status === 'inactive') {
                            this.status = false;
                            this.client = null;
                        }else {
                            this.status = true;
                            this.generateToken();
                        }

                    }
                });

            },

            fetchCallSummary(){

                const vm = this;
                let uri = base_url+`v1/call-agent-summary/`+ this.admin_id;
                axios.get(uri).then((response) => {

                    let summary = response.data;
                    if(summary){
                        vm.summary_call_completed = summary.summary_call_completed;
                        vm.summary_call_missed = summary.summary_call_missed;
                        vm.summary_call_waiting = summary.summary_call_waiting;
                        vm.summary_call_duration = summary.summary_call_duration;
                    }
                });

            },

            fetchWaitingHistory(){

                const vm = this;
                let uri = base_url+`v1/call-waiting-history`;
                axios.get(uri).then((response) => {
                    this.call_waiting_histories = response.data;
                });

            },

            fetchCallHistory(){

                const vm = this;
                let uri = base_url+`v1/call-agent-history/` + this.admin_id;
                axios.get(uri).then((response) => {
                    this.call_histories = response.data;
                });

            },

            fetchOrderHistory(phone_number){

                const vm = this;
                let uri = base_url+`v1/call-order-history/` + phone_number;
                axios.get(uri).then((response) => {
                    vm.orders = response.data;
                });

            },

            generateToken(){

                const vm = this;
                const formData = new FormData();
                formData.append( 'clientName', this.client_name);

                let uri = base_url+`v1/call-centre-generate-token`;
                axios.post(uri, formData)
                    .then(function (response) {
                        vm.token = response.data;
                        vm.initiateCallCentre();
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },

            updateAgentStatus(){

                var agent_status = 'inactive'
                if(this.status === true){
                    agent_status = 'available';
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'id', vm.admin_id);
                formData.append( 'status', agent_status);

                let uri = base_url+`v1/call-agent-edit-status`;
                axios.post(uri, formData)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){

                            alert('Status updated successfully!');
                            location.reload();

                        }else{
                            alert('Failed to update status!');
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },

            initiateCallCentre(){

                const params = {
                    sounds: {
                        dialing: '/dashboard/public/sounds/dial.mp3',
                        ringing: '/dashboard/public/sounds/ring.mp3',
                    },
                };

                const vm = this;
                vm.client = new Africastalking.Client(vm.token, params);
                vm.client.on('ready', function () {

                    vm.connection_active = true;
                    vm.client.on('incomingcall', function (params) {

                        vm.call_button = false;
                        vm.answer_button = true;
                        vm.mute_mutton = false;
                        vm.hold_mutton = false;
                        vm.call_active = false;
                        vm.call_transfer = false;
                        vm.call_transfer_active = false;
                        vm.dialer_phone = params.from;
                        vm.active_phone_number = params.from;
                        vm.fetchOrderHistory(params.from);
                        vm.refreshStatistics();

                        alert(`You have an incoming call from ${params.from}`);

                    }, false);

                    vm.client.on('hangup', function (hangupCause) {

                        vm.call_button = true;
                        vm.answer_button = false;
                        vm.call_active = false;
                        vm.mute_mutton = false;
                        vm.hold_mutton = false;
                        vm.call_transfer = false;
                        vm.call_transfer_active = false;
                        vm.dialer_phone = '';
                        vm.active_phone_number = '';
                        vm.refreshStatistics();
                        alert(`Call hung up (${hangupCause.code} - ${hangupCause.reason})`)

                    }, false);

                    vm.client.on('callaccepted', function (callaccepted) {

                        vm.call_button = false;
                        vm.answer_button = true;
                        vm.mute_mutton = false;
                        vm.hold_mutton = false;
                        vm.call_active = true;
                        vm.call_transfer = true;
                        vm.call_transfer_active = false;
                        vm.active_phone_number = vm.dialer_phone;
                        vm.refreshStatistics();

                        console.log(callaccepted);

                    }, false);

                    vm.client.on('calling', function (calling) {

                        vm.call_button = true;
                        vm.answer_button = false;
                        vm.mute_mutton = false;
                        vm.hold_mutton = false;
                        vm.call_active = true;
                        vm.call_transfer = false;
                        vm.call_transfer_active = false;
                        vm.active_phone_number = vm.dialer_phone;
                        vm.refreshStatistics();

                        console.log(calling);

                    }, false);

                    vm.client.on('closed', function () {
                    vm.connection_active = false;
                    // alert('Connection failed. Please check your internet connection.');
                    }, false);

                    vm.client.on('failed', function (failedCause) {
                    vm.call_button = true;
                    vm.answer_button = false;
                    vm.call_active = false;
                    vm.mute_mutton = false;
                    vm.hold_mutton = false;
                    vm.call_transfer = false;
                    vm.call_transfer_active = false;
                    vm.dialer_phone = '';
                    vm.active_phone_number = '';
                    vm.refreshStatistics();

                    alert(`Call failed (${failedCause.code} - ${failedCause.reason}). Please try again.`);
                }, false);

                }, false);



            },

            clickNumber(number){
                this.dialer_phone = this.dialer_phone.concat(number);
            },

            clickDial(number){

                this.dialer_phone = '';
                this.dialer_phone = number;
                console.log(number);
                this.client.dtmf(number);
            },

            clickDelete(){
                this.dialer_phone = this.dialer_phone.slice(0, -1);
            },

            getPhoneNumber(value){
                this.dialer_phone = '';
                this.dialer_phone = value;
                this.callDialer();
            },

            callDialer(){

                const vm = this;
                if(this.client === null){
                    alert('Please wait for the call centre to initiate');
                    return
                }

                if(this.dialer_phone === ''){
                    alert('Please enter phone number to dial');
                    return
                }

                if(this.dialer_phone.substring(0, 2) == '07'){
                    this.dialer_phone = "254" + this.dialer_phone.substring(1);
                }else if(this.dialer_phone.substring(0, 1) == '7'){
                    this.dialer_phone = "254" + this.dialer_phone;
                }

                this.client.call('+' + this.dialer_phone);
                console.log('+' + this.dialer_phone);
            },

            callHangup(){

                if(this.client === null){
                    alert('Please wait for the call centre to initiate');
                    return
                }

                const vm = this;
                vm.client.hangup();

            },

            callAnswer(){

                if(this.client === null){
                    alert('Please wait for the call centre to initiate');
                    return
                }

                const vm = this;
                vm.client.answer();
                console.log('Call answer');
            },

            callMute(){

                if(this.client === null){
                    alert('Please wait for the call centre to initiate');
                    return
                }

                this.client.muteAudio();
                this.mute_mutton = true;
                console.log('Call mute');
            },

            callUnmute(){

                if(this.client === null){
                    alert('Please wait for the call centre to initiate');
                    return
                }

                this.client.unmuteAudio();
                this.mute_mutton = false;
                console.log('Call unmute');
            },

            callHold(){

                if(this.client === null){
                    alert('Please wait for the call centre to initiate');
                    return
                }

                this.client.hold();
                this.hold_mutton = true;
                console.log('Call hold');
            },

            callUnhold(){

                if(this.client === null){
                    alert('Please wait for the call centre to initiate');
                    return
                }

                this.client.unhold();
                this.hold_mutton = false;
                console.log('Call unhold');
6            },

            dequeueCalls(callId){
                alert(`Dequeque call from ${callId}`);
                if(this.client === null){
                    alert('Unable to process call session');
                    return
                }

                const vm = this;
                vm.client.answer();
                console.log('Call answer');
                },


            refreshStatistics(){
                this.fetchCallSummary();
                this.fetchWaitingHistory();
                this.fetchCallHistory();
            },

            callTransfer(){

                if(!this.transfer_client_name){
                    alert('Select transfer agent');
                    return
                }

                const vm = this;
                const formData = new FormData();
                formData.append( 'client_name', this.client_name);
                formData.append( 'transfer_client_name', this.transfer_client_name);

                let uri = base_url+`v1/call-centre-transfer-call`;
                axios.post(uri, formData,)
                    .then(function (response) {
                        var status = response.data.success;
                        if(status === 1){
                            vm.call_transfer_active = true;
                            alert('Call transfered successifully!');
                            vm.client.hangup();
                        }else if(status === 0){

                            alert(response.data.message);
                        }

                        else{
                            alert('Call transfer failed!')
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }

        }
    }
</script>

