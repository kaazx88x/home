@extends('admin.layouts.master')

@section('title', 'Manage Filter')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>@lang('localize.product_filter')</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.product')
            </li>
            <li>
                @lang('localize.Filter')
            </li>
            <li class="active">
                <strong>{{$product['details']->pro_id}} - {{$product['details']->pro_title_en}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    @include('admin.common.error')
    @include('admin.common.errors')
    @include('admin.common.success')

    <div class="row">
        <div class="tabs-container">

            @include('admin.product.nav-tabs', ['link' => 'filter'])

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="alert alert-info" role="alert">
                                    <strong>{{trans('localize.information')}}:</strong>
                                    <br/>{{trans('localize.filter_msg1')}}
                                    <br/>{{trans('localize.filter_msg2')}}
                                    <br/>{{trans('localize.filter_msg3')}}
                                </div>
                            </div>
                        </div>

                        @if(count($lists['array']) > 0)
                        <form id="update_filter" action="/admin/product/filter/{{ $mer_id }}/{{ $pro_id }}" method="POST">
                        {{ csrf_field() }}
                            <div class="row">
                                <div class="col-lg-12">

                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-nowrap" width="10%">{{trans('localize.Filter')}}</th>
                                                    <th class="text-center text-nowrap" width="70%">{{trans('localize.Item')}}</th>
                                                    <th class="text-center text-nowrap" width="20%">{{trans('localize.copy_to_attribute')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($lists['array'] as $key => $list)
                                                <tr class="text-center">
                                                    <td style="vertical-align: middle;">
                                                        <strong>{{ $list['attribute']->name }}</strong>
                                                    </td>

                                                    <td>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                @foreach ($list['item'] as $item)
                                                                <div class="col-sm-4 text-left" style="padding:2px;">
                                                                    <div class="i-checks">
                                                                        <label>
                                                                            <input type="checkbox" name="filter[]" value="{{ $list['attribute']->id }},{{ $item->id }}" {{ ($item->selected == 1)? 'checked' : '' }}> <i></i>{{ $item->name }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                    </td>

                                                    <td style="vertical-align: middle;">
                                                        <div class="i-checks">
                                                            <label>
                                                                <input type="checkbox" name="copy[]" value="{{ $list['attribute']->id }}">
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="col-sm-2 pull-right">
                                            @if($edit_permission)
                                            <button class="btn btn-block btn-primary" id="submit_form">{{trans('localize.submit')}}</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endif
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

        $('#submit_form').click(function(e) {
            e.preventDefault();
            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.confirm_update_filter')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "{{trans('localize.Yes')}}",
				cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function(isConfirm){
                if(isConfirm) {
                    $("#update_filter").submit();
                } else {
                    return false;
                }
            });
        });

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    });

</script>
@endsection