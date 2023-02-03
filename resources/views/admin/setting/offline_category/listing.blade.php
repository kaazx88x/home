@extends('admin.layouts.master')
@section('title', 'Manage Offline Category')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.manage')}} {{trans('localize.Offline')}} {{trans('localize.Category')}}</h2>
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
                        <a href="/admin/setting/offline_category/add/{{($parent_id != 0) ? $parent_id : ''}}" class="btn btn-primary btn-xs">{{trans('localize.Create_New_Offline_Category')}}</a>
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
                                    <th data-hide="phone">{{trans('localize.wallet_type')}}</th>
                                    <th data-hide="phone" data-sort-ignore="true">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                <tr>
                                    <td>{{$category->name_en}}</td>
                                    <td><span class="label label-{{$category->status?'primary':'default'}}">{{$category->status? trans('localize.Active') : trans('localize.Inactive') }}</span></td>
                                    <td>{{ ($category->wallet) ? $category->wallet->name : '' }}</td>
                                    <td>
                                        <a href="/admin/setting/offline_category/edit/{{$category->id}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> {{trans('localize.Edit')}} </a>
                                        @if($delete_permission)
                                        <button type="button" class="btn btn-white btn-sm button_delete" data-id="{{ $category->id  }}" data-post="data-php"><i class="fa fa-close"></i> {{trans('localize.Delete')}}</button>
                                        @endif
                                        &nbsp;&nbsp;
                                        <a href="/admin/setting/offline_category/listing/{{$category->id}}" class="btn btn-primary btn-sm"><i class="fa fa-list"></i> {{trans('localize.Manage_Sub_Category')}} ({{$category->count}}) </a>
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

        $('.button_delete').on('click', function() {
            var this_id = $(this).attr('data-id');

            $.get("/admin/setting/offline_category/check", { category_id: this_id })
            .done(function( data ) {
                if (data > 0) {
                    swal("{{trans('localize.Sorry')}}", "{{trans('localize.Unable_to_delete_This_category_is_tied_up_to_store')}}", "error");
                } else {
                    swal({
                        title: "{{trans('localize.sure')}}",
                        text: "{{trans('localize.Unable_to_delete_This_category_is_tied_up_to_store')}}",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "{{trans('localize.yes_delete_it')}}",
                        cancelButtonText: "{{trans('localize.cancel')}}",
                        closeOnConfirm: false
                    }, function(){
                        var url = '/admin/setting/offline_category/delete/' + this_id;
                        window.location.href = url;
                    });
                }
            });
        });

    });
</script>
@endsection