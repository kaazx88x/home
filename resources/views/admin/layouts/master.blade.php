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
        <link href="/web/lib/sweetalert/sweetalert.css" rel="stylesheet">
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

        @include('admin.partial.nav')
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
                $('[data-toggle="tooltip"]').tooltip();

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
