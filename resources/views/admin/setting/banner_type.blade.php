@extends('admin.layouts.master')
@section('title', 'Manage Banner Image')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Banner_Management')}}</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            @include('admin.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{trans('localize.Banner_Type')}}</h5>
                    <div class="ibox-tools">
                        @if($add_permission)
                        <a href="/admin/setting/banner/type/add" class="btn btn-primary btn-xs">{{trans('localize.Create_New_Type')}}</a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    {{-- <input type="text" class="form-control input-sm m-b-xs" id="filter" placeholder='Search in table'> --}}
                    <div class="table-responsive">
                        <table class="table table-stripped">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>{{trans('localize.Banner_Type')}}</th>
                                    <th>{{trans('localize.Description')}}</th>
                                    <th data-hide="phone" data-sort-ignore="true">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bannertypes as $banner)
                                <tr>
                                    <td>{{$banner->id}}</td>
                                    <td>{{$banner->name}}</td>
                                    <td>{{$banner->description}}</td>
                                    <td>
                                        <a href="/admin/setting/banner/type/edit/{{$banner->id}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> {{trans('localize.Edit')}}</a>
                                        <a href="/admin/setting/banner/manage/{{$banner->id}}" class="btn btn-primary btn-sm"><i class="fa fa-list"></i> {{trans('localize.manage')}} {{trans('localize.Banner')}} ({{$banner->total}}) </a>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">
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

        $('.button_delete').on('click', function(){
            var this_id = $(this).attr('data-id');
            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.Confirm_to_delete_this_banner')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{trans('localize.yes_delete_it')}}",
                cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false
            }, function(){
                var url = '/admin/setting/banner/delete/' + this_id;
                window.location.href = url;
            });
        });

    });
</script>
@endsection