@extends('admin.layouts.master')

@section('title', 'View E-Card')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>@lang('localize.e-card.name')</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.product')
            </li>
            <li>
                @lang('localize.e-card.serial_number')
            </li>
            <li class="active">
                <strong>{{$product['details']->pro_id}} - {{$product['details']->pro_title_en}}</strong>
            </li>
        </ol>
    </div>
    <div class="col-sm-8">
        <div class="title-action">
            <a href="{{ url('download/ecard') }}" target="_blank" class="btn btn-info"><i class="fa fa-cloud-download"></i> @lang('localize.e-card.download') </a>
        </div>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    @include('admin.common.notifications')

    <div class="ibox float-e-margins border-bottom">
        <a class="collapse-link nolinkcolor">
            <div class="ibox-title ibox-title-filter">
                <h5>@lang('localize.Search_Filter')</h5>
                <div class="ibox-tools">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </a>
        <div class="ibox-content ibox-content-filter" style="display:none;">
            <div class="row">
                <div class="col-lg-12">
                    <form class="form-horizontal" action="{{ url()->current() }}" method="GET">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">@lang('localize.e-card.serial_number')</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="serial_number">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">@lang('localize.sort')</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                    @foreach($filters->sort as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $input['sort']? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">@lang('localize.status')</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status">
                                    @foreach($filters->status as $key => $value)
                                    <option value="{{ $key }}" {{ ($key == $input['status'])? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">@lang('localize.show')</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="show">
                                    @foreach($filters->show as $key => $value)
                                    <option value="{{ $key }}" {{ ($key == $input['show'])? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-2">
                                <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">@lang('localize.search')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="tabs-container">

            @include('admin.product.nav-tabs', ['link' => 'ecard'])

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="panel-body">
                        <div class="row" style="margin-bottom:20px;">
                            <div class="form-upload" style="display:none;">
                                <form action="{{ url('admin/product/code/upload', [$product['details']->pro_mr_id, $product['details']->pro_id]) }}" method="POST" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                    <div class="col-lg-8">
                                        <span class='btn btn-default btn-block'><input class="compulsary files" type='file' name='file' id="file"></span>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="submit" class="btn btn-primary btn-block" id="submit">@lang('localize.upload')</button>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-default btn-block close-form">@lang('localize.cancel')</button>
                                    </div>
                                </form>
                            </div>
                            <div class="button-upload">
                                <div class="col-lg-2 col-lg-offset-10">
                                    <button type="button" class="btn btn-primary btn-block show-form">@lang('localize.e-card.upload')</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-stripped table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-nowrap" data-sort-ignore="true">#</th>
                                                <th class="text-left text-nowrap" width="70%;">@lang('localize.e-card.serial_number')</th>
                                                @if(!session('duplicates'))
                                                <th class="text-center text-nowrap" width="20%;">@lang('localize.e-card.validity')</th>
                                                <th class="text-center text-nowrap" width="20%;">@lang('localize.redemption_date')</th>
                                                @else
                                                <th class="text-center text-nowrap">@lang('localize.description')</th>
                                                @endif
                                                <th class="text-center text-nowrap">@lang('localize.status')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!session('duplicates'))
                                            @foreach($listings as $key => $code)
                                            <tr>
                                                <td class="text-center text-middle">{{ $key + $listings->firstItem() }}</td>
                                                <td class="text-left text-middle">{{ $code->serial_number }}</td>
                                                <td class="text-center text-nowrap text-middle">
                                                    @if($code->valid_from && $code->valid_to)
                                                    {{ \Helper::UTCtoTZ($code->valid_from) }}
                                                    <br>@lang('localize.to')
                                                    <br>{{ \Helper::UTCtoTZ($code->valid_to) }}
                                                    @endif
                                                </td>
                                                <td class="text-center text-nowrap text-middle">
                                                    @if($code->status == 2)
                                                    {{ \Helper::UTCtoTZ($code->redeemed_at) }}
                                                    @endif
                                                </td>
                                                <td class="text-center text-middle">
                                                    @if($code->status == 0)
                                                        {{--  @if(!empty($code->valid_to) && \Carbon\Carbon::now('UTC') >= $code->valid_to)
                                                        <span class="text-danger">@lang('localize.expired')</span>
                                                        @else
                                                        <span class="text-muted">@lang('localize.open')</span>
                                                        @endif  --}}
                                                        <span class="text-muted">@lang('localize.open')</span>
                                                        <br><br>
                                                        <button type="button" class="btn btn-danger btn-xs btn-block btn-delete" url="{{ url('admin/product/code/delete', [$code->id, $product['details']->pro_id, $product['details']->pro_mr_id]) }}">
                                                            <span><i class="fa fa-trash"></i> @lang('localize.e-card.delete.name')</span>
                                                        </button>
                                                    @elseif($code->status == 1)
                                                        <span class="text-navy">@lang('localize.purchased')</span>
                                                        <br><br>
                                                        <button type="button" class="btn btn-success btn-xs btn-block btn-redeem" url="{{ url('admin/product/code/redeem', [$code->id, $product['details']->pro_id, $product['details']->pro_mr_id]) }}">
                                                            <span><i class="fa fa-check"></i> @lang('localize.e-card.redeem.name')</span>
                                                        </button>
                                                    @elseif($code->status == 2)
                                                        <span class="text-warning">@lang('localize.redeemed')</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            @foreach(session('duplicates') as $key => $code)
                                            <tr>
                                                <td class="text-center">{{ $key+1 }}</td>
                                                <td class="text-left">{{ $code->serial_number }}</td>
                                                <td class="text-center">
                                                    <span class="text-{{ $code->status? 'navy' : 'danger' }}"> <i class="fa fa-{{ $code->status? 'check' : 'times' }}"></i> {{ $code->status? 'Pass' : 'Failed' }}</span>
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    @if($code->duplicate > 0)
                                                    <p>@lang('localize.e-card.duplicate', ['total' => $code->duplicate])</p>
                                                    @endif

                                                    @if($code->exist)
                                                    <p>@lang('localize.e-card.exist')</p>
                                                    @endif

                                                    @if(!$code->validate)
                                                    <p>@lang('localize.e-card.error.alphanumeric')</p>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                        @if(!session('duplicates'))
                                        <tfoot>
                                            <tr>
                                                <td colspan="100">
                                                    <div class="row">
                                                        <div class=" col-xs-3">
                                                            <span class="pagination">
                                                            @lang('localize.Showing') {{ $listings->firstItem() }} @lang('localize.to') {{ $listings->lastItem() }} @lang('localize.of') {{ $listings->Total() }} @lang('localize.Records')
                                                            </span>
                                                        </div>
                                                        <div class="col-xs-6 text-center">
                                                            {{$listings->appends(Request::except('page'))->links()}}
                                                        </div>
                                                        <div class="col-xs-3 text-right pagination">
                                                            @lang('localize.Go_To_Page')
                                                            <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$listings->lastPage()}}">
                                                            <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                                <i class="fa fa-share-square-o"></i> @lang('localize.Go')
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                        @endif
                                    </table>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/custom.js"></script>

<script>
$(document).ready(function() {
    {{--  $('.footable').footable();  --}}

    $('.show-form').click(function() {
        $('.button-upload').hide();
        $('.form-upload').show();
    });

    $('.close-form').click(function() {
        $('.button-upload').show();
        $('.form-upload').hide();
    });

    $('.files').change(function() {
        var fileExtension = ['xlsx'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            swal("{{trans('localize.error')}}", "{{ trans('localize.file.error.type', ['values' => 'xlsx']) }}", "error");
            $(this).css('border', '1px solid red').focus().val('');
        } else if (($(this)[0].files[0].size) > 1000000){
            swal("{{trans('localize.error')}}", "{{ trans('localize.localize.file.error.size') }}", "error");
            $(this).css('border', '1px solid red').focus().val('');
        }
    });

    $('#submit').click(function(event) {

        if(!$('#file').val()) {
            swal("{{trans('localize.error')}}", "{{ trans('localize.file.error.required') }}", "error");
            event.preventDefault();
            return false;
        }

        var fileExtension = ['xlsx'];
        if($('#file').val()) {
            if ($.inArray($('#file').val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                swal("{{trans('localize.error')}}", "{{ trans('localize.file.error.type', ['values' => 'xlsx']) }}", "error");
                event.preventDefault();
                return false;
            } else if (($('#file')[0].files[0].size) > 1000000) {
                swal("{{trans('localize.error')}}", "{{ trans('localize.localize.file.error.size') }}", "error");
                event.preventDefault();
                return false;
            }
        }

        $('#spinner').show();
    });

    $('.btn-redeem').click(function() {
        var url = $(this).attr('url');
        redeem_ecard(url);
    });

    $('.btn-delete').click(function() {
        var url = $(this).attr('url');
        delete_ecard(url);
    });
});

function gotopage($page) {
    $val =  $("#pageno").val();

    var href = window.location.href.substring(0, window.location.href.indexOf('?'));
    var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
    var newParam = $page + '=' + $val;

    if (qs.indexOf($page + '=') == -1) {
        if (qs == '') {
            qs = '?'
        }
        else {
            qs = qs + '&'
        }
        qs = newParam;

    }
    else {
        var start = qs.indexOf($page + "=");
        var end = qs.indexOf("&", start);
        if (end == -1) {
            end = qs.length;
        }
        var curParam = qs.substring(start, end);
        qs = qs.replace(curParam, newParam);
    }
    window.location.replace(href + '?' + qs);
}
</script>
@endsection