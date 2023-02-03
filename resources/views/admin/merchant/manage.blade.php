@extends('admin.layouts.master')

@section('title', 'Manage Merchant')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{ ucfirst($type) }} {{trans('localize.Merchants')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.Merchants')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.manage')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
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
                <form class="form-horizontal" id="filter" action='/admin/merchant/manage/{{$type}}' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">ID</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.Search_By_Merchant_ID')}}" class="form-control" id="id" name="id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['name']}}" placeholder="{{trans('localize.Search_by_Merchant_Name')}}" class="form-control" id="name" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.email')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['email']}}" placeholder="{{trans('localize.Search_by_Merchant_Email')}}" class="form-control" id="email" name="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.country')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['country']}}" placeholder="{{trans('localize.Search_By')}} {{trans('localize.country')}}" class="form-control" id="country" name="country">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Status')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="status" name="status">
                                @foreach ($status_list as $key => $stat)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['status']) ? 'selected' : '' }}>{{ $stat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if($type == "offline")
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.category')}}</label>
                        <div class="col-sm-9">
                            <div class="ibox-content">
                                <table id="showCats" class="table table-condensed table-striped">
                                    <tbody>
                                        @if(!empty($offline_category_name))
                                        <tr id="{{ $input['selected_cats'] }}">
                                            <td>{{$offline_category_name->name_en}}</td>
                                            <td><a onclick="removeCat('{{$input['selected_cats']}}'); return false;" class="btn btn-xs btn-danger pull-right">Remove</a></td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <input type="hidden" class="compulsary" id="selected_cats" name="selected_cats" value="{{ $input['selected_cats'] }}">
                                <div id="cats" class="demo" style="overflow-y:scroll; height:100px;"></div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.sort')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.Newest')}}</option>
                                <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.Oldest')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-2">
                            <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            @include('admin.common.notifications')

            <div class="ibox">

                @if($export_permission)
                <div class="ibox-title" style="display: block;">
                    <div class="ibox-tools" style="margin-bottom:10px;">

                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> Export All <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('admin.export.merchant', [$type]) }}?action=export&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="{{ route('admin.export.merchant', [$type]) }}?action=export&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="{{ route('admin.export.merchant', [$type]) }}?action=export&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>

                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-white btn-sm dropdown-toggle"> Export This Page <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('admin.export.merchant', [$type]) }}?action=export_by_page&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                <li><a href="{{ route('admin.export.merchant', [$type]) }}?action=export_by_page&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                <li><a href="{{ route('admin.export.merchant', [$type]) }}?action=export_by_page&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                            </ul>
                        </div>

                    </div>
                </div>
                @endif

                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">#ID</th>
                                    <th class="text-center">{{trans('localize.Name')}}</th>
                                    <th class="text-center">{{trans('localize.Username')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.email')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.country')}}</th>
                                    <th class="text-center text-nowrap">@lang('localize.commission')</th>
                                    <th class="text-center text-nowrap">@lang('common.credit_name')</th>
                                    <th class="text-center text-nowrap">{{trans('localize.manage')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Register_Date')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Updated_By')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            @if ($merchants->total())
                                <tbody>
                                    @foreach ($merchants as $key => $merchant)
                                    <tr class="text-center">
                                        <td>{{ $merchant['details']->mer_id }}</td>
                                        <td>{{ $merchant['details']->mer_fname }}</td>
                                        <td>{{ $merchant['details']->username }}</td>
                                        <td>{{ $merchant['details']->email }}</td>
                                        <td>{{ $merchant['details']->co_name }}</td>
                                        <td>{{ $merchant['details']->mer_commission . '%' }}</td>
                                        <td>
                                            <a class="nolinkcolor btn btn-white btn-block btn-sm" href="/admin/merchant/credit/{{$merchant['details']->mer_id}}">{{ ($merchant['details']->mer_vtoken)? $merchant['details']->mer_vtoken : '0.00' }}</a>
                                        </td>
                                        <td class="text-nowrap">
                                            @if($manage_store)
                                            <a class="btn btn-white btn-sm btn-block" href="/admin/store/manage/{{$merchant['details']->mer_id}}"><span><i class="fa fa-bank"></i> {{trans('localize.manage')}} {{trans('localize.store')}}  ( {{$merchant['store_by_country_permission_count']}} / {{$merchant['store_count']}} )</span></a>
                                            @endif

                                            @if($manage_store_user)
                                            <a class="btn btn-white btn-sm btn-block" href="/admin/store/user/{{$merchant['details']->mer_id}}"><span><i class="fa fa-users"></i> {{trans('localize.manage')}} {{trans('localize.User')}}  ( {{$merchant['users_by_country_permission_count']}} / {{$merchant['storeuser_count']}} )</span></a>
                                            @endif
                                        </td>
                                        {{-- <td nowrap>{{ $merchant['details']->created_at->format('d M Y H:i A') }}</td> --}}
                                        <td nowrap>{{ \Helper::UTCtoTZ($merchant['details']->created_at) }}</td>
                                        <td>
                                            @if ($merchant->updater_id)
                                                @if ($merchant['details']->mer_staus == 1)
                                                    <span class="text-navy"> {{trans('localize.Activated_By')}}</span>
                                                @elseif ($merchant['details']->mer_staus == 0)
                                                    <span class="text-danger"> {{trans('localize.Blocked_By')}}</span>
                                                @endif
                                                <br/>
                                                <b>{{ $merchant->updater_name }} </b>
                                                <br/> on <br/> {{ \Helper::UTCtoTZ($merchant->updated_at) }}
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            <p>
                                                @if ($merchant['details']->mer_staus == 1)
                                                <span class="text-navy"> <i class='fa fa-check'></i> {{trans('localize.Merchant_in_use')}}</span>
                                                @elseif ($merchant['details']->mer_staus == 2)
                                                <span class="text-warning"> <i class='fa fa-exclamation-triangle'></i> {{trans('localize.Pending_Approval')}}</span>
                                                @else
                                                <span class="text-danger"> <i class='fa fa-ban'></i> {{trans('localize.Merchant_Blocked')}}</span>
                                                @endif
                                            </p>

                                            <p>
                                                @if($edit_permission)
                                                <a class="btn btn-white btn-sm" href="/admin/merchant/edit/{{$merchant['details']->mer_id}}"><span><i class="fa fa-edit"></i> {{trans('localize.Edit')}}</span></a>
                                                @endif
                                                <a class="btn btn-white btn-sm" href="/admin/merchant/view/{{$merchant['details']->mer_id}}"><span><i class="fa fa-file-text-o"></i> {{trans('localize.view')}}</span></a>
                                            </p>

                                            @if($block_merchant_permission)
                                            <p>
                                                @if($merchant['details']->mer_staus == 1)
                                                    <a class="btn btn-white btn-sm text-danger btn-block block-merchant" href="{{ url('update_merchant_status', [$merchant['details']->mer_id, 0]) }}"><span><i class="fa fa-ban"></i> {{trans('localize.Block')}}</span></a>
                                                @elseif ($merchant['details']->mer_staus == 2)
                                                    <a class="btn btn-white btn-sm text-warning btn-block" href="{{ url('update_merchant_status', [$merchant['details']->mer_id, 1]) }}"><span><i class="fa fa-thumbs-o-up"></i> {{trans('localize.Approve_Merchant')}}</span></a>
                                                @else
                                                    <a class="btn btn-white btn-sm text-navy btn-block" href="{{ url('update_merchant_status', [$merchant['details']->mer_id, 1]) }}"><span><i class="fa fa-check"></i> {{trans('localize.Unblock')}}</span></a>
                                                @endif
                                            </p>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    {{trans('localize.Showing')}} {{$merchants->firstItem()}} {{trans('localize.to')}} {{$merchants->lastItem()}} {{trans('localize.of')}} {{$merchants->Total()}} {{trans('localize.Records')}}
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$merchants->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    {{trans('localize.Go_To_Page')}}
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$merchants->lastPage()}}">
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
            </div>
        </div>
    </div>
