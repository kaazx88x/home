<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name') }}</title>

    <link href="/backend/css/bootstrap.min.css" rel="stylesheet">
    <link href="/backend/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="/backend/css/animate.css" rel="stylesheet">
    <link href="/backend/css/style.css" rel="stylesheet">
    <link href="/backend/css/custom.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/web/lib/sweetalert/sweetalert.css" />
    <link href="/backend/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <link href="/backend/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
    <style type="text/css">
        .gllpMap {
            height: 228px;
        }
    </style>
    @yield('style')
</head>
<body>
    <div id="wrapper">
        @include('merchant.partial.nav_merchant')

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="javascript:void(0)"><i class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message">
                                @lang('localize.welcome')
                                @if($logintype == 'merchants')
                                    @lang('localize.merchant_user')
                                @elseif($logintype == 'storeusers')
                                    @lang('localize.store_user')
                                @endif
                            </span>
                        </li>
                        <li class="dropdown credit-amount">
                            <a href="{{ url('merchant/credit/log') }}" style="text-decoration:none; color: inherit;">
                                <div class="row no-margins text-left credit-tab">
                                    @if($logintype == 'merchants')
                                    <div class="pull-right text-center">
                                        <h2 class="no-margins textsize">
                                            {{ ($merchant->mer_vtoken) ? $merchant->mer_vtoken : '0.00' }}
                                        </h2>
                                    </div>
                                    <div class="pull-right text-center">
                                        <img src="{{ url('/assets/images/icon/icon_meicredit.png') }}" style="width:auto; height:25px;">
                                        <small class="credit-label">@lang('common.credit_name')</small>
                                    </div>
                                    @endif
                                </div>
                            </a>
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
                        @if($logintype == 'merchants')
                            <li class="dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#"><span class="dhidden"><i class="fa fa-user"></i> <span class="mhidden"> {{trans('localize.setting')}}</span></span></a>
                                <ul class="dropdown-menu dropdown-messages animated fadeInRight m-t-xs" style="width:auto;">
                                    <li><a href="/merchant/profile">{{trans('localize.profile')}}</a></li>
                                    <li><a href="/merchant/profile/edit">{{trans('localize.edit_profile')}}</a></li>
                                    <li><a href="/merchant/profile/password">{{trans('localize.change_password')}}</a></li>
                                    <li><a href="/merchant/credit/log">{{trans('localize.vcoinLog')}}</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="/merchant/logout">
                                    <i class="fa fa-sign-out"></i><span class="mhidden">  @lang('localize.logout')</span>
                                </a>
                            </li>
                            @endif
                            @if($logintype == 'storeusers')
                            <li class="dropdown">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#"><span class="dhidden"><i class="fa fa-user"></i> <span class="mhidden"> {{trans('localize.setting')}}</span></span></a>
                                <ul class="dropdown-menu dropdown-messages animated fadeInRight m-t-xs" style="width:auto;">
                                    <li><a href="/store/profile/edit">{{trans('localize.edit_profile')}}</a></li>
                                    <li><a href="/store/password/edit">{{trans('localize.change_password')}}</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="/store/logout">
                                    <i class="fa fa-sign-out"></i><span class="mhidden">  @lang('localize.logout')</span>
                                </a>
                            </li>
                            @endif
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

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content load_modal">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Modal -->
    <div class="modal fade" id="myModal-static" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content load_modal">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div id="spinner" class="loading" style="display:none;">
        <div class="sk-spinner sk-spinner-cube-grid">
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
            <div class="sk-cube"></div>
        </div>
    </div>

    <div id="blueimp-gallery" class="blueimp-gallery" style="display:none;">
        <div class="slides"></div>
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">x</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
    </div>

    <!-- Mainly scripts -->
    <script src="/backend/js/jquery-2.1.1.js"></script>
    <script src="/backend/js/bootstrap.min.js"></script>
    <script src="/backend/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/backend/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/web/lib/sweetalert/sweetalert.min.js"></script>
    <!-- Custom and plugin javascript -->
    <script src="/backend/js/inspinia.js"></script>
    <script src="/backend/js/plugins/pace/pace.min.js"></script>
    <script src="/backend/js/plugins/datapicker/bootstrap-datepicker.js"></script>
    <script src="/backend/js/plugins/daterangepicker/daterangepicker.js"></script>

    <script src="/backend/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <script src="{{ asset('backend/js/country_state_f.js') }}" type="text/javascript"></script>

    @include('layouts.web.translation_script')

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            // Check cookie if exist redirect
            var cookie =  {{!empty(\Cookie::get('update_username')) ? \Cookie::get('update_username') : 0}};
            var update_flag = {{!empty($merchant) ? $merchant->update_password : 0}};
            var route = '{{ url()->current() }}';
            if (cookie == 1 && route != '{{ url("merchant/profile/edit") }}' && update_flag == 0) {
                swal({
                    title: "",
                    text: "@lang('localize.updateusername')",
                    type: "error",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function() {
                    window.location.href = '{{ url("merchant/profile/edit") }}';
                });
            }

            if (update_flag == 1 && route != '{{ url("merchant/profile/password") }}') {
                swal({
                    title: "",
                    text: "@lang('localize.passwordUpdate')",
                    type: "error",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function() {
                    window.location.href = '{{ url("merchant/profile/password") }}';
                });
            }

            $( ".locale" ).each(function(index) {
                $(this).on("click", function(){
                    var lang = $(this).attr('data-lang');
                    $.ajax({
                        type: 'post',
                        data: { 'lang': lang },
                        url: '{{ url('/home/setlocale') }}',
                        success: function (responseText) {
                            if (responseText) {
                                if (responseText == "success") {
                                    window.location.reload(true);
                                }
                            }
                        }
                    });
                });
            });

            $( ".countryloc" ).each(function(index) {
                $(this).on("click", function(){
                    var id = $(this).attr('data-id');
                    $.ajax({
                        type: 'post',
                        data: { 'id': id },
                        url: '{{url('/home/setcountry')}}',
                        success: function (responseText) {
                            if (responseText) {
                                if (responseText == "success") {
                                    location.reload();
                                    //window.location.replace(window.location.href);
                                }
                            }
                        }
                    });
                });
            });
        });
    </script>

    @yield('script')
</body>
</html>
