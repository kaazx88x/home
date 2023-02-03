@extends('admin.layouts.master')
@section('title', 'Manage Filter Item')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Filter_Item')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/setting/filter">{{trans('localize.Filter')}}</a>
            </li>
            <li>
                <a href="/admin/setting/filter/item/{{ $parent->id }}">{{$parent->name}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.manage')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            @include('admin.common.success')
            @include('admin.common.errors')
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        @if($add_permission)
                        <a href="/admin/setting/filter/item/add/{{ $parent->id }}" class="btn btn-primary btn-xs">{{trans('localize.Create_New_Item')}}</a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <input type="text" class="form-control input-sm m-b-xs" id="filter" placeholder="{{trans('localize.Search_in_table')}}">
                    <div class="table-responsive">
                        <table class="footable table table-stripped" data-page-size="20" data-filter=#filter>
                            <thead>
                                <tr>
                                    <th>{{trans('localize.Name')}}</th>
                                    <th data-hide="phone">{{trans('localize.Status')}}</th>
                                    <th data-hide="phone" data-sort-ignore="true">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filters as $filter)
                                <tr>
                                    <td>{{$filter->name}}</td>
                                    <td><span class="label label-{{($filter->status == 1)?'primary':'default'}}">{{($filter->status == 1)? trans('localize.Active') : trans('localize.Inactive') }}</span></td>
                                    <td>
                                        <a href="/admin/setting/filter/item/edit/{{$filter->id}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> {{trans('localize.Edit')}} </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/backend/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/sweetalert/sweetalert.min.js"></script>

<script>
    $(document).ready(function() {

        $('.footable').footable();

    });
</script>
@endsection