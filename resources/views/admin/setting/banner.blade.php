@extends('admin.layouts.master')
@section('title', 'Manage Banner Image')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.manage')}} {{trans('localize.Banner')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">{{trans('localize.dashboard')}}</a>
            </li>
            <li>
                <a href="/admin/setting/banner">{{trans('localize.Banner')}}</a>
            </li>
            <li class="active">
                <strong>{{$type->name}}</strong>
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
                    <h5>{{trans('localize.Drag_and_drop_list_to_change_order')}}</h5>
                    <div class="ibox-tools">
                        @if($add_permission)
                        <a href="/admin/setting/banner/add/{{$type->id}}" class="btn btn-primary btn-xs">{{trans('localize.Create_New_Banner')}}</a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <input type="text" class="form-control input-sm m-b-xs" id="filter" placeholder="{{trans('localize.Search_in_table')}}">
                    <div class="table-responsive">
                        <table class="footable table table-stripped" data-page-size="50" data-filter=#filter>
                            <thead>
                                <tr>
                                    <th>{{trans('localize.Sort')}}</th>
                                    <th>{{trans('localize.Title')}}</th>
                                    <th>{{trans('localize.URL')}}</th>
                                    <th>{{trans('localize.Open_in_New_Page')}}</th>
                                    <th>{{trans('localize.country')}}</th>
                                    <th>{{trans('localize.image')}}</th>
                                    <th data-hide="phone">{{trans('localize.Status')}}</th>
                                    <th data-hide="phone" data-sort-ignore="true">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            <tbody class="sort">
                                @foreach($banners as $banner)
                                <tr id="{{$banner->bn_id}}">
                                    <td>{{$banner->order}}</td>
                                    <td>{{$banner->bn_title}}</td>
                                    <td>{{$banner->bn_redirecturl}}</td>
                                    <td>{{($banner->bn_open == 1)? trans('localize.Yes') : trans('localize.No')}}</td>
                                    <td>
                                        @if($banner->countries->count() > 0)
                                            <ul>
                                            @foreach($banner->countries as $country)
                                            <li>{{ $country->co_name }}</li>
                                            @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- <img src="{{ url('images/banner/'.$banner->bn_img) }}" style="height:40px;"> --}}
                                        <img src="{{ env('IMAGE_DIR') . '/banner/' . $banner->bn_img }}" style="height:40px;">
                                    </td>
                                    <td><span class="label label-{{$banner->bn_status?'primary':'default'}}">{{$banner->bn_status? trans('localize.Active') : trans('localize.Inactive') }}</span></td>
                                    <td>
                                        <a href="/admin/setting/banner/edit/{{$banner->bn_id}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> {{trans('localize.Edit')}} </a>
                                        @if($delete_permission)
                                        <button type="button" class="btn btn-white btn-sm button_delete" data-id="{{ $banner->bn_id  }}" data-post="data-php"><i class="fa fa-close"></i> {{trans('localize.Delete')}}</button>
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
<link href="/backend/css/plugins/sortable/sortable.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/backend/js/plugins/sortable/sortable.js"></script>

<script>
    $(document).ready(function() {
        $( ".sort" ).sortable({
            update : function () {
                var order = $(this).sortable('toArray').toString();
                var pass = 'newOrder='+order;

                 $.ajax({
                    type: 'get',
                    data: pass,
                    url: '/admin/setting/banner/save_order',
                    beforeSend : function() {
                        $('#spinner').show();
                    },
                    success: function () {
                        $('#spinner').hide();
                    }
                });
            }
        });

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


        $('.footable').footable();
    });
</script>
@endsection