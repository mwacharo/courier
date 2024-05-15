<div id="kt_header" class="header header-fixed bg-light ">
    <!--begin::Container-->
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <!--begin::Header Menu Wrapper-->
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <!--begin::Header Menu-->
            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                <!--begin::Header Nav-->
                <ul class="menu-nav">
                    <li class="menu-item menu-item-open menu-item-here menu-item-submenu menu-item-rel menu-item-open menu-item-here menu-item-active" data-menu-toggle="click" aria-haspopup="true">
                        <a href="{{ route('admin.order') }}" class="menu-link">
                            <span class="menu-text text-danger">Orders</span>
                            <i class="menu-arrow"></i>
                        </a>

                    </li>
                    <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                        <a href="{{ route('admin.order.calculator') }}" class="menu-link">
                            <span class="menu-text">Quote</span>
                            <i class="menu-arrow"></i>
                        </a>
                    </li>

                    @can('call-centre-menu')
                        <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                            <a href="#" class="menu-link  bg-dark-75-o-30" id="kt_quick_panel_toggle">
                                <i class="fa fa-phone text-primary pr-2"></i>
                                <span class="menu-text">Call Menu</span>

                            </a>
                        </li>
                    @endcan

                </ul>
                <!--end::Header Nav-->
            </div>
            <!--end::Header Menu-->
        </div>
        <!--end::Header Menu Wrapper-->
        <!--begin::Topbar-->
        <div class="topbar">
            <!--begin::Notifications-->
            <div class="dropdown">
                <!--begin::Toggle-->
                <div class="topbar-item">
                    <a href="{{ route('admin.notification') }}">Notification&nbsp; <notification-count-component></notification-count-component></a>
                </div>
                <!--end::Toggle-->
            </div>
            <!--end::Notifications-->
            <!--begin::User-->
            <div class="topbar-item">
                @if (Auth::check())
                    <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                        <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                        <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">{{ Auth::user()->first_name }}</span>
                        <span class="symbol symbol-35 symbol-light-success">
                                <span class="symbol-label font-size-h5 font-weight-bold">{{ substr(strtoupper(Auth::user()->first_name), 0, 1) }}</span>
                        </span>
                    </div>
                @endif
            </div>
            <!--end::User-->
        </div>
        <!--end::Topbar-->
    </div>
    <!--end::Container-->
</div>
