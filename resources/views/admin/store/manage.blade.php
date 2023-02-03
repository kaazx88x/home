@extends('admin.layouts.master')

@section('title', 'Manage Stores')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">

    @if(!$pending)
    <div class="col-sm-4">
        <h2>{{trans('localize.manage_stores')}}</h2>
        <ol class="breadcrumb">
            @if($mer_id)
            <li>
                @if ($type == 0)
                    <a href="/admin/merchant/manage/online">{{trans('localize.Merchants')}} {{trans('localize.Online')}} </a>
                @elseif ($type == 1)
                    <a href="/admin/merchant/manage/offline">{{trans('localize.Merchants')}} {{trans('localize.Offline')}}</a>
                @endif
            </li>
            <li>
               <a href="/admin/merchant/view/{{$mer_id}}">{{trans('localize.view')}} {{trans('localize.Merchants')}} </a>
            </li>
            <li class="active">
                <strong>{{trans('localize.manage')}}</strong>
            </li>
            @else
            <li>
                @if ($type == 0)
                    {{trans('localize.Online')}}
                @elseif ($type == 1)
                    {{trans('localize.Offline')}}
                @endif
            </li>
            <li>
               {{trans('localize.Stores')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.manage')}}</strong>
            </li>
            @endif
        </ol>
    </div>
    @else
    <div class="col-sm-4">
        <h2>{{trans('localize.Store_Pending_Review')}}</h2>
    </div>
    @endif
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">

            @if(!$mer_id)
            <div class="ibox float-e-margins border-bottom">
                <a class="collapse-link nolinkcolor">
                <div class="ibox-title ibox-title-filter">
                    <h5>{{trans('localize.Search_Filter')}}</h5>
                    <div class="ibox-tools">
                        <i class="fa fa-chevron-down"></i>
                    </div>
                </div>
                </a>
                <div class="ibox-content ibox-content-filter" style="display:none;">
                    <div class="row">
                        <form class="form-horizontal" id="filter" action= "{{ url('admin/store/manage', [$merid_type]) }}" method="GET">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">ID</label>
                                <div class="col-sm-5">
                                    <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.Search_By_Store_ID')}}" class="form-control" name="id">
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" value="{{$input['mid']}}" placeholder="{{trans('localize.Search_By_Merchant_ID')}}" class="form-control" name="mid">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{{trans('localize.Name')}}</label>
                                <div class="col-sm-5">
                                    <input type="text" value="{{$input['name']}}" placeholder="{{trans('localize.Search_By_Merchant_ID')}}" class="form-control" name="name">
                                </div>

                                <div class="col-sm-5">
                                    <input type="text" value="{{$input['mname']}}" placeholder="{{trans('localize.Search_by_Merchant_Name')}}" class="form-control" name="mname">
                                </div>
                            </div>
                            @if(!$pending)
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{{trans('localize.Status')}}</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="status">
                                        <option value="" {{ ($input['status'] == "") ? 'selected' : '' }}>{{trans('localize.status')}}</option>
                                        <option value="1" {{ ($input['status'] == "1") ? 'selected' : '' }}>{{trans('localize.active')}}</option>
                                        <option value="0" {{ ($input['status'] == "0") ? 'selected' : '' }}>{{trans('localize.inactive')}}</option>
                                        <option value="2" {{ ($input['status'] == "2") ? 'selected' : '' }}>{{trans('localize.pending')}}</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{{trans('localize.sort')}}</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                        <option value="name_asc" {{ ($input['sort'] == "name_asc") ? 'selected' : '' }}>{{trans('localize.mer_store_name')}} : &#xf15d;</option>
                                        <option value="name_desc" {{ ($input['sort'] == "name_desc") ? 'selected' : '' }}>{{trans('localize.mer_store_name')}} : &#xf15e;</option>
                                        <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.newest')}}</option>
                                        <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.oldest')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-1">
                                    <button type="submit" class="btn btn-block btn-outline btn-primary" data-action="filter">{{trans('localize.search')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @include('admin.common.notifications')

            <div class="ibox">

                @if($mer_id)
                <div class="ibox-title">
                    <h5>{{trans('localize.manage_store')}}</h5>
                    <div class="ibox-tools">
                        <a href="/admin/store/add/{{$mer_id}}" class="btn btn-primary btn-xs">{{trans('localize.create_new_store')}}</a>
                    </div>
                </div>
                @endif

                <div class="ibox-content">

                    @if($type == 2)
                    <div class="row">
                        @if($batch_store_active_permission)
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-block btn-md" id="batch_update">{{trans('localize.Batch_Store_Active')}}</button><br>
                        </div>
                        @endif
                    </div>
                    <form id="batch_update_form" action="{{ url('admin/store/batch_status_update', ['pending_review']) }}" method="POST">
                    {{ csrf_field() }}
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>

                                    @if($type == 2)
                                    <th class="text-center text-nowrap">
                                        <div class="i-checks">
                                            <label>
                                                <input type="checkbox" id="check_all">
                                            </label>
                                        </div>
                                    </th>
                                    @endif

                                    <th class="text-center">#ID</th>
                                    <th class="text-center">{{trans('localize.store')}}</th>
                                    @if(!$mer_id)
                                    <th class="text-center">{{trans('localize.Merchants')}}</th>
                                    @endif

                                    <th class="text-center">{{trans('localize.Phone')}}</th>
                                    <th class="text-center">{{trans('localize.city')}}</th>
                                    <th class="text-center">{{trans('localize.country')}}</th>
                                    <th class="text-center">{{trans('localize.type')}}</th>
                                    <th class="text-center">{{trans('localize.Listed')}}</th>
                                    <th class="text-center">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            @if ($stores->total())
                                <tbody>
                                    @foreach ($stores as $key => $stor)
                                    <tr class="text-center">

                                        @if($type == 2)
                                        <td colspan="">
                                            <div class="i-checks">
                                                <label>
                                                    <input type="checkbox" class="input_checkbox" name="store_id[]" value="{{ $stor->stor_id }}">
                                                </label>
                                            </div>
                                        </td>
                                        @endif

                                        <td>{{$stor->stor_id}}</td>
                                        <td>{{$stor->stor_id.' - '.$stor->stor_name}}</td>

                                        @if(!$mer_id)
                                            <td class="text-center">
                                                @if($stor->mer_id)
                                                    <a class="nolinkcolor" href="/admin/merchant/view/{{ $stor->mer_id }}" data-toggle="tooltip" title="View this merchant">
                                                        {{$stor->mer_id}} - {{$stor->mer_fname.' '.$stor->mer_lname}}
                                                    </a>
                                                @else
                                                    <span class="text-danger">{{trans('localize.Merchant_not_found')}}</span>
                                                @endif
                                            </td>
                                        @endif

                                        <td>{{$stor->stor_phone}}</td>
                                        <td>{{$stor->stor_city_name}}</td>
                                        <td>{{$stor->co_name}}</td>
                                        <td class="{{($stor->stor_type == 0) ? 'text-navy' : 'text-warning'}}">{{($stor->stor_type == 0) ? trans('localize.Online')  : trans('localize.Offline')}}</td>
                                        <td>
                                            @if($stor->stor_type == 1)
                                            <span class="{{($stor->listed == 1) ? 'text-navy' : 'text-warning'}}">{{($stor->listed == 1) ? trans('localize.yes') : trans('localize.no')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <p>
                                                <span class="{{($stor->stor_status == 1) ? 'text-navy' : 'text-danger'}}">
                                                    @if($stor->stor_status == 1)
                                                        <i class="fa fa-check"></i> {{trans('localize.active')}}
                                                    @elseif($stor->stor_status == 0)
                                                        <i class="fa fa-ban"></i> {{trans('localize.inactive')}}
                                                    @else
                                                        <i class="fa fa-pause"></i> {{trans('localize.pending')}}
                                                    @endif
                                                </span>
                                            </p>
                                            <p>
                                                <a href="/admin/store/edit/{{$stor->stor_merchant_id}}/{{$stor->stor_id}}" class="btn btn-white btn-block btn-sm"><i class="fa fa-pencil"></i> {{trans('localize.Edit')}}</a>
                                            </p>
                                            @if($block_store_permission)
                                            <p>
                                                @if($stor->stor_status == 1)
                                                    <a class="btn btn-white btn-block btn-sm text-warning" href="/update_store_status/{{$stor->stor_id}}/0"><span><i class="fa fa-ban"></i> {{trans('localize.Block')}} {{trans('localize.store')}} </span></a>
                                                @else
                                                    <a class="btn btn-white btn-block btn-sm text-navy {{ ($stor->mer_staus != 1)? 'block-merchant' : '' }}" href="{{ url('update_store_status', [$stor->stor_id, 1]) }}"><span><i class="fa fa-check"></i> {{trans('localize.Active')}} {{trans('localize.store')}}</span></a>
                                                @endif
                                            </p>
                                            @endif

                                                @if($stor->stor_type == 1)
                                                    @if($toggle_listed_permission)
                                                    <p>
                                                        <a class="btn btn-white btn-block btn-sm text-primary" href="/admin/store/update_store_listed_status/{{$stor->stor_id}}"><span><i class="fa fa-refresh"></i> {{trans('localize.Toggle_Listed')}}</span></a>
                                                    </p>
                                                    @endif

                                                    @if($stor->accept_payment == 1)
                                                    <p>
													@if($limit)
                                                        <a class="btn btn-white btn-block btn-sm text-primary" href="{{ url('admin/store/limit', [$stor->stor_merchant_id,$stor->stor_id]) }}"><span><i class="fa fa-bar-chart-o"></i> {{trans('localize.limit')}}</span></a>
                                                    @endif
													</p>
                                                    @endif
                                                @endif
                                            <p>
                                                {{-- <button class="btn btn-white btn-block btn-sm text-primary" data-toggle="modal" data-href="http://www.meihome.asia/api/v1/member/check/store?store_id={{ $stor->stor_id }}" data-storename="{{ $stor->stor_name }}" data-action="show-qrcode"> <i class="fa fa-qrcode"></i> QR Code</button> --}}
                                                <button class="btn btn-white btn-block btn-sm text-primary" data-toggle="modal" data-href="{{ url('/api/v1/member/check/store', [$stor->stor_id]) }}" data-storename="{{ $stor->stor_name }}" data-action="show-qrcode"> <i class="fa fa-qrcode"></i> {{trans('localize.qr_code')}}</button>
                                            </p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    {{trans('localize.Showing')}} {{$stores->firstItem()}} {{trans('localize.to')}} {{$stores->lastItem()}} {{trans('localize.of')}} {{$stores->Total()}} {{trans('localize.Records')}}
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$stores->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    {{trans('localize.Go_To_Page')}}
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$stores->lastPage()}}">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                        <i class="fa fa-share-square-o"></i> {{trans('localize.Go')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="10" class="text-center">@lang('localize.nodata')</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($review)
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="qrcode">
    <div class="modal-dialog">
        <div class="modal-body">
            <div class="row" style="background-color: #fff;" id="qr_code_content">
                <div class="col-sm-12 text-center" style="padding:15px;"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class='fa fa-close'></i></button></div>

                <div class="col-sm-12 text-center" style="background-color: #fff; padding-top: 1px;">
                    <div class="col-sm-6 text-left" style="padding-left: 13%;">
                        <span style="color: #337ab7; font-size: 20px;"><b>{{trans('localize.Accept')}} Mei Point</b></span>
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
<img id='my-image' src="{{ url('assets/images/meihome_logo.png') }}" style="display:none;"/>
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/JQuery-qrcode/qrcode.js"></script>
<script src="/backend/js/html2canvas.js"></script>
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script>
    $(document).ready(function() {

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('.block-merchant').on('click', function (event) {
            event.preventDefault();
            swal({
                title: "@lang('localize.Sorry')",
                text: "@lang('localize.store_block_disclaimer')",
                type: "warning",
                showCancelButton: true,
                showConfirmButton: false,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "@lang('localize.swal_ok')",
                closeOnConfirm: false
            });
        });

        $('#check_all').on('ifToggled', function(event) {
            if(this.checked == true) {
                $('.input_checkbox').iCheck('check');
            } else {
                $('.input_checkbox').iCheck('uncheck');
            }
        });

        $('#batch_update').click(function(event) {
            event.preventDefault();

            if ($("#batch_update_form :checkbox:checked").is(":checked")) {
                swal({
                    title: "{{trans('localize.sure')}}",
                    text: "{{trans('localize.Store_status_will_updated_to_Active_Continue')}}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "{{trans('localize.yes')}}",
					cancelButtonText: "{{trans('localize.no')}}",
                    closeOnConfirm: true
                }, function(isConfirm){
                    if(isConfirm) {
                        $('#spinner').show();
                        $('#batch_update_form').submit();
                    } else {
                        return false;
                    }
                });
            } else {
                swal("{{trans('localize.error')}}", "{{ trans('localize.please_tick_checkbox') }}", "error");
                return false;
            }
        });

        var element = $("#qr_code_content");

        $('button').on('click', function(event) {
            event.preventDefault();

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
            } else if(this_action == 'filter') {
                $('#filter').submit();
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
