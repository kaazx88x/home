@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('content')
@include('admin.common.error')
@include('admin.common.errors')
@include('admin.common.success')
{{trans('localize.Welcome')}}!
@stop
@section('style')
<link href="/backend/css/plugins/toastr/toastr.min.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/toastr/toastr.min.js"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": false,
        "preventDuplicates": true,
        "positionClass": "toast-top-right",
        "showDuration": "100",
        "hideDuration": "1000",
        "timeOut": "7000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    @if(Session::has('denied'))
        toastr.warning("{{ Session::get('denied') }}","Access Denied!")
    @endif
</script>
@endsection