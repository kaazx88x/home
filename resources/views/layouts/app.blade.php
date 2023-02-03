<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    {{-- <link rel="stylesheet" type="text/css" media="all" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="css/font-awesome.css">
	<link rel="stylesheet" type="text/css" media="all" href="css/style.css"> --}}
    <link rel="shortcut icon" href="{{{ asset('assets/images/favicon.ico') }}}">
    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('assets/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('assets/css/font-awesome.min.css') }}">
	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('assets/css/front.css') }}">
	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('assets/css/custom.css') }}">
    @yield('styles')

    @yield('head')
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div class="sticky-wrapper">
        @yield('top-header')
        <header class="main-header">
            <div class="container">
                {{--  <a href="/"><img src="{{ asset('assets/images/logo.png') }}" class="logo"></a>
                @if (!str_contains(url()->current(), '/admin'))
                <ul class="main-nav">
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="expand-toggle" data-toggle="dropdown">@lang('front.language') <i class="fa fa-angle-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0)" class="locale" data-lang="en">English</a></li>
                            <li><a href="javascript:void(0)" class="locale" data-lang="cn">中文</a></li>
                        </ul>
                    </li>
                </ul>
                @endif  --}}
            </div>
        </header>
        @yield('content')
    </div>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-copyright">
                <p>
                    &copy; {{ date("Y") }} @lang('common.mall_name')
                    {{-- @if (!str_contains(url()->current(), '/admin'))
                        @if (str_contains(url()->current(), '/merchant') || str_contains(url()->current(), '/store'))
                            &nbsp;|&nbsp; {{ trans('common.mall_name') }} {{ trans('localize.merchant_app') }} :
                            <a href="#" data-toggle="modal" data-target="#android_merchant"><img src="{{ asset('assets/images/google-play-badge.png') }}" height="30px"></a>
                            &nbsp; &nbsp;
                            <a href="#" data-toggle="modal" data-target="#ios_merchant"><img src="{{ asset('assets/images/app-store-badge.png') }}" height="30px"></a>
                        @else
                            &nbsp;|&nbsp; {{ trans('common.mall_name') }} {{ trans('localize.member_app') }} :
                            <a href="#" data-toggle="modal" data-target="#android_member"><img src="{{ asset('assets/images/google-play-badge.png') }}" height="30px"></a>
                            &nbsp; &nbsp;
                            <a href="#" data-toggle="modal" data-target="#ios_member"><img src="{{ asset('assets/images/app-store-badge.png') }}" height="30px"></a>
                        @endif
                    @endif --}}
                </p>
            </div>
        </div>
    </footer>

    <!-- Modal Android Member-->
    <div class="modal fade" id="android_member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body" style="text-align:center;">
                    <img src="{{ asset('assets/images/qr/android_member.jpg') }}" height="450px"/>
                    <a class="btn btn-xs" href="{{env('ANDROID_MEMBER')}}">
                        <i class="fa fa-android fa-2x" aria-hidden="true"></i>
                        <br/>CLICK TO DOWNLOAD ANDROID VERSION
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Android Merchant-->
    <div class="modal fade" id="android_merchant" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body" style="text-align:center;">
                    <img src="{{ asset('assets/images/qr/android_merchant.jpg') }}" height="450px"/>
                    <a class="btn btn-xs" href="{{env('android_merchant')}}">
                        <i class="fa fa-android fa-2x" aria-hidden="true"></i>
                        <br/>CLICK TO DOWNLOAD ANDROID VERSION
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal IOS Member-->
    <div class="modal fade" id="ios_member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body" style="text-align:center;">
                    <img src="{{ asset('assets/images/qr/ios_member.jpg') }}" height="450px"/>
                    <a class="btn btn-xs" href="{{env('IOS_MEMBER')}}">
                        <i class="fa fa-apple fa-2x" aria-hidden="true"></i>
                        <br/>CLICK TO DOWNLOAD IOS VERSION
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal IOS Merchant-->
    <div class="modal fade" id="ios_merchant" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body" style="text-align:center;">
                    <img src="{{ asset('assets/images/qr/ios_merchant.jpg') }}" height="450px"/>
                    <a class="btn btn-xs" href="{{env('IOS_MERCHANT')}}">
                        <i class="fa fa-apple fa-2x" aria-hidden="true"></i>
                        <br/>CLICK TO DOWNLOAD IOS VERSION
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScripts -->
    <script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script>
    $(function() {
        $( ".locale" ).each(function(index) {
            $(this).on("click", function(){
                var lang = $(this).attr('data-lang');
                $.ajax({
                    type: 'post',
                    data: { 'lang': lang },
                    url: '{{ url('home/setlocale') }}',
                    success: function(responseText) {
                        if (responseText) {
                            if (responseText == "success") {
                                window.location.replace(window.location.href);
                            }
                        }
                    }
                });
            });
        });
    });
    </script>
    @yield('scripts')
<!--    @if(env('APP_ENV')=='production')
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-93692610-1', 'auto');
        ga('send', 'pageview');

    </script>-->
    @endif
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
