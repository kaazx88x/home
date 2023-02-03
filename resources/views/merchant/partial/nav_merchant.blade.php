<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header text-center">
                <div class="dropdown profile-element">
                    <span>
                        <a href="{{ url( $route ) }}"><img src="{{ asset('assets/images/meihome_logo.png') }}" style="height:75px;"/></a>
                    </span>
                    @if($logintype == 'merchants')
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear"> <span class="block m-t-xs">
                            <strong class="font-bold">{{$merchant->mer_fname.' '.$merchant->mer_lname}}</strong>
                        </span>
                        <span class="text-muted text-xs block">{{trans('localize.setting')}}<b class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="/merchant/profile">{{trans('localize.profile')}}</a></li>
                        {{--  <li><a href="/merchant/profile/edit">{{trans('localize.edit_profile')}}</a></li>  --}}
                        <li><a href="/merchant/profile/password">{{trans('localize.change_password')}}</a></li>
                        <li><a href="/merchant/credit/log">{{trans('localize.vcoinLog')}}</a></li>
                        <li class="divider"></li>
                        <li><a href="/merchant/logout">{{trans('localize.logout')}}</a></li>
                    </ul>
                    @endif

                    @if($logintype == 'storeusers')
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear"> <span class="block m-t-xs">
                            <strong class="font-bold">Hello, {{$name}}</strong>
                        </span>
                        <span class="text-muted text-xs block">{{trans('localize.setting')}}<b class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="/store/profile/edit">{{trans('localize.edit_profile')}}</a></li>
                        <li><a href="/store/password/edit">{{trans('localize.change_password')}}</a></li>
                        <li class="divider"></li>
                        <li><a href="/store/logout">{{trans('localize.logout')}}</a></li>
                    </ul>
                    @endif
                </div>
                <div class="logo-element">
                    <img src="{{ asset('assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:40px;">
                </div>
            </li>

            @if($logintype == 'merchants')
            <li>
                <a href="/merchant/"><i class="fa fa-th-large"></i> <span class="nav-label">{{trans('localize.dashboard')}}</span> </a>
            </li>
            @if ($merchant->mer_type == 0)
            <li {{Request::is('merchant/product/*')?'class=active':''}}>
                <a href="#"><i class="fa fa-tags"></i> <span class="nav-label">{{trans('localize.manage_product')}}</span></a>
                <ul class="nav nav-second-level collapse">
                    <li {{Request::is("merchant/product/add*")?'class=active':''}}><a href="/merchant/product/add">{{trans('localize.add')}}</a></li>
                    <li {{Request::is('merchant/product/manage')?'class=active':''}}><a href="/merchant/product/manage">{{trans('localize.manage')}}</a></li>
                    {{--  <li {{Request::is('merchant/product/sold')?'class=active':''}}><a href="/merchant/product/sold">{{trans('localize.sold_product')}}</a></li>
                    <li {{Request::is('merchant/product/shipping')?'class=active':''}}><a href="/merchant/product/shipping">{{trans('localize.shipping_n_delivery')}}</a></li>  --}}
                </ul>
            </li>
            @endif
            <li {{Request::is('merchant/transaction/*')?'class=active':''}}>
                <a href="#"><i class="fa fa-exchange"></i> <span class="nav-label">{{trans('localize.transaction')}}</span></a>
                <ul class="nav nav-second-level collapse" >
                    @if ($merchant->mer_type == 0)
                     <li {{Request::is('merchant/transaction/product/orders*')?'class=active':''}}>
                        <a href="#"><i class="fa fa-dropbox"></i> {{trans('localize.online_orders')}}<span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level collapse">
                            <li {{ (Request::is('/merchant/transaction/product/orders') && (!Request::input('status')))  ?'class=active':''}}><a href="/merchant/transaction/product/orders">{{trans('localize.all')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/orders') && (Request::input('status')  == 1)) ?'class=active':''}}><a href="/merchant/transaction/product/orders?status=1">{{trans('localize.processing')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/orders') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/merchant/transaction/product/orders?status=2">{{trans('localize.packaging')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/orders') && (Request::input('status')  == 3)) ?'class=active':''}}><a href="/merchant/transaction/product/orders?status=3">{{trans('localize.shipped')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/orders') && (Request::input('status')  == 4)) ?'class=active':''}}><a href="/merchant/transaction/product/orders?status=4">{{trans('localize.completed')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/orders') && (Request::input('status')  == 5)) ?'class=active':''}}><a href="/merchant/transaction/product/orders?status=5">{{trans('localize.canceled')}}</a></li>
                            {{--  <li {{ (Request::is('merchant/transaction/product/orders') && (Request::input('status')  == 6)) ?'class=active':''}}><a href="/merchant/transaction/product/orders?status=6">{{trans('localize.refunded')}}</a></li>  --}}
                        </ul>
                    </li>
                    @else
                    <li {{Request::is('merchant/transaction/offline*')?'class=active':''}}>
                        <a href="#"><i class="fa fa-th-list"></i> {{trans('localize.order_offline')}}<span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level collapse">
                            <li {{ (Request::is('merchant/transaction/offline') && (is_null(Request::input('status'))))  ?'class=active':''}}><a href="/merchant/transaction/offline">{{trans('localize.all')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/offline') && (Request::input('status')  == 0) && (!is_null(Request::input('status')))) ?'class=active':''}}><a href="/merchant/transaction/offline?status=0">{{trans('localize.unpaid')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/offline') && (Request::input('status')  == 1)) ?'class=active':''}}><a href="/merchant/transaction/offline?status=1">{{trans('localize.paid')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/offline') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/merchant/transaction/offline?status=2">{{trans('localize.cancel_by_member')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/offline') && (Request::input('status')  == 3)) ?'class=active':''}}><a href="/merchant/transaction/offline?status=3">{{trans('localize.cancel_by_merchant')}}</a></li>
                        </ul>
                    </li>
                    @endif
                    {{--  <li {{Request::is('merchant/transaction/product/coupons*')?'class=active':''}}>
                        <a href="#"><i class="fa fa-barcode"></i> {{trans('localize.coupon_orders')}}<span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level collapse">
                            <li {{ (Request::is('/merchant/transaction/product/coupons') && (!Request::input('status')))  ?'class=active':''}}><a href="/merchant/transaction/product/coupons">{{trans('localize.all')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/coupons') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/merchant/transaction/product/coupons?status=2">{{trans('localize.pending')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/coupons') && (Request::input('status')  == 4)) ?'class=active':''}}><a href="/merchant/transaction/product/coupons?status=4">{{trans('localize.completed')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/coupons') && (Request::input('status')  == 5 || Request::input('status')  == 7)) ?'class=active':''}}><a href="/merchant/transaction/product/coupons?status=5">{{trans('localize.canceled')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/coupons') && (Request::input('status')  == 6)) ?'class=active':''}}><a href="/merchant/transaction/product/coupons?status=6">{{trans('localize.refunded')}}</a></li>
                        </ul>
                    </li>
                    <li {{Request::is('merchant/transaction/product/tickets*')?'class=active':''}}>
                        <a href="#"><i class="fa fa-barcode"></i> {{trans('localize.ticket_orders')}}<span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level collapse">
                            <li {{ (Request::is('/merchant/transaction/product/tickets') && (!Request::input('status')))  ?'class=active':''}}><a href="/merchant/transaction/product/tickets">{{trans('localize.all')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/tickets') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/merchant/transaction/product/tickets?status=2">{{trans('localize.pending')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/tickets') && (Request::input('status')  == 4)) ?'class=active':''}}><a href="/merchant/transaction/product/tickets?status=4">{{trans('localize.completed')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/tickets') && (Request::input('status')  == 5 || Request::input('status')  == 7)) ?'class=active':''}}><a href="/merchant/transaction/product/tickets?status=5">{{trans('localize.canceled')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/tickets') && (Request::input('status')  == 6)) ?'class=active':''}}><a href="/merchant/transaction/product/tickets?status=6">{{trans('localize.refunded')}}</a></li>
                        </ul>
                    </li>  --}}
                    <li {{Request::is('merchant/transaction/product/ecards*')?'class=active':''}}>
                        <a href="#"><i class="fa fa-barcode"></i> {{trans('localize.e-card.orders')}}<span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level collapse">
                            <li {{ (Request::is('/merchant/transaction/product/ecards') && (!Request::input('status')))  ?'class=active':''}}><a href="/merchant/transaction/product/ecards">{{trans('localize.all')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/ecards') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/merchant/transaction/product/ecards?status=2">{{trans('localize.pending')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/ecards') && (Request::input('status')  == 4)) ?'class=active':''}}><a href="/merchant/transaction/product/ecards?status=4">{{trans('localize.completed')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/ecards') && (Request::input('status')  == 5 || Request::input('status')  == 7)) ?'class=active':''}}><a href="/merchant/transaction/product/ecards?status=5">{{trans('localize.canceled')}}</a></li>
                            <li {{ (Request::is('merchant/transaction/product/ecards') && (Request::input('status')  == 6)) ?'class=active':''}}><a href="/merchant/transaction/product/ecards?status=6">{{trans('localize.refunded')}}</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li {{Request::is('merchant/fund/*')?'class=active':''}}>
                <a href="#"><i class="fa fa-money"></i> <span class="nav-label">{{trans('localize.fund_request')}}</span></a>
                <ul class="nav nav-second-level collapse">
                    <li {{Request::is('merchant/fund/report')?'class=active':''}}><a href="/merchant/fund/report">{{trans('localize.report')}}</a></li>
                    <li {{Request::is('merchant/fund/withdraw')?'class=active':''}}><a href="/merchant/fund/withdraw">{{trans('localize.withdraw')}}</a></li>
                </ul>
            </li>
            <li {{Request::is('merchant/store/*')?'class=active':''}}>
                <a href="#"><i class="fa fa-building"></i> <span class="nav-label">@lang('localize.manage_store')</span></a>
                <ul class="nav nav-second-level collapse">
                    {{--  <li {{Request::is('merchant/store/add')?'class=active':''}}><a href="/merchant/store/add">{{trans('localize.add')}}</a></li>  --}}
                    <li {{Request::is('merchant/store/manage')?'class=active':''}}><a href="/merchant/store/manage">@lang('localize.store')</a></li>
                    <li {{Request::is('merchant/store/user/manage')?'class=active':''}}><a href="/merchant/store/user/manage">@lang('localize.store_user')</a></li>
                </ul>
            </li>
            @endif


            @if($logintype == 'storeusers')
            <li>
                <a href="/store/"><i class="fa fa-th-large"></i> <span class="nav-label">{{trans('localize.dashboard')}}</span> </a>
            </li>
            @if ($merchant->mer_type == 0)
            <li {{Request::is('store/product/*')?'class=active':''}}>
                <a href="#"><i class="fa fa-tags"></i> <span class="nav-label">{{trans('localize.manage_product')}}</span></a>
                <ul class="nav nav-second-level collapse">
                    <li {{Request::is('store/product/manage')?'class=active':''}}><a href="/store/product/manage">{{trans('localize.manage')}}</a></li>
                </ul>
            </li>
            @endif
            <li {{Request::is('store/transaction/*')?'class=active':''}}>
                <a href="#"><i class="fa fa-exchange"></i> <span class="nav-label">{{trans('localize.transaction')}}</span></a>
                <ul class="nav nav-second-level collapse" >
                    @if ($merchant->mer_type == 0)
                    <li {{Request::is('store/transaction/product/orders*')?'class=active':''}}>
                        <a href="#"><i class="fa fa-dropbox"></i> {{trans('localize.product')}}<span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level collapse">
                            <li {{ (Request::is('store/transaction/product/orders') && (!Request::input('status')))  ?'class=active':''}}><a href="/store/transaction/product/orders">{{trans('localize.all')}}</a></li>
                            <li {{ (Request::is('store/transaction/product/orders') && (Request::input('status')  == 1)) ?'class=active':''}}><a href="/store/transaction/product/orders?status=1">{{trans('localize.processing')}}</a></li>
                            <li {{ (Request::is('store/transaction/product/orders') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/store/transaction/product/orders?status=2">{{trans('localize.packaging')}}</a></li>
                            <li {{ (Request::is('store/transaction/product/orders') && (Request::input('status')  == 3)) ?'class=active':''}}><a href="/store/transaction/product/orders?status=3">{{trans('localize.shipped')}}</a></li>
                            <li {{ (Request::is('store/transaction/product/orders') && (Request::input('status')  == 4)) ?'class=active':''}}><a href="/store/transaction/product/orders?status=4">{{trans('localize.completed')}}</a></li>
                            <li {{ (Request::is('store/transaction/product/orders') && (Request::input('status')  == 5)) ?'class=active':''}}><a href="/store/transaction/product/orders?status=5">{{trans('localize.canceled')}}</a></li>
                        </ul>
                    </li>
                    @else
                    <li {{Request::is('store/transaction/offline*')?'class=active':''}}>
                        <a href="#"><i class="fa fa-th-list"></i> {{trans('localize.order_offline')}}<span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level collapse">
                            <li {{ (Request::is('store/transaction/offline') && (is_null(Request::input('status'))))  ?'class=active':''}}><a href="/store/transaction/offline">{{trans('localize.all')}}</a></li>
                            <li {{ (Request::is('store/transaction/offline') && (Request::input('status')  == 0) && (!is_null(Request::input('status')))) ?'class=active':''}}><a href="/store/transaction/offline?status=0">{{trans('localize.unpaid')}}</a></li>
                            <li {{ (Request::is('store/transaction/offline') && (Request::input('status')  == 1)) ?'class=active':''}}><a href="/store/transaction/offline?status=1">{{trans('localize.paid')}}</a></li>
                            <li {{ (Request::is('store/transaction/offline') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/store/transaction/offline?status=2">{{trans('localize.cancel_by_member')}}</a></li>
                            <li {{ (Request::is('store/transaction/offline') && (Request::input('status')  == 3)) ?'class=active':''}}><a href="/store/transaction/offline?status=3">{{trans('localize.cancel_by_merchant')}}</a></li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
        </ul>

    </div>
</nav>
