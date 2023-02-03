@extends('merchant.layouts.master')

@section('title', 'Manage Stores')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{trans('localize.manage_store')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.store')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.manage_store')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <form id="filter" action="{{ url('merchant/store/manage') }}" method="GET">
                <div class="col-sm-1">
                    <div class="form-group">
                        <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.#id')}}" class="form-control" id="id" name="id">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <input type="text" value="{{$input['name']}}" placeholder="{{trans('localize.mer_store_name')}}" class="form-control" id="name" name="name">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="type" name="type">
                            <option value="" {{ ($input['type'] == "") ? 'selected' : '' }}>{{trans('localize.type')}}</option>
                            <option value="1" {{ ($input['type'] == "1") ? 'selected' : '' }}>{{trans('localize.offline_store')}}</option>
                            <option value="0" {{ ($input['type'] == "0") ? 'selected' : '' }}>{{trans('localize.online_store')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="status" name="status">
                            <option value="" {{ ($input['status'] == "") ? 'selected' : '' }}>{{trans('localize.status')}}</option>
                            <option value="1" {{ ($input['status'] == "1") ? 'selected' : '' }}>{{trans('localize.active')}}</option>
                            <option value="0" {{ ($input['status'] == "0") ? 'selected' : '' }}>{{trans('localize.inactive')}}</option>
                            <option value="2" {{ ($input['status'] == "2") ? 'selected' : '' }}>{{trans('localize.pending')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                            <option value="name_asc" {{ ($input['sort'] == "name_asc") ? 'selected' : '' }}>{{trans('localize.productName')}} : &#xf15d;</option>
                            <option value="name_desc" {{ ($input['sort'] == "name_desc") ? 'selected' : '' }}>{{trans('localize.productName')}} : &#xf15e;</option>
                            <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.newest')}}</option>
                            <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.oldest')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('merchant.common.success')
            @include('merchant.common.error')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="ibox-tools">
                        {{--  <a href="/merchant/store/add" class="btn btn-primary btn-md">@lang('localize.add_store')</a>  --}}
                        <a href="/merchant/store/user/manage" class="btn btn-primary btn-md">@lang('localize.manage_store_user')</a>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">{{trans('localize.#id')}}</th>
                                    <th>{{trans('localize.mer_store_name')}}</th>
                                    <th class="text-center">{{trans('localize.store_user')}}</th>
                                    <th class="text-center">{{trans('localize.phone')}}</th>
                                    <th class="text-center">{{trans('localize.country')}}</th>
                                    <th class="text-center">{{trans('localize.state')}}</th>
                                    <th class="text-center">{{trans('localize.city')}}</th>
                                    <th class="text-center">{{trans('localize.type')}}</th>
                                    <th class="text-center">{{trans('localize.rating')}}</th>
                                    <th class="text-center">{{trans('localize.review')}}</th>
                                    <th class="text-center">{{trans('localize.action')}}</th>
                                </tr>
                            </thead>
                            @if ($stores->total())
                                <tbody>
                                    @foreach ($stores as $key => $stor)
                                    <tr class="text-center">
                                        <td>{{$stor->stor_id}}</td>
                                        <td class="text-left">{{$stor->stor_name}}</td>
                                        <td nowrap>
                                                @foreach($stor->store_user as $user)
                                                    <li><a class="nolinkcolor" href="/merchant/store/user/edit/{{ $user->id}}" data-toggle="tooltip" title="">{{ $user->name }}</a></li>
                                                @endforeach
                                            </td>
                                        <td>{{$stor->stor_phone}}</td>
                                        <td>{{$stor->co_name}}</td>
                                        <td>{{($stor->name)? $stor->name : $stor->ci_name}}</td>
                                        <td>{{$stor->stor_city_name}}</td>
                                        <td class="{{($stor->stor_type == 0) ? 'text-navy' : 'text-warning'}}">{{($stor->stor_type == 0) ? trans('localize.online_store') : trans('localize.offline_store')}}</td>
                                        <td>{{$stor->total_rating}}</td>
                                        <td>{{$stor->total_review}}</td>
                                        <td>
                                            <p>
                                                @if($stor->stor_status == 1)
                                                    <span class="text-navy"><i class="fa fa-check"></i> {{trans('localize.active')}}</span>
                                                @elseif($stor->stor_status == 0)
                                                    <span class="text-danger"><i class="fa fa-ban"></i> {{trans('localize.inactive')}}</span>
                                                @else
                                                    <span class="text-warning"><i class="fa fa-exclamation"></i> {{trans('localize.pending')}}</span>
                                                @endif
                                            <p>
                                                <a href="{{ url('merchant/store/edit', [$stor->stor_id]) }}" class="btn btn-white btn-block btn-sm"><i class="fa fa-pencil"></i> {{trans('localize.edit')}}</a>
                                            </p>

                                            @if($stor->stor_type > 0 && ($stor->accept_payment == 1 || $stor->accept_paymentgateway == 1))
                                            <p>
                                                <a class="btn btn-white btn-block btn-sm text-primary" href="{{ url('merchant/store/limit', [$stor->stor_id]) }}"><span><i class="fa fa-bar-chart-o"></i> @lang('localize.limit')</span></a>
                                            </p>
                                            @endif

                                            <p>
                                                {{-- <button class="btn btn-white btn-block btn-sm text-primary" data-toggle="modal" data-href="http://www.meihome.asia/api/v1/member/check/store?store_id={{ $stor->stor_id }}" data-storename="{{ $stor->stor_name }}" data-action="show-qrcode"> <i class="fa fa-qrcode"></i> @lang('localize.qr_code')</button> --}}
                                                <button class="btn btn-white btn-block btn-sm text-primary" data-toggle="modal" data-href="{{ url('/api/v1/member/check/store', [$stor->stor_id]) }}" data-storename="{{ $stor->stor_name }}" data-action="show-qrcode"> <i class="fa fa-qrcode"></i> @lang('localize.qr_code')</button>
                                            </p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="12">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    Showing {{$stores->firstItem()}} to {{$stores->lastItem()}} of {{$stores->Total()}} Records
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$stores->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    Go To Page
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$stores->lastPage()}}">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                        <i class="fa fa-share-square-o"></i> Go
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="12" class="text-center">@lang('localize.nodata')</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="qrcode">
    <div class="modal-dialog">
        <div class="modal-body">
            <div class="row" style="background-color: #fff;" id="qr_code_content">
                <div class="col-sm-12 text-center" style="padding:15px;"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class='fa fa-close'></i></button>
                </div>

                <div class="col-sm-12 text-center" style="background-color: #fff; padding-top: 1px;">
                    <div class="col-sm-6 text-left" style="padding-left: 13%;">
                        <span style="color: #337ab7; font-size: 20px;"><b>Accept Mei Point</b></span>
                    </div>
                    <div class="col-sm-6 text-right" style="padding-right: 13%;">
                        <span style="color: #fa5555; font-size: 20px;"><b>美点支付</b></span>
                    </div>
                    <div class="col-sm-12 text-center" style="background-color: #FFF; margin-top:30px;">
                        <div class="col sm-12" id="qr_output" style="padding-left:10px;"></div>
                        <div class="col sm-12" style="margin-top: 5%;">
                            <span id="storename" style="color: #000; font-size: 18px;"></span>
                        </div>
                    </div>

                    <div class="col-sm-12 text-center" style="padding:15px;">
                        {{-- <span style="color: #fff; font-size: 23px; border-bottom: 4px dotted #fff;"><b>@lang('localize.join_meihome_today')</b></span> --}}
                    </div>
                </div>
            </div>

            <div class="row" style="display:none;">
                <div id="previewImage"></div>
            </div>

        </div>

        <div class="modal-footer">
            <div class="row">
                <div class="col-sm-12 text-center">
                    {{-- <button class="btn btn-white btn-md text-primary" data-action="print-qrcode"><i class="fa fa-print"></i>Print</button> --}}
                    <button class="btn btn-white btn-md text-primary" data-action="download-qrcode" href="#"><i class="fa fa-download"></i> @lang('localize.download_as_image')</button>
                </div>
            </div>
        </div>
    </div>
</div>
<img id='my-image' src="{{ url('/assets/images/meihome_logo.png') }}" style="display:none;"/>
@endsection

@section('script')
<script src="/backend/js/plugins/JQuery-qrcode/qrcode.js"></script>
<script src="/backend/js/html2canvas.js"></script>
<script>
    $(document).ready(function() {

        var element = $("#qr_code_content");
        $('button').on('click', function() {
            var this_action = $(this).attr('data-action');
            if(this_action == 'show-qrcode') {
                var qrcodeurl = $(this).attr('data-href');
                var storename = $(this).attr('data-storename');
                var options = {
                    // render method: 'canvas', 'image' or 'div'
                    render: 'image',

                    // version range somewhere in 1 .. 40
                    minVersion: 6,
                    maxVersion: 15,

                    // error correction level: 'L', 'M', 'Q' or 'H'
                    ecLevel: 'L',

                    // offset in pixel if drawn onto existing canvas
                    left: 0,
                    top: 0,

                    // size in pixel
                    size: 400,

                    // code color or image element
                    fill: '#000',

                    // background color or image element, null for transparent background
                    background: '#FFF',

                    // content
                    text: qrcodeurl,

                    // corner radius relative to module width: 0.0 .. 0.5
                    radius: 0.4,

                    // quiet zone in modules
                    quiet: 0,

                    // modes
                    // 0: normal
                    // 1: label strip
                    // 2: label box
                    // 3: image strip
                    // 4: image box
                    mode: 4,

                    mSize: 0.10,
                    mPosX: 0.5,
                    mPosY: 0.5,

                    label: 'no label',
                    fontname: 'sans',
                    fontcolor: '#000',

                    image: $('#my-image')[0]
                };

                $('#qr_output').empty().qrcode(options);
                $('#storename').text(storename);
                $("#qrcode").modal('show');
            } else if (this_action == 'download-qrcode') {
                html2canvas(element, {
                    onrendered: function (canvas) {
                        $("#previewImage").append(canvas);

                        var a = $("<a>").attr("href", canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream"))
                        .attr("download", "qr_code.png")
                        .appendTo("body");
                        a[0].click();
                        a.remove();
                    }
                });

            }
        });

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$stores->lastPage()}};
            if(input > max){
                $(this).val(max);
            }
        });

        // $('.footable').footable();

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