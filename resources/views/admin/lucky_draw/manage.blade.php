@extends('admin.layouts.master')

@section('title', 'Manage Lucky Draw')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.lucky_draw')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.lucky_draw')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.manage')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox float-e-margins border-bottom">
                <a class="collapse-link nolinkcolor">
                <div class="ibox-title ibox-title-filter">
                    <h5>{{trans('localize.Search_Filter')}}</h5>
                    <div class="ibox-tools">
                        <i class="fa fa-chevron-down"></i>
                    </div>
                </div>
                </a>
                <div class="ibox-content ibox-content-filter" style="display:none;">
                    <div class="row">
                        <form class="form-horizontal" id="filter" action= "{{ url('admin/lucky_draw/manage') }}" method="GET">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{{trans('localize.search')}}</label>
                                <div class="col-sm-5">
                                    <input type="text" value="{{$input['email']}}" placeholder="{{trans('localize.email')}}" class="form-control" name="email">
                                </div>

                                <div class="col-sm-5">
                                    <input type="text" value="{{$input['phone']}}" placeholder="{{trans('localize.phone')}}" class="form-control" name="phone">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-1 control-label">{{trans('localize.Status')}}</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="status">
                                        <option value="" {{ ($input['status'] == "") ? 'selected' : '' }}>{{trans('localize.all')}}</option>
                                        <option value="unclaimed" {{ ($input['status'] == "unclaimed") ? 'selected' : '' }}>{{trans('localize.unclaimed')}}</option>
                                        <option value="unredeem" {{ ($input['status'] == "unredeem") ? 'selected' : '' }}>{{trans('localize.notredeem')}}</option>
                                        <option value="redeemed" {{ ($input['status'] == "redeemed") ? 'selected' : '' }}>{{trans('localize.redeemed')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-1">
                                    <button type="submit" class="btn btn-block btn-outline btn-primary" data-action="filter">{{trans('localize.search')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @include('admin.common.notifications')

            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a  onclick="window.open('{{ url('/admin/lucky_draw/print') }}', 'newwindow', 'width=750, height=500'); return false;" href="" class="btn btn-primary btn-sm">{{trans('localize.print_qr')}}</a>
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false" style="color:#fff">{{trans('localize.print_dummy')}}</a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a  onclick="window.open('{{ url('/admin/lucky_draw/dummy?total=10') }}', 'newwindow', 'width=750, height=500'); return false;" href="#">10 QR Code</a></li>
                            <li><a  onclick="window.open('{{ url('/admin/lucky_draw/dummy?total=30') }}', 'newwindow', 'width=750, height=500'); return false;" href="#">30 QR Code</a></li>
                            <li><a  onclick="window.open('{{ url('/admin/lucky_draw/dummy?total=50') }}', 'newwindow', 'width=750, height=500'); return false;" href="#">50 QR Code</a></li>
                            <li><a  onclick="window.open('{{ url('/admin/lucky_draw/dummy?total=100') }}', 'newwindow', 'width=750, height=500'); return false;" href="#">100 QR Code</a></li>
                            <li><a  onclick="window.open('{{ url('/admin/lucky_draw/dummy?total=500') }}', 'newwindow', 'width=750, height=500'); return false;" href="#">500 QR Code</a></li>
                        </ul>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">#ID</th>
                                    <th class="text-center">{{trans('localize.type')}}</th>
                                    <th class="text-center">{{trans('localize.value')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.description')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.email')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.phone')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.claim_date')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.redemption')}}</th>
                                </tr>
                            </thead>
                            @if ($luckydraws->total())
                                <tbody>
                                    @foreach ($luckydraws as $key => $ld)
                                    <tr class="text-center">
                                        <td>{{ $ld->id }}</td>
                                        <td>{{ ($ld->type == 1) ? trans('localize.normal_reward') : trans('common.credit_name') }}</td>
                                        <td>{{ $ld->value }}</td>
                                        <td>{{ $ld->desc }}</td>
                                        @if ($ld->claim_by > 0)
                                        <td>{{ ($ld->email) ? $ld->email : '-' }}</td>
                                        <td>{{ ($ld->phone_area_code) ? $ld->phone_area_code . $ld->cus_phone : '-' }}</td>
                                        <td>{{ \Helper::UTCtoTZ($ld->claim_date) }}</td>
                                        <td>
                                            @if ($ld->status != 1)
                                            <button type="button" class="btn btn-primary btn-sm button_redeemed" data-id="{{ $ld->id  }}" data-post="data-php">@lang('localize.redeemed')</button>
                                            @else
                                            {{ \Helper::UTCtoTZ($ld->redemption_date) }}
                                            @endif
                                        </td>
                                        @else
                                        <td colspan=4>@lang('localize.unclaimed')</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    {{trans('localize.Showing')}} {{$luckydraws->firstItem()}} {{trans('localize.to')}} {{$luckydraws->lastItem()}} {{trans('localize.of')}} {{$luckydraws->Total()}} {{trans('localize.Records')}}
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$luckydraws->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    {{trans('localize.Go_To_Page')}}
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$luckydraws->lastPage()}}">
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
                                    <td colspan="10" class="text-center">@lang('localize.nodata')</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection @section('style')
    <link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
    <script src="/backend/js/plugins/footable/footable.all.min.js"></script>

<script>
    $(document).ready(function() {
        $('.button_redeemed').on('click', function(){
            var this_id = $(this).attr('data-id');
            swal({
                title: "{{trans('localize.sure')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{trans('localize.yes')}}",
                cancelButtonText: "{{trans('localize.cancel')}}",
                closeOnConfirm: false
            }, function(){
                var url = '/admin/lucky_draw/redeem/' + this_id;
                window.location.href = url;
            });
        });

        $("#pageno").change(function() {
            var input = $(this).val();
            var max = {{$luckydraws->lastPage()}};
            if(input > max){
                $(this).val(max);
            }
        });
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