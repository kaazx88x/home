@extends('admin.layouts.master')
@section('title', 'Admin Users')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{ trans('localize.Admin_Users') }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{ trans('localize.dashboard') }}</a>
            </li>
            <li class="active">
                <strong>{{ trans('localize.manage') }}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
             @include('admin.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ trans('localize.administrator_list') }} </h5>
                    <div class="ibox-tools">
                        {{--  <a href="/admin/admin-user/edit/" class="btn btn-primary btn-xs">My Account</a>  --}}
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">#</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.name') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.display_name') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.description') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.role_level') }}</th>
                                    <th class="text-center text-nowrap">{{ trans('localize.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr class="text-center" >
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->display_name }}</td>
                                    <td class='text-left' ><div style="word-wrap: break-word; white-space: pre-wrap; ">{{ $role->description }}</div></td>
                                    <td >{{ $role->role_level }}</td>
                                    <td style="width:15%;">
                                        <a href="{{ url('admin/administrator/role/edit', ['role_id' => $role->id]) }}" class="btn btn-white btn-sm "><i class="fa fa-pencil"></i> {{ trans('localize.edit') }} </a>
                                        @if($delete_permission)
                                        <button type="button" class="btn btn-white btn-sm button_delete" data-id="{{ $role->id  }}" data-post="data-php"><i class="fa fa-close"></i> {{trans('localize.delete')}}</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            {{-- <tfoot>
                                <tr>
                                    <td colspan="7">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                </tr>
                            </tfoot> --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script>
    $(document).ready(function() {


        $('.button_delete').on('click', function(){
            var this_id = $(this).attr('data-id');
            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.Confirm_to_delete_this_user_role')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{trans('localize.yes_delete_it')}}",
                cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false
            }, function(){
                var url = '/admin/administrator/role/delete/' + this_id;
                window.location.href = url;
            });
        });

    });
</script>
@endsection