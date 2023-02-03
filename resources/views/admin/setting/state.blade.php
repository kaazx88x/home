@extends('admin.layouts.master')
@section('title', 'Manage State')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Manage State</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            @include('admin.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a href="/admin/setting/state/add" class="btn btn-primary btn-xs">Create new state</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <input type="text" class="form-control input-sm m-b-xs" id="filter" placeholder="Search in table">
                    <div class="table-responsive">
                        <table class="footable table table-stripped" data-page-size="20" data-filter=#filter>
                            <thead>
                                <tr>
                                    <th>State</th>
                                    <th>Country</th>
                                    <th data-hide="phone">Status</th>
                                    <th data-hide="phone" data-sort-ignore="true">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($states as $state)
                                <tr>
                                    <td>{{$state->name}}</td>
                                    <td>{{$state->co_name}}</td>
                                    <td><span class="label label-{{$state->status?'primary':'default'}}">{{$state->status?'Active':'Inactive'}}</span></td>
                                    <td>
                                        <a href="/admin/setting/state/edit/{{$state->id}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit </a>
                                        <button type="button" class="btn btn-white btn-sm button_delete" data-id="{{ $state->id }}" data-post="data-php"><i class="fa fa-close"></i> Delete</button>
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
            swal({
                title: "Are you sure?",
                text: "Confirm to delete this state?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            }, function(){
                var url = '/admin/setting/state/delete/' + this_id;
                window.location.href = url;
            });
        });

    });
</script>
@endsection