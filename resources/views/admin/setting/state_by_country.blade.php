@extends('admin.layouts.master')
@section('title', 'Manage State')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Manage_State_in')}} {{$country->co_name}}</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            @include('admin.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        @if($add_permission)
                        <a href="/admin/setting/state/add/{{$country->co_id}}" class="btn btn-primary btn-xs">{{trans('localize.Create_New_State')}}</a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <input type="text" class="form-control input-sm m-b-xs" id="filter" placeholder="{{trans('localize.Search_in_table')}}">
                    <div class="table-responsive">
                        <table class="footable table table-stripped" data-page-size="20" data-filter=#filter>
                            <thead>
                                <tr>
                                    <th>{{trans('localize.state')}}</th>
                                    <th>{{trans('localize.country')}}</th>
                                    <th data-hide="phone">{{trans('localize.Status')}}</th>
                                    <th data-hide="phone" data-sort-ignore="true">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($states as $state)
                                <tr>
                                    <td>{{$state->name}}</td>
                                    <td>{{$state->co_name}}</td>
                                    <td><span class="label label-{{$state->status?'primary':'default'}}">{{$state->status? trans('localize.Active') : trans('localize.Inactive') }}</span></td>
                                    <td>
                                        <a href="/admin/setting/state/edit/{{$state->id}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> {{trans('localize.Edit')}} </a>
                                        @if($delete_permission)
                                        <button type="button" class="btn btn-white btn-sm button_delete" data-id="{{ $state->id }}" co-id="{{$state->country_id}}" data-post="data-php"><i class="fa fa-close"></i> {{trans('localize.Delete')}}</button>
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

        $('.button_delete').on('click', function(){
            var this_id = $(this).attr('data-id');
            var co_id = $(this).attr('co-id');
            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.Confirm_to_delete_this_state')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{trans('localize.yes_delete_it')}}",
                cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false
            }, function(){
                var url = '/admin/setting/state/delete/' + this_id + '/' + co_id;
                window.location.href = url;
            });
        });

    });
</script>
@endsection