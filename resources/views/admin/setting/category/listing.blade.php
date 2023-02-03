@extends('admin.layouts.master')
@section('title', 'Manage Category')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.manage')}} {{trans('localize.Category')}}</h2>
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
                        <a href="/admin/setting/category/add/{{($parent_id != 0) ? $parent_id : ''}}" class="btn btn-primary btn-xs">{{trans('localize.Create_New_Category')}}</a>
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
                                    @if($parent_id == 0)
                                    <th data-hide="phone">{{trans('localize.default_ticket')}}</th>
                                    @endif
                                    <th data-hide="phone">{{ trans('localize.Url_Slug') }}</th>
                                    <th data-hide="phone">{{ trans('localize.wallet_type') }}</th>
                                    <th data-hide="phone" data-sort-ignore="true">{{trans('localize.Action')}}</th>
                                    <th data-hide="phone" data-sort-ignore="true"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                <tr>
                                    <td class="text-nowrap">{{$category->name_en}}</td>
                                    <td class="text-center"><span class="label label-{{$category->status?'primary':'default'}}">{{$category->status? trans('localize.Active') : trans('localize.Inactive') }}</span></td>
                                    @if($parent_id == 0)
                                    <td class="text-center">
                                        <span class="label label-{{$category->default_ticket?'primary':'default'}}">{{$category->default_ticket? trans('localize.Yes') : '' }}</span>
                                    </td>
                                    @endif
                                    @if($category->url_slug)
                                    <td class="text-nowrap">{{ $category->url_slug }}</td>
                                    @else
                                    <td style="color: red;">{{ trans('localize.slug_not_set') }}</td>
                                    @endif
                                    <td>{{ ($category->wallet) ? $category->wallet->name : '' }}</td>
                                    <td class="text-nowrap">
                                        <a href="/admin/setting/category/edit/{{$category->id}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> {{trans('localize.Edit')}} </a>
                                        @if($delete_permission)
                                        <button type="button" class="btn btn-white btn-sm button_delete" data-id="{{ $category->id  }}" data-post="data-php"><i class="fa fa-close"></i> {{trans('localize.Delete')}}</button>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="/admin/setting/category/listing/{{$category->id}}" class="btn btn-primary btn-sm"><i class="fa fa-list"></i> {{trans('localize.Manage_Sub_Category')}} ({{$category->count}}) </a>
                                        @if($edit_permission)
                                        <a href="/admin/setting/category/filter/{{$category->id}}" class="btn btn-success btn-sm"><i class="fa fa-list"></i> {{trans('localize.Manage_Filters')}} ({{ $category->filter }}) </a>
                                        @endif
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

        $.get("/admin/setting/category/check", { category_id: this_id })
        .done(function( data ) {
            if (data > 0) {
                swal("{{trans('localize.Sorry')}}", "{{trans('localize.Unable_to_delete')}}", "error");
            } else {
                swal({
                    title: "{{trans('localize.sure')}}",
                    text: "{{trans('localize.Confirm_to_delete_this_category_and_all_its_sub_categories')}}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "{{trans('localize.yes_delete_it')}}",
                    cancelButtonText: "{{trans('localize.cancel')}}",
                    closeOnConfirm: false
                }, function(){
                    var url = '/admin/setting/category/delete/' + this_id;
                    window.location.href = url;
                });
            }
        });
    });

});
</script>
@endsection