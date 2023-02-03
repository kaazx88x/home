<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="kM02mnFDSZS1w3uqZ15PoRFkIhvb8uH6khL7HFrY">

		<title>{{ config('app.name') }}</title>

		<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.css') }}" media="all">
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/font-awesome.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/front.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/loading.css') }}" media="all">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/sweetalert.css') }}" media="all">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/sweetalert.css') }}" media="all">
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/css/plugins/datapicker/datepicker3.css') }}" media="all">
        <link rel="stylesheet" type="text/css" href="{{ asset('backend/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" media="all">

        @yield('styles')

	</head>

	<body>

        <div class="PageContainer">

            @yield('header')

            <div class="MainContent">

                @yield('content')

            </div>
        </div>

        @include('layouts.web.footer')

        <!-- Category Listing -->
        <div class="CategoryPanel">
            <ul>
                <li><a href="javascript:void(0)" class="CloseToggle">@lang('localize.close_')</a></li>
                @foreach ($nav_parent_all as $cat)
                    @if (count($cat['layer_one']))
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown"><span id="cat_link" data-href="/products/category/{{ ($cat['parent']->url_slug)?$cat['parent']->url_slug:base64_encode($cat['parent']->id) }}">{{$cat['parent']->name}}</span></a>
                        <ul class="dropdown-menu">
                            @foreach ($cat['layer_one'] as $key => $layer_one)
                                <li><a href="/products/category/{{ ($cat['parent']->url_slug)?$cat['parent']->url_slug:base64_encode($cat['parent']->id) }}/{{ ($layer_one->url_slug)?$layer_one->url_slug:base64_encode($layer_one->id) }}">{{$layer_one->name}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li>
                        <a href="/products/category/{{ ($cat['parent']->url_slug)?$cat['parent']->url_slug:base64_encode($cat['parent']->id)}}" >{{ ucwords(strtolower($cat['parent']->name)) }}</a>
                    </li>
                @endif
                @endforeach
            </ul>
        </div>

        <div id="language" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="modal-title">@lang('localize.lang')</div>
                        <div class="user-selection">
                            <div class="list">
                                <a href="javascript:void(0)" class="locale" data-lang="en">English</a>
                            </div>
                            <div class="list">
                                <a href="javascript:void(0)" class="locale" data-lang="cn">简体中文</a>
                            </div>
                            <div class="list">
                                <a href="javascript:void(0)" class="locale" data-lang="cnt">繁体中文</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="country" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="modal-title">{{ $country_name }}</div>
                        <div class="user-selection">
                            @foreach($locale_countries as $key => $co)
                            <div class="list">
                                <a href="javascript:void(0)" class="location" data-id="{{$co->co_id}}"><span class="flag-icon flag-icon-{{strtolower($co->co_code)}}"></span> {{ $co->co_name }}</a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div class="loading" style="display:none;">&nbsp;</div>

        <script type="text/javascript" src="{{ asset('assets/js/jquery-2.2.4.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/jquery.lazy-master/jquery.lazy.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/general.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('backend/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
        <script type="text/javascript" src="{{ asset('backend/js/plugins/daterangepicker/daterangepicker.js') }}"></script>
        
        <script src="{{ asset('backend/js/country_state_f.js') }}" type="text/javascript"></script>

        @include('layouts.web.translation_script')

        @yield('scripts')

        <script>
            $(document).ready(function() {
                $(".locale").each(function (index) {
                    $(this).on("click", function () {
                        var lang = $(this).attr('data-lang');
                        $('#language').modal('toggle');
                        $('.loading').show();
                        $.ajax({
                            type: 'post',
                            data: { 'lang': lang },
                            url: '{{ url('home/setlocale') }}',
                            success: function (responseText) {
                                if (responseText) {
                                    if (responseText == "success") {
                                        location.reload();
                                    }
                                }
                            }
                        });
                    });
                });

                $( ".location" ).each(function(index) {
                    $(this).on("click", function(){
                        var id = $(this).attr('data-id');
                        $('#country').modal('toggle');
                        $('.loading').show();
                        $.ajax({
                            type: 'post',
                            data: { 'id': id },
                            url: '{{url('/home/setcountry')}}',
                            success: function (responseText) {
                                if (responseText) {
                                    if (responseText == "success") {
                                        location.reload();
                                    }
                                }
                            }
                        });
                    });
                });

                $('span#cat_link').on('click', function() {
                    var goto = $(this).attr('data-href');
                    document.location.href = goto;
                });

                $('a.goBack').on('click', function() {
                    if(document.referrer) {
                        window.history.go(-1);
                    } else {
                        document.location.href = '/';
                    }
                });

                $('.cart_remove').on('click', function(index) {
                    var id = $(this).attr('data-id');
                    swal({
                        title: "@lang('localize.remove_item_from_cart')",
                        text: "@lang('localize.sure')",
                        type: "warning",
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "@lang('localize.remove')",
                        cancelButtonText: "@lang('localize.cancel')",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                    }, function(){
                        $.ajax({
                            type: 'post',
                            data: { 'id': id },
                            url: '{{url('/carts/delete')}}',
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
            });
        </script>
	</body>
</html>