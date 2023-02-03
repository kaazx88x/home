<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <link rel="shortcut icon" href="{{{ asset('assets/images/favicon.ico') }}}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('assets/css/jquery.fancybox.css') }}" rel="stylesheet" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.css') }}">
    <link href="{{ asset('assets/css/animate.min.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('assets/css/swatch.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('assets/css/owl.carousel.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('assets/css/flexslider.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('assets/css/timber.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('assets/css/home_market.global.scss.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('assets/css/tada.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('web/lib/sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css" media="all">

    <link href="{{ asset('assets/css/spr.css') }}" rel="stylesheet" type="text/css" media="all">
    <link href="{{ asset('assets/css/loading.css') }}" rel="stylesheet" type="text/css" media="all">

    <link rel="stylesheet" type="text/css" href="{{ asset('/web/lib/custombox/css/custombox.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('/web/lib/flag-icon/css/flag-icon.css') }}" />

    <style>
        /* Flag Icon */
        .flag-wrapper {
        width: 100%;
        display: inline-block;
        position: relative;
        box-shadow: 0 0 2px black;
        overflow: hidden;
        margin-bottom: 20px;
        }
        .flag-wrapper:after {
        padding-top: 75%;
        /* ratio */
        display: block;
        content: '';
        }
        .flag-wrapper .flag {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        }
    </style>
    @yield('styles')

    <script src="{{ asset('assets/js/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/jquery.fancybox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/jquery.tweet.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/jquery.optionSelect.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/jquery.flexslider-min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/jquery.easytabs.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/main.js') }}" type="text/javascript"></script>
    <script src="{{ asset('web/lib/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};

    </script>
</head>

<body>
    @include('layouts.drawer')
    <div id="PageContainer" class="is-moved-by-drawer">
        @include('layouts.nav')

        <main class="main-content ">
            <div class="wrapper">
                @if (Request::is('/') || Request::is('products*'))
                    @include('layouts.side_nav')
                @endif
                @yield('content')
            </div>
        </main>
    @yield('featured')
    @include('layouts.footer')
    </div>
    <!-- Modal -->
    <div id="mymodal" class="modal-dialog modal-lg" style="display:none;">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h3 class="modal-title" id="myLargeModalLabel"><i class="fa fa-flag" aria-hidden="true"></i> Please Choose Your Country</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($locale_countries as $key => $country)
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
                            <div class="flag-wrapper">
                                <a class="countryloc" data-id="{{$country->co_id}}" style="cursor:pointer;">
                                    <div class="img-thumbnail flag flag-icon-background flag-icon-{{strtolower($country->co_code)}}"></div>
                                </a>
                            </div>
                            <h3><span class="label label-info-custom">{{$country->co_name}}</span></h3>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div id="scroll-to-top" title="Scroll to Top" class="off">
        <i class="fa fa-angle-up"></i>
    </div>

    <!-- Loading Spinner -->
    <div class="loading" style="display:none;">&nbsp;</div>

    <script>
        var tada_index, tada_autosearchcomplete, tada_swiftype, tada_ads, tada_adsspeed, tada_slideshowtime, tada_block1gallery = false, tada_block1product = false, tada_newsletter = false;
        tada_index = 1;
        tada_ads = 1;
        tada_adsspeed = 5000;
        tada_slideshowtime = 30000;
        tada_block1gallery = true;
        tada_block1product = true;
        tada_newsletter = true;

    </script>

    <script src="{{ asset('assets/js/modernizr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/timber.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('/web/lib/custombox/js/custombox.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/web/lib/custombox/js/legacy.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/web/lib/jquery.unveil.js') }}"></script>
    @yield('scripts')
    <script type="text/javascript">
        $(window).load(function() {
            $('img').unveil(300, function() {
                $(this).load(function() {
                    this.style.opacity = 1;
                });
            });
        });

        jQuery(document).ready(function ($) {
            $(".locale").each(function (index) {
                $(this).on("click", function () {
                    var lang = $(this).attr('data-lang');
                    $.ajax({
                        type: 'post',
                        data: { 'lang': lang },
                        url: '{{ url('home/setlocale') }}',
                        success: function (responseText) {
                            if (responseText) {
                                if (responseText == "success") {
                                    window.location.replace(window.location.href);
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

            if (navigator.cookieEnabled){;
                @if(empty(Session::get('countryid')))
                    Custombox.open({
                        target: '#mymodal',
                        effect: 'door',
                        escKey: false,
                        overlayEffect: 'blur',
                        overlayClose: false,
                        position: ['center', 'center'],

                    });
                @endif
            } else {
                $('#cookie_needed').show();
            }

            if ($('.quantity-wrapper').length) {
                $('.quantity-wrapper').on('click', '.qty-up', function () {
                    var $this = $(this);
                    var qty = $this.data('src');
                    $(qty).val(parseInt($(qty).val()) + 1);
                });
                $('.quantity-wrapper').on('click', '.qty-down', function () {
                    var $this = $(this);
                    var qty = $this.data('src');
                    if (parseInt($(qty).val()) > 1)
                        $(qty).val(parseInt($(qty).val()) - 1);
                });
            }

            function paramSearch($search) {
                var val = document.getElementById('search').value;
                var cat = document.getElementById('category').value;
                var href = window.location.href = '/search';
                var newParam = $search + '=' + val;
                window.location.replace(href +'?category='+ cat +'&' + newParam);
            }

            $('.cart__remove').on('click', function(index) {
                var id = $(this).attr('data-id');
                swal({
                    title: "Remove item from cart",
                    text: "Are you sure?",
                    type: "warning",
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Remove Item",
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
<!--    @if(env('APP_ENV')=='production')
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-93692610-1', 'auto');
        ga('send', 'pageview');

    </script>
    @endif-->
</body>
</html>