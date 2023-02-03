@extends('admin.layouts.master')
@section('title', 'Manage Product')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.manage')}} {{trans('localize.Inquiries')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="">{{trans('localize.customer')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.manage')}} {{trans('localize.Inquiries')}}</strong>
            </li>
        </ol>
    </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox float-e-margins border-bottom">
        <a class="collapse-link nolinkcolor">
            <div class="ibox-title ibox-title-filter">
                <h5>@lang('localize.Search_Filter')</h5>
                <div class="ibox-tools">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </a>
        <div class="ibox-content ibox-content-filter" style="display:none;">
            <div class="row">
                <form class="form-horizontal" id="filter" action='/admin/customer/inquiries' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.search')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['search']}}" placeholder="{{trans('localize.Search_in_table')}}" class="form-control" id="search" name="search">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.sort')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                    <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.Newest')}}</option>
                                    <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.Oldest')}}</option>
                                </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-2">
                            <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
        @include('admin.common.success')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">#No</th>
                                    <th width="15%" class="text-center text-nowrap">{{trans('localize.Name')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.email')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Phone')}}</th>
                                    <th class="text-left text-nowrap">{{trans('localize.message')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Action')}}</th>
                                </tr>
                            </thead>
                            @if ($inquiries->total())
                                <tbody>
                                    <?php
                                        $i = 1;
                                        $i = (( $inquiries->currentPage() - 1 ) * $inquiries->perPage() ) + $i;
                                    ?>
                                    @foreach($inquiries as $inquiry)
                                    <tr class="text-center">
                                        <td>{{ $i }}</td>
                                        <td>{{$inquiry->iq_name}}</td>
                                        <td>{{$inquiry->iq_emailid}}</td>
                                        <td>{{$inquiry->iq_phonenumber}}</td>
                                        <td class="text-left">{{$inquiry->iq_message}}</td>
                                        <td>
                                            @if($delete_permission)
                                            <a href="/admin/customer/inquiries/delete/{{$inquiry->iq_id}}" class="fa fa-trash fa-2x"><i></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    {{trans('localize.Showing')}} {{$inquiries->firstItem()}} {{trans('localize.to')}} {{$inquiries->lastItem()}} {{trans('localize.of')}} {{$inquiries->Total()}} {{trans('localize.Records')}}
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$inquiries->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    {{trans('localize.Go_To_Page')}}
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$inquiries->lastPage()}}">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                        <i class="fa fa-share-square-o"></i> {{trans('localize.Go')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">@lang('localize.nodata')</td>
                                </tr>
                            @endif
                        </table>
                        <div class="text-center"></div>
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

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$inquiries->lastPage()}};
            if(input > max){
                $(this).val(max);
            }
        });

        //$('.footable').footable();

    });

         function gotopage($page) {
            $val =  $("#pageno").val();

            var href = window.location.href.substring(0, window.location.href.indexOf('?'));
            var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
            var newParam = $page + '=' + $val;

            if (qs.indexOf($page + '=') == -1) {
                if (qs == '') {
                    qs = '?'
                }
                else {
                    qs = qs + '&'
                }
                qs = newParam;

            }
            else {
                var start = qs.indexOf($page + "=");
                var end = qs.indexOf("&", start);
                if (end == -1) {
                    end = qs.length;
                }
                var curParam = qs.substring(start, end);
                qs = qs.replace(curParam, newParam);
            }
            window.location.replace(href + '?' + qs);
        }
</script>
@endsection
