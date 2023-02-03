@extends('admin.layouts.master')

@section('title', 'Manage Attribute')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>@lang('localize.product_attributes')</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.product')
            </li>
            <li>
                @lang('localize.attribute')
            </li>
            <li class="active">
                <strong>{{$product['details']->pro_id}} - {{$product['details']->pro_title_en}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    @include('admin.common.notifications')

    <div class="row">
        <div class="tabs-container">

            @include('admin.product.nav-tabs', ['link' => 'attribute'])

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="panel-body">

                        <div id="button_back_view" style="display:none;">
                            <div class="col-lg-12 nopadding" style="margin-bottom: 18px;">
                                <div class="col-md-2 col-md-offset-10">
                                    <button type="button" class="btn btn-primary btn-block btn-md btn-outline" id="button_back">@lang('localize.mer_back')</button>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="ibox float-e-margins well">
                                    <div class="ibox-title">
                                        <h5>@lang('localize.attribute_en')</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <div class="row">
                                            <form class="form-horizontal" action="{{ url('admin/product/attribute', [$mer_id, $pro_id, 'add']) }}" method="POST">
                                            {{ csrf_field() }}
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">@lang('localize.attribute')</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" placeholder="@lang('localize.attribute_hint_en')" name="attribute">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">@lang('localize.attribute_item')</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" placeholder="@lang('localize.attribute_item_hint_en')" name="attribute_item">
                                                    </div>
                                                </div>
                                                <div class="ibox-title">
                                                    <h5>@lang('localize.attribute_cn')</h5>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">@lang('localize.attribute')</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" placeholder="@lang('localize.attribute_hint_cn')" name="attribute_cn">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">@lang('localize.attribute_item')</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" placeholder="@lang('localize.attribute_item_hint_cn')" name="attribute_item_cn">
                                                    </div>
                                                </div>
                                                <div class="ibox-title">
                                                    <h5>@lang('localize.attribute_cnt')</h5>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">@lang('localize.attribute')</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" placeholder="@lang('localize.attribute_hint_cnt')" name="attribute_cnt">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">@lang('localize.attribute_item')</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" placeholder="@lang('localize.attribute_item_hint_cnt')" name="attribute_item_cnt">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-9 col-sm-offset-2">
                                                        <button type="submit" class="btn btn-outline btn-primary pull-right">@lang('localize.add_attribute')</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($edit_permission)
                        <div class="col-lg-12 nopadding" id="button_add_view" style="display:block;margin-bottom: 18px;">
                            <div class="col-md-2 col-md-offset-10">
                                <button type="button" class="btn btn-primary btn-block btn-md btn-outline" id="button_add">@lang('localize.add_attribute')</button>
                            </div>
                        </div>
                        @endif

                        <div class="col-lg-12" id="table_list">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-nowrap" colspan="2">@lang('localize.English')</th>
                                            <th class="text-center text-nowrap" colspan="2">@lang('localize.Chinese') @lang('localize.Simplified')</th>
                                            <th class="text-center text-nowrap" colspan="2">@lang('localize.Chinese') @lang('localize.Traditional')</th>
                                            <th class="text-center text-nowrap" ></th>
                                        </tr>
                                        <tr>
                                            <th class="text-center text-nowrap">@lang('localize.attribute_en')</th>
                                            <th class="text-center text-nowrap">@lang('localize.attribute_item_en')</th>
                                            <th class="text-center text-nowrap">@lang('localize.attribute_cn')</th>
                                            <th class="text-center text-nowrap">@lang('localize.attribute_item_cn')</th>
                                            <th class="text-center text-nowrap">@lang('localize.attribute_cnt')</th>

                                            <th class="text-center text-nowrap">@lang('localize.attribute_item_cnt')</th>
                                            <th class="text-center text-nowrap">@lang('localize.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lists as $key => $item)
                                        <tr class="text-center">
                                            <td style="width:20%;" nowrap>
                                                {{ $item->attribute }}
                                            </td>
                                            </td>
                                            <td style="width:20%;" nowrap>
                                                {{ $item->attribute_item }}
                                            </td>
                                              <td style="width:20%;" nowrap>
                                               {{ $item->attribute_cn }}
                                            </td>
                                            <td style="width:20%;" nowrap>
                                                {{ $item->attribute_item_cn }}
                                            </td>
                                            <td style="width:20%;" nowrap>
                                                {{ $item->attribute_cnt }}
                                            </td>
                                            <td style="width:20%;" nowrap>
                                               {{ $item->attribute_item_cnt }}
                                            </td>
                                            <td nowrap>
                                                @if($edit_permission)
                                                <button class="btn btn-white btn-sm text-primary" data-id="{{ $item->id }}" data-action="edit_attribute"><i class="fa fa-pencil"></i> @lang('localize.Edit')</button>
                                                <button class="btn btn-white btn-sm text-danger" data-id="{{ $item->id }}" data-action="delete_attribute"><i class="fa fa-trash"></i> @lang('localize.delete')</button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script src="/backend/js/custom.js"></script>

<script>
    $(document).ready(function () {

        $('#button_add').click(function() {
            $('#button_back_view').show();
            $('#button_add_view').hide();
            $('#table_list').hide();
        });

         $('#button_back').click(function() {
            $('#button_back_view').hide();
            $('#button_add_view').show();
            $('#table_list').show();
        });

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('button').on('click', function() {
            var attribute_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');

            if (this_action == 'edit_attribute') {
                edit_product_attribute(attribute_id, "{{ $product['details']->pro_id }}", "{{ $mer_id }}");
            } else if(this_action == 'delete_attribute') {
                $.get( '/attribute/parent_check_attribute_exist/{{ $mer_id }}/{{ $pro_id }}/' + attribute_id, function( response ) {
                    if(response == 0) {

                        swal({
                            title: "{{ trans('localize.sure') }}",
                            text: "{{ trans('localize.confirm_delete_attribute') }}",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#d9534f",
                            confirmButtonText: "{{ trans('localize.yes_delete_it') }}",
							cancelButtonText: "{{ trans('localize.cancel') }}",
                            closeOnConfirm: true
                        }, function(){
                            var url = '/product/attribute_parent/delete/' + attribute_id + '/normal?product_id={{$pro_id}}&merchant_id={{$mer_id}}';
                            window.location.href = url;
                        });

                    } else if (response == 1) {

                        swal({
                            title: "{{trans('localize.Warning')}}",
                            text: "{{trans('localize.attr_msg1')}} <span class='text-danger'>{{trans('localize.attr_msg2')}}</span>. {{trans('localize.Continue')}}?",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#d9534f",
                            confirmButtonText: "{{ trans('localize.yes_delete_it') }}",
							cancelButtonText: "{{ trans('localize.cancel') }}",
                            closeOnConfirm: true,
                            html: true
                        }, function(){
                            var url = '/product/attribute_parent/delete/' + attribute_id + '/force?product_id={{$pro_id}}&merchant_id={{$mer_id}}';
                            window.location.href = url;
                        });

                        return false;
                    }
                });
            }
        });
    });
</script>
@endsection