</div>

@endsection @section('style')
    <link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
    <link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/backend/css/plugins/jsTree/style.min.css" rel="stylesheet">
@endsection

@section('script')
    <script src="/backend/js/plugins/footable/footable.all.min.js"></script>
    <script src="/backend/js/plugins/jsTree/jstree.min.js"></script>
    <script src="/backend/js/plugins/iCheck/icheck.min.js"></script>

<script>
    $('.ibox-content').removeAttr("style");
    $('#cats').jstree({
        'core' : {
            'data' : {
                'url' : function (node) {
                    return node.id === '#' ? '/get_offline_category' : '/get_offline_category/' + node.id;
                },
                'dataType' : 'json',
            },
            'themes' : {
                'icons' : false
            },
        },
        'plugins' : [ 'wholerow', 'themes' ],
    });

    $('#cats').on("changed.jstree", function (e, data) {
        if(data.selected.length) {

            var catSelected = $('#selected_cats').val();
            var catExist = jQuery.inArray( data.node.id, catSelected );

            var cat_id = data.node.id;

            if(!$('#selected_cats').val().length){
                $('input[name="selectedCats[]"]').filter(function() {
                    console.log($(this).val());
                });
                $('#selected_cats').val(data.node.id);
                $('#showCats').append("<tr id='" + data.node.id + "'><td>" + data.node.text + "</td><td><a onClick='removeCat(\"" + data.node.id + "\"); return false;' class='btn btn-xs btn-danger pull-right'>Remove</a></td></tr>");
                $('#cats').css('display','none');
            }else{
                 swal("Sorry!", "{{trans('localize.Maximum_1_category_only_can_be_selected')}}", "error");
            }
        }
    });

    function removeCat(id) {
        var catSelected = $('#selected_cats').val();
        jQuery('#' + id).remove();
        $('#cats').css('display','block');

        $('#selected_cats').val("");
    }

    $(document).ready(function() {

        if($('#selected_cats').val().length){
            $('#cats').css('display','none');
        }
        $('#showCats').css('margin-bottom','0px');

        $('.block-merchant').on('click', function (event) {
            event.preventDefault();
            var href = $(this).attr('href');

            swal({
                title: "@lang('localize.sure')",
                text: "@lang('localize.merchant_block_disclaimer')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('localize.Block')",
                cancelButtonText: "@lang('localize.cancel')",
                closeOnConfirm: false
            }, function(isConfirm) {
                if(isConfirm)
                    window.location.replace(href);
            });
        });

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$merchants->lastPage()}};
            if(input > max){
                $(this).val(max);
            }
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