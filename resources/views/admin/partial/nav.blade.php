<div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header text-center">
                    <div class="dropdown profile-element">
                        <span>
                             <a href="{{ url('admin/login') }}"><img src="{{ asset('assets/images/meihome_logo.png') }}" style="height:75px;"/></a>
                        </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                            <span class="block m-t-xs">
                            <strong class="font-bold">{{ $admin->username }}</strong>
                            {{-- </span>
                                <span class="text-muted text-xs block">Profile <b class="caret"></b></span>
                            </span> --}}
                        </a>
                        {{-- <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="profile.html">My Profile</a></li>
                            <li><a href="contacts.html">Edit Profile</a></li>
                            <li><a href="mailbox.html">Change Password</a></li>
                            <li class="divider"></li>
                            <li><a href="login.html">Logout</a></li>
                        </ul> --}}
                    </div>
                    <div class="logo-element">
                        <img src="{{ asset('assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:40px;">
                    </div>
                </li>
                <li>
                    <a href="{{url('/admin')}}"><i class="fa fa-th-large"></i> <span class="nav-label">{{trans('localize.Dashboards')}}</span> </a>
                </li>

                @if(in_array('adminmanagelist', $admin_permission) || in_array('adminmanageuserlist', $admin_permission))
                <li {{Request::is('admin/administrator*')?'class=active':''}}>
                    @if(in_array('adminmanageuserlist', $admin_permission) || in_array('adminmanagelist', $admin_permission))
                    <a href="#"><i class="fa fa-users"></i> <span class="nav-label">{{trans('localize.Administrator')}}</span><span class="fa arrow"></span></a>
                    @endif
                    <ul class="nav nav-second-level collapse">
                        <li {{(Request::is('admin/administrator/role*'))?'class=active':''}}>
                            <a href="#"><i class="fa fa-key"></i> {{trans('localize.Role')}}<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level collapse">
                                @if(in_array('adminmanagelist', $admin_permission) && in_array('adminmanagecreate', $admin_permission))
                                <li {{Request::is('admin/administrator/role/add')?'class=active':''}}><a href="{{url('admin/administrator/role/add')}}"><i class="fa fa-plus"></i>{{trans('localize.add')}}</a></li>
                                @endif

                                @if(in_array('adminmanagelist', $admin_permission))
                                <li {{Request::is('admin/administrator/role')?'class=active':''}}><a href="{{url('admin/administrator/role')}}"><i class="fa fa-pencil-square-o"></i>{{trans('localize.manage')}}</a></li>
                                @endif
                            </ul>
                        </li>
                        <li {{(Request::is('admin/administrator/user*'))?'class=active':''}}>
                            @if(in_array('adminmanageuserlist', $admin_permission))
                            <a href="#"><i class="fa fa-users"></i>{{trans('localize.User')}}<span class="fa arrow"></span></a>
                            @endif
                            <ul class="nav nav-third-level collapse">
                                @if(in_array('adminmanageusercreate', $admin_permission) && in_array('adminmanageuserlist', $admin_permission))
                                <li {{Request::is('admin/administrator/user/add')?'class=active':''}}><a href="{{url('admin/administrator/user/add')}}"><i class="fa fa-plus"></i>{{trans('localize.add')}}</a></li>
                                @endif

                                @if(in_array('adminmanageuserlist', $admin_permission))
                                <li {{Request::is('admin/administrator/user')?'class=active':''}}><a href="{{url('admin/administrator/user')}}"><i class="fa fa-pencil-square-o"></i>{{trans('localize.manage')}}</a></li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </li>
                @endif

                <li {{Request::is('admin/setting/*') || Request::is('setting/general')?'class=active':''}}>
                    @if(in_array('settingcourierlist', $admin_permission) ||
                        in_array('settingcountrieslist', $admin_permission) ||
                        in_array('settingcategoriesonlinelist', $admin_permission) ||
                        in_array('settingcategoriesofflinelist', $admin_permission) ||
                        in_array('settingcmslist', $admin_permission) ||
                        in_array('settingbannerlist', $admin_permission) ||
                        in_array('settingfilterlist', $admin_permission) ||
                        in_array('settingcommissionlist', $admin_permission)  )
                    <a href="#"><i class="fa fa-cogs"></i> <span class="nav-label">{{trans('localize.Settings')}}</span><span class="fa arrow"></span></a>
                    @endif
                    <ul class="nav nav-second-level collapse">
                        @if(in_array('settingcourierlist', $admin_permission))
                        <li {{Request::is('admin/setting/courier*')?'class=active':''}}><a href="{{url('admin/setting/courier')}}"><i class="fa fa-truck"></i> {{trans('localize.Courier')}}</a></li>
                        @endif
                        @if(in_array('settingcountrieslist', $admin_permission))
                        <li {{Request::is('admin/setting/country*')?'class=active':''}}><a href="{{url('admin/setting/country')}}"><i class="fa fa-globe"></i> {{trans('localize.Countries')}}</a></li>
                        {{-- <li {{Request::is('admin/setting/city*')?'class=active':''}}><a href="{{url('admin/setting/city')}}"><i class="fa fa-building"></i> Cities</a></li> --}}
                        {{-- <li {{Request::is('admin/setting/state*')?'class=active':''}}><a href="{{url('admin/setting/state')}}"><i class="fa fa-building"></i> States</a></li> --}}
                        @endif

						@if(in_array('settingcategoriesonlinelist', $admin_permission) || in_array('settingcategoriesofflinelist', $admin_permission))
                        <li {{(Request::is('admin/setting/category*') || Request::is('admin/setting/offline_category*'))?'class=active':''}}>
                            <a href="#"><i class="fa fa-list-alt"></i> {{trans('localize.Categories')}}<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level collapse">
                                @if(in_array('settingcategoriesonlinelist', $admin_permission))
                                <li {{Request::is('admin/setting/category*')?'class=active':''}}><a href="{{url('admin/setting/category/listing')}}">{{trans('localize.Online')}}</a></li>
                                @endif
                                @if(in_array('settingcategoriesofflinelist', $admin_permission))
                                <li {{Request::is('admin/setting/offline_category*')?'class=active':''}}><a href="{{url('admin/setting/offline_category/listing')}}">{{trans('localize.Offline')}}</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif

                        @if(in_array('settingcmslist', $admin_permission))
                        <li {{Request::is('admin/setting/cms*')?'class=active':''}}><a href="{{url('admin/setting/cms')}}"><i class="fa fa-pencil-square-o"></i> CMS</a></li>
                        @endif

                        @if(in_array('settingbannerlist', $admin_permission))
                        <li {{Request::is('admin/setting/banner*')?'class=active':''}}><a href="{{url('admin/setting/banner')}}"><i class="fa fa-image"></i> {{trans('localize.Banner')}}</a></li>
                        @endif

                        {{-- <li {{(Request::is('admin/setting/color*') || Request::is('admin/setting/size*'))?'class=active':''}}>
                            <a href="#"><i class="fa fa-anchor"></i> Attributes<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level collapse">
                                <li {{Request::is('admin/setting/color*')?'class=active':''}}><a href="{{url('admin/setting/color')}}">Color</a></li>
                                <li {{Request::is('admin/setting/size*')?'class=active':''}}><a href="{{url('admin/setting/size')}}">Size</a></li>
                            </ul>
                        </li> --}}

                        @if(in_array('settingfilterlist', $admin_permission))
                        <li {{(Request::is('admin/setting/filter*'))?'class=active':''}}>
                            <a href="{{url('admin/setting/filter')}}"><i class="fa fa-anchor"></i> {{trans('localize.Filter')}}</a>
                        </li>
                        @endif

                        @if(in_array('settingcommissionlist', $admin_permission))
                        <li {{Request::is('admin/setting/commission')?'class=active':''}}><a href="{{url('admin/setting/commission')}}"><i class="fa fa-money"></i> {{trans('localize.Commission')}}</a></li>
                        @endif
                    </ul>
                </li>
                <li {{Request::is('admin/product/*') || Request::is('admin/product')?'class=active':''}}>
                    @if(in_array('productmanagelist', $admin_permission) ||  in_array('productshippinganddeliverylist', $admin_permission))
                    <a href="#"><i class="fa fa-cubes"></i> <span class="nav-label">{{trans('localize.Products')}}</span><span class="fa arrow"></span></a>
                    @endif
                    <ul class="nav nav-second-level collapse">
                        @if(in_array('productmanagecreate', $admin_permission) && in_array('productmanagelist', $admin_permission))
                        <li {{Request::is('admin/product/add')?'class=active':''}}><a href="{{url('admin/product/add')}}"><i class="fa fa-plus"></i> {{trans('localize.add')}}</a></li>
                        @endif

                        @if(in_array('productmanagelist', $admin_permission))
                        <li {{Request::is('admin/product/manage')?'class=active':''}}><a href="{{url('admin/product/manage')}}"><i class="fa fa-pencil-square-o"></i> {{trans('localize.manage')}}</a></li>
                        @endif
                    </ul>
                </li>
                <li {{Request::is('admin/customer/*') || Request::is('admin/customer')?'class=active':''}}>
                    @if(in_array('customermanagelist', $admin_permission)  || in_array('customerinquirieslist', $admin_permission))
                    <a href="#"><i class="fa fa-users"></i> <span class="nav-label">{{trans('localize.Customers')}}</span><span class="fa arrow"></span></a>
                    @endif
                    <ul class="nav nav-second-level collapse">
                        @if(in_array('customermanagecreate', $admin_permission) && in_array('customermanagelist', $admin_permission))
                        <li {{Request::is('admin/customer/add')?'class=active':''}}><a href="{{url('admin/customer/add')}}"><i class="fa fa-plus"></i> {{trans('localize.add')}}</a></li>
                        @endif

                        @if(in_array('customermanagelist', $admin_permission))
                        <li {{Request::is('admin/customer/manage')?'class=active':''}}><a href="{{url('admin/customer/manage')}}"><i class="fa fa-pencil-square-o"></i>{{trans('localize.manage')}}</a></li>
                        @endif

                        @if(in_array('customerinquirieslist', $admin_permission))
                        <li {{Request::is('admin/customer/inquiries')?'class=active':''}}><a href="{{url('admin/customer/inquiries')}}"><i class="fa fa-question-circle"></i>{{trans('localize.Inquiries')}}</a></li>
                        @endif
                    </ul>
                </li>
                <li {{Request::is('admin/transaction/*')?'class=active':''}} >
                    @if(in_array('transactiononlineorderslist', $admin_permission) || in_array('transactionofflineorderslist', $admin_permission) || in_array('transactionfundrequestlist', $admin_permission) )
                    <a href="#"><i class="fa fa-money"></i> <span class="nav-label">{{trans('localize.Transactions')}}</span><span class="fa arrow"></span></a>
                    @endif
                    <ul class="nav nav-second-level collapse">
                        @if(in_array('transactiononlineorderslist', $admin_permission))
                        <li {{Request::is('admin/transaction/product/orders')?'class=active':''}}>
                            <a href="#"><i class="fa fa-dropbox"></i> {{trans('localize.Online_Orders')}}<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level collapse">
                                <li {{ (Request::is('admin/transaction/product/orders') && (!Request::input('status')))  ?'class=active':''}}><a href="/admin/transaction/product/orders">{{trans('localize.All')}}</a></li>
                                <li {{ (Request::is('admin/transaction/product/orders') && (Request::input('status')  == 1)) ?'class=active':''}}><a href="/admin/transaction/product/orders?status=1">{{trans('localize.Processing')}}</a></li>
                                <li {{ (Request::is('admin/transaction/product/orders') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/admin/transaction/product/orders?status=2">{{trans('localize.Packaging')}}</a></li>
                                <li {{ (Request::is('admin/transaction/product/orders') && (Request::input('status')  == 3)) ?'class=active':''}}><a href="/admin/transaction/product/orders?status=3">{{trans('localize.Shipped')}}</a></li>
                                <li {{ (Request::is('admin/transaction/product/orders') && (Request::input('status')  == 4)) ?'class=active':''}}><a href="/admin/transaction/product/orders?status=4">{{trans('localize.Completed')}}</a></li>
                                <li {{ (Request::is('admin/transaction/product/orders') && (Request::input('status')  == 5)) ?'class=active':''}}><a href="/admin/transaction/product/orders?status=5">{{trans('localize.Canceled')}}</a></li>
                            </ul>
                        </li>
                        @endif

                        @if(in_array('transactionofflineorderslist', $admin_permission))
                        <li {{Request::is('admin/transaction/offline*')?'class=active':''}}>
                            <a href="#"><i class="fa fa-th-list"></i> {{trans('localize.order_offline')}}<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level collapse">
                                <li {{ (Request::is('admin/transaction/offline') && (is_null(Request::input('status'))))  ?'class=active':''}}><a href="/admin/transaction/offline">{{trans('localize.All')}}</a></li>
                                <li {{ (Request::is('admin/transaction/offline') && (Request::input('status')  == 0) && (!is_null(Request::input('status')))) ?'class=active':''}}><a href="/admin/transaction/offline?status=0">{{trans('localize.Unpaid')}}</a></li>
                                <li {{ (Request::is('admin/transaction/offline') && (Request::input('status')  == 1)) ?'class=active':''}}><a href="/admin/transaction/offline?status=1">{{trans('localize.Paid')}}</a></li>
                                <li {{ (Request::is('admin/transaction/offline') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/admin/transaction/offline?status=2">{{trans('localize.Cancel_By_Member')}}</a></li>
                                <li {{ (Request::is('admin/transaction/offline') && (Request::input('status')  == 3)) ?'class=active':''}}><a href="/admin/transaction/offline?status=3">{{trans('localize.Cancel_By_Merchant')}}</a></li>
                            </ul>
                        </li>
                        @endif

                        @if(in_array('transactionfundrequestlist', $admin_permission))
                        <li {{Request::is('admin/transaction/fund-request')?'class=active':''}}>
                            <a href="#"><i class="fa fa-book"></i> {{trans('localize.Fund_Request')}}<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level collapse">
                                <li {{ (Request::is('admin/transaction/fund-request') && (is_null(Request::input('status'))))  ?'class=active':''}}><a href="/admin/transaction/fund-request">{{trans('localize.All')}}</a></li>
                                <li {{ (Request::is('admin/transaction/fund-request') && (Request::input('status')  == 0) && (!is_null(Request::input('status')))) ?'class=active':''}}><a href="/admin/transaction/fund-request?status=0">{{trans('localize.Pending')}}</a></li>
                                <li {{ (Request::is('admin/transaction/fund-request') && (Request::input('status')  == 1)) ?'class=active':''}}><a href="/admin/transaction/fund-request?status=1">{{trans('localize.Approved')}}</a></li>
                                <li {{ (Request::is('admin/transaction/fund-request') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/admin/transaction/fund-request?status=2">{{trans('localize.Declined')}}</a></li>
                                <li {{ (Request::is('admin/transaction/fund-request') && (Request::input('status')  == 3)) ?'class=active':''}}><a href="/admin/transaction/fund-request?status=3">{{trans('localize.Paid')}}</a></li></a></li>
                            </ul>
                        </li>
                        @endif

                        <li {{Request::is('admin/transaction/product/coupons')?'class=active':''}}>
                            <a href="#"><i class="fa fa-barcode"></i> {{ trans('localize.coupon_orders') }}<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level collapse">
                                <li {{ (Request::is('admin/transaction/product/coupons') && (!Request::input('status')))  ?'class=active':''}}><a href="/admin/transaction/product/coupons">{{ trans('localize.all') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/coupons') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/admin/transaction/product/coupons?status=2">{{ trans('localize.pending') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/coupons') && (Request::input('status')  == 4)) ?'class=active':''}}><a href="/admin/transaction/product/coupons?status=4">{{ trans('localize.completed') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/coupons') && (Request::input('status')  == 5 || Request::input('status')  == 7)) ?'class=active':''}}><a href="/admin/transaction/product/coupons?status=5">{{ trans('localize.canceled') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/coupons') && (Request::input('status')  == 6)) ?'class=active':''}}><a href="/admin/transaction/product/coupons?status=6">{{ trans('localize.refunded') }}</a></li>
                            </ul>
                        </li>

                        {{-- <li {{Request::is('admin/transaction/product/tickets')?'class=active':''}}>
                            <a href="#"><i class="fa fa-barcode"></i> {{ trans('localize.ticket_orders') }}<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level collapse">
                                <li {{ (Request::is('admin/transaction/product/tickets') && (!Request::input('status')))  ?'class=active':''}}><a href="/admin/transaction/product/tickets">{{ trans('localize.all') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/tickets') && (Request::input('status')  == 2)) ?'class=active':''}}><a href="/admin/transaction/product/tickets?status=2">{{ trans('localize.pending') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/tickets') && (Request::input('status')  == 4)) ?'class=active':''}}><a href="/admin/transaction/product/tickets?status=4">{{ trans('localize.completed') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/tickets') && (Request::input('status')  == 5 || Request::input('status')  == 7)) ?'class=active':''}}><a href="/admin/transaction/product/tickets?status=5">{{ trans('localize.canceled') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/tickets') && (Request::input('status')  == 6)) ?'class=active':''}}><a href="/admin/transaction/product/tickets?status=6">{{ trans('localize.refunded') }}</a></li>
                            </ul>
                        </li>  --}}
                        <li {{Request::is('admin/transaction/product/ecards')?'class=active':''}}>
                            <a href="#"><i class="fa fa-barcode"></i> {{ trans('localize.e-card.orders') }}<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level collapse">
                                <li {{ (Request::is('admin/transaction/product/ecards') && (!Request::input('status')))  ?'class=active':''}}><a href="/admin/transaction/product/ecards">{{ trans('localize.all') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/ecards') && (Request::input('status') == 2)) ?'class=active':''}}><a href="/admin/transaction/product/ecards?status=2">{{ trans('localize.pending') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/ecards') && (Request::input('status') == 4)) ?'class=active':''}}><a href="/admin/transaction/product/ecards?status=4">{{ trans('localize.completed') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/ecards') && (Request::input('status') == 5 || Request::input('status') == 7)) ?'class=active':''}}><a href="/admin/transaction/product/ecards?status=5">{{ trans('localize.canceled') }}</a></li>
                                <li {{ (Request::is('admin/transaction/product/ecards') && (Request::input('status') == 6)) ?'class=active':''}}><a href="/admin/transaction/product/ecards?status=6">{{ trans('localize.refunded') }}</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li {{Request::is('admin/merchants/*') || Request::is('admin/merchant/*')?'class=active':''}}>
                    @if(in_array('merchantonlinelist', $admin_permission))
                    <a href="#"><i class="fa fa-users"></i> <span class="nav-label">{{trans('localize.Merchants')}}</span><span class="fa arrow"></span></a>
                    @endif
                    <ul class="nav nav-second-level collapse">
                        @if(in_array('merchantonlinecreate', $admin_permission) && in_array('merchantonlinelist', $admin_permission))
                        <li {{Request::is('admin/merchant/add')?'class=active':''}}><a href="{{url('admin/merchant/add')}}"><i class="fa fa-plus"></i> {{trans('localize.add')}}</a></li>
                        @endif

                        @if(in_array('merchantonlinelist', $admin_permission))
                        <li {{Request::is('admin/merchant/manage/online')?'class=active':''}}><a href="{{url('admin/merchant/manage/online')}}"><i class="fa fa-square-o"></i> {{trans('localize.Online')}}</a></li>
                        <li {{Request::is('admin/merchant/manage/offline')?'class=active':''}}><a href="{{url('admin/merchant/manage/offline')}}"><i class="fa fa-square"></i> {{trans('localize.Offline')}}</a></li>
                        @endif
                    </ul>
                </li>

				@if(in_array('storelist', $admin_permission))
                <li {{Request::is('admin/store/manage/online') || Request::is('admin/store/manage/offline')?'class=active':''}}>
                    <a href="#"><i class="fa fa-building"></i> <span class="nav-label">{{trans('localize.Stores')}}</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li {{Request::is('admin/store/manage/online')?'class=active':''}}><a href="{{url('admin/store/manage/online')}}"><i class="fa fa-square-o"></i> {{trans('localize.Online')}}</a></li>
                        <li {{Request::is('admin/store/manage/offline')?'class=active':''}}><a href="{{url('admin/store/manage/offline')}}"><i class="fa fa-square"></i> {{trans('localize.Offline')}}</a></li>
                    </ul>
                </li>
				@endif
				@if(in_array('storelist', $admin_permission))
                <li {{Request::is('admin/pending/*') || Request::is('admin/store/merchant/pending') ?'class=active':''}}>
                    <a href="#"><i class="fa fa-exclamation"></i> <span class="nav-label">{{trans('localize.Pending_Review')}}</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li {{ Request::is('admin/store/merchant/pending')?'class=active':'' }}><a href="{{url('admin/store/merchant/pending')}}"><i class="fa fa-building"></i> <span class="nav-label">{{trans('localize.Stores')}}</span></a></li>
                    </ul>
                </li>
				@endif

				@if(in_array('viewsalesreport', $admin_permission) ||
					in_array('viewmicreditsummary', $admin_permission) ||
					in_array('viewmicreditlog', $admin_permission) ||
					in_array('credittransfer', $admin_permission) )
                <li {{Request::is('admin/report/*') ? 'class=active' : ''}}>
                    <a href="#"><i class="fa fa-book"></i> <span class="nav-label">{{trans('localize.report')}}</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
						@if(in_array('credittransfer', $admin_permission))
                        <li {{ Request::is('admin/report/credit-transfer')?'class=active':'' }}><a href="{{url('admin/report/credit-transfer')}}"><i class="fa fa-bolt"></i> <span class="nav-label">{{trans('localize.credit_transfer')}}</span></a></li>
						@endif
						@if(in_array('viewsalesreport', $admin_permission))
						<li {{ Request::is('admin/report/sale')?'class=active':'' }}><a href="{{url('admin/report/sales')}}"><i class="fa fa-bolt"></i> <span class="nav-label">{{trans('localize.sales')}}</span></a></li>
						@endif
						@if(in_array('viewmicreditlog', $admin_permission))
						<li {{ Request::is('admin/report/credit-log')?'class=active':'' }}><a href="{{url('admin/report/credit-log')}}"><i class="fa fa-bolt"></i> <span class="nav-label">{{trans('localize.credit_log')}}</span></a></li>
						@endif
						@if(in_array('viewmicreditsummary', $admin_permission))
						<li {{ Request::is('admin/report/credit-summary')?'class=active':'' }}><a href="{{url('admin/report/credit-summary')}}"><i class="fa fa-bolt"></i> <span class="nav-label">{{trans('localize.credit_summary')}}</span></a></li>
						@endif
					</ul>
                </li>
				@endif

				@if(in_array('luckydraw', $admin_permission))
                <li {{Request::is('admin/lucky_draw/*') ? 'class=active' : ''}}>
                    <a href="{{url('admin/lucky_draw/manage')}}"><i class="fa fa-gift"></i> <span class="nav-label">{{trans('localize.lucky_draw')}}</span> </a>
                </li>
				@endif
            </ul>

        </div>
    </nav>

    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                </div>

                <ul>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message">{{trans('localize.welcome_to')}} {{ config('app.name') }} {{trans('localize.admin')}}</span>
                    </li>

                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#"><i class="fa fa-clock-o"></i><span class="mhidden"> {{ $country_timezone }}</span></a>
                        <ul class="dropdown-menu dropdown-messages animated fadeInRight m-t-xs" style="width:auto;">
                            @foreach($locale_countries as $key => $co)
                                <li><a href="javascript:void(0)" class="countryloc" data-id="{{$co->co_id}}"><span class="flag-icon flag-icon-{{strtolower($co->co_code)}}"></span> {{$co->timezone}}</a></li>
                            @endforeach
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="javascript:void(0)"><i class="fa fa-language"></i><span class="mhidden"> @lang('localize.language')</span></a>
                        <ul class="dropdown-menu dropdown-messages animated fadeInRight m-t-xs" style="width:auto;">
                            <li><a href="javascript:void(0)" class="locale" data-lang="en">English</a></li>
                            <li><a href="javascript:void(0)" class="locale" data-lang="cn">简体中文</a></li>
                            <li><a href="javascript:void(0)" class="locale" data-lang="cnt">繁体中文</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="/admin/logout">
                            <i class="fa fa-sign-out"></i> @lang('localize.logout')
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- content start here -->
        @yield('content')
        <!-- content end here -->
        <div class="footer" >
            <div class="pull-right">
                <strong>Back Office</strong>
            </div>
            <div>
                &copy; {{ date("Y") }} {{ config('app.name') }}
            </div>
        </div>

    </div>
</div>
