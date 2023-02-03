@extends('admin.layouts.master')
@section('title', 'Manage CMS Page')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.manage')}} CMS {{trans('localize.Page')}}</h2>
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
                        <a href="/admin/setting/cms/add/{{ $cms_type->id }}" class="btn btn-primary btn-xs">{{trans('localize.Create_New_CMS_Page')}}</a>
                        @endif
                        @if ($cms_type->type == 'web')
                        <button type="button" class="btn btn-primary btn-xs btn-outline pull-left button_sorting" data-post="data-php"><i class="fa fa-sort-numeric-asc"></i> {{trans('localize.Sorting_footer_link')}}</button>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <input type="text" class="form-control input-sm m-b-xs" id="filter" placeholder="{{trans('localize.Search_in_table')}}">
                    <div class="table-responsive">
                        <table class="footable table table-stripped" data-page-size="20" data-filter=#filter>
                            <thead>
                                <tr>
                                    <th>{{trans('localize.CMS_Page_Title')}}</th>
                                    @if ($cms_type->type == 'web')
                                    <th data-hide="phone">{{trans('localize.Url_Slug')}}</th>
                                    <th data-hide="phone" data-sort-ignore="true">{{trans('localize.Display_at_footer')}}</th>
                                    @endif
                                    <th data-hide="phone">{{trans('localize.Status')}}</th>
                                    <th data-hide="phone" data-sort-ignore="true">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cms_pages as $cms)
                                <tr>
                                    <td>{{$cms->cp_title_en}}</td>
                                    @if ($cms_type->type == 'web')
                                    <td><a href="{{ url('/info', $cms->cp_url) }}" target="_blank" class="nolinkcolor">http://www.meihome.asia/info/{{$cms->cp_url}}</a></td>
                                    <td><span class="label label-{{$cms->cp_footer?'primary':'default'}}">{{$cms->cp_footer? trans('localize.Yes') : trans('localize.No') }}</span></td>
                                    @endif
                                    <td><span class="label label-{{$cms->cp_status?'primary':'default'}}">{{$cms->cp_status? trans('localize.Active') : trans('localize.Inactive') }}</span></td>
                                    <td>
                                        <a href="/admin/setting/cms/edit/{{$cms->cp_id}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> {{trans('localize.Edit')}} </a>
                                        @if($delete_permission)
                                        <button type="button" class="btn btn-white btn-sm button_delete" data-id="{{ $cms->cp_id  }}" data-post="data-php"><i class="fa fa-close"></i> {{trans('localize.Delete')}}</button>
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
            swal({
                title: "{{trans('localize.sure')}}",
                text: "{{trans('localize.Confirm_to_delete_this_cms_page')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{trans('localize.yes_delete_it')}}",
                cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false
            }, function(){
                var url = '/admin/setting/cms/delete/' + this_id;
                window.location.href = url;
            });
        });

        $('.button_sorting').on('click', function() {
            $.get( '/admin/setting/cms/footer/sorting', function( data ) {
                $('#myModal').modal();
                $('#myModal').on('shown.bs.modal', function(){
                    $('#myModal .load_modal').html(data);
                });
                $('#myModal').on('hidden.bs.modal', function(){
                    $('#myModal .modal-body').data('');
                });
            });
        });

    });
</script>
@endsection