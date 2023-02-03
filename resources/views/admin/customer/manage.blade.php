@extends('admin.layouts.master')

@section('title', 'Manage Customer')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.manage')}} {{trans('localize.customer')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.customer')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.manage')}}</strong>
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
                <form class="form-horizontal" id="filter" action='/admin/customer/manage' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.customer')}} ID</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.Search_By_Customer_ID')}}" class="form-control number" id="id" name="id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['search']}}" placeholder="{{trans('localize.Search_by_Customer_Name')}}" class="form-control" id="search" name="search">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.phone')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['phone']}}" placeholder="Search by Customer Phone" class="form-control number" id="phone" name="phone">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.email')}}</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['email']}}" placeholder="{{trans('localize.Search_by_Customer_Email')}}" class="form-control" id="email" name="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Status')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="status" name="status">
                                <option value="" {{ ($input['status'] == "") ? 'selected' : '' }}>{{trans('localize.All')}}</option>
                                <option value="1" {{ ($input['status'] == "1") ? 'selected' : '' }}>{{trans('localize.Active')}}</option>
                                <option value="0" {{ ($input['status'] == "0") ? 'selected' : '' }}>{{trans('localize.Blocked')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.sort')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                <option value="id_asc" {{ ($input['sort'] == "id_desc") ? 'selected' : '' }}> #ID : &#xf162;</option>
                                <option value="id_desc" {{ ($input['sort'] == "" || $input['sort'] == "id_desc") ? 'selected' : '' }}>#ID : &#xf163;</option>
                                <option value="new" {{($input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.Newest')}}</option>
                                <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.Oldest')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">{{trans('localize.Login_Type')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="type" name="type">
                                <option value="" {{ ($input['type'] == "") ? 'selected' : '' }}>{{trans('localize.All')}}</option>
                                <option value="1" {{ ($input['type'] == "1") ? 'selected' : '' }}>{{trans('localize.admin')}}</option>
                                <option value="2" {{ ($input['type'] == "2") ? 'selected' : '' }}>{{trans('localize.Website_User')}}</option>
                                <option value="3" {{ ($input['type'] == "3") ? 'selected' : '' }}>{{trans('localize.Facebook_User')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('localize.country')</label>
                        <div class="col-sm-9">
                            <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="countries[]" value="0" {{ (isset($input['countries']) && (in_array("0", $input['countries']))? 'checked' : '' ) }}>&nbsp; No Country</label>&nbsp;
                            @foreach($countries as $country)
                                <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="countries[]" value="{{ $country->co_id }}" {{ (isset($input['countries']) && (in_array($country->co_id, $input['countries']))? 'checked' : '' ) }}>&nbsp; {{ $country->co_name }}</label>
                            @endforeach
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
            @include('admin.common.status')
            @include('admin.common.error')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">#ID</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Name')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.phone')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.email')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Joined_Date')}}</th>
                                    <th class="text-center text-nowrap">@lang('common.credit_name')</th>
                                    {{-- <th class="text-center text-nowrap">Game Point</th> --}}
                                    <th class="text-center text-nowrap">{{trans('localize.Action')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Status')}}</th>
                                    <th class="text-center text-nowrap">{{trans('localize.Login_Type')}}</th>
                                </tr>
                            </thead>
                            @if ($customers->total())
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($customers as $customer)
                                    <tr class="text-center">
                                        <td>{{$customer->cus_id}}</td>
                                        <td>{{$customer->cus_name}}</td>
                                        <td>{{$customer->phone_area_code . $customer->cus_phone}}</td>
                                        <td>{{$customer->email}}</td>
                                        <td>{{ (empty($customer->cus_joindate)) ? \Helper::UTCtoTZ($customer->created_at) : \Helper::UTCtoTZ($customer->cus_joindate) }}</td>
                                        <td>
                                            <a class="btn btn-white btn-block btn-sm nolinkcolor" href="/admin/customer/credit/{{$customer->cus_id}}">
                                            <div class="row">
                                                @foreach ($customer->customer_wallets as $key => $cusWallet)
                                                    @if ($cusWallet->wallet->percentage == 0)
                                                        @if ($cusWallet->log_id)
                                                            <div class="col-sm-6">
                                                                <b>{{ ucfirst($cusWallet->wallet->name) }}</b>
                                                                <br/>{{ $cusWallet->credit }}
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="col-sm-6">
                                                            <b>{{ ucfirst($cusWallet->wallet->name) }}</b>
                                                            <br/>{{ $cusWallet->credit }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                                @if ($customer->special_wallet > 0)
                                                    <div class="{{ (count($customer->customer_wallets)%2 == 0) ? 'col-sm-12' : 'col-sm-6'}}">
                                                        <b>Hemma</b><br/>
                                                        {{ $customer->special_wallet }}
                                                    </div>
                                                @endif
                                            </div>
                                            </a>
                                            {{--  <a class="btn btn-white btn-block btn-sm nolinkcolor" href="/admin/customer/credit/{{$customer->cus_id}}">{{ ($customer->v_token)? $customer->v_token : '0.00' }}</a>  --}}
                                        </td>

                                        <td class="text-nowrap">
                                            <p>
                                                @if(in_array('customermanageedit',$admin_permission))
                                                <a class="btn btn-white btn-sm" href="/admin/customer/edit/{{$customer->cus_id}}"><span><i class="fa fa-edit"></i> {{trans('localize.Edit')}}</span></a>
                                                @endif

                                                <a class="btn btn-white btn-sm" href="/admin/customer/view/{{$customer->cus_id}}"><span><i class="fa fa-file-text-o"></i> {{trans('localize.view')}}</span></a>
                                            </p>
                                            <p>
                                                <a class="btn btn-white btn-block btn-sm text-primary" href="{{ url('admin/customer/limit', [$customer->cus_id]) }}"><span><i class="fa fa-bar-chart-o"></i> {{trans('localize.limit')}}</span></a>
                                            </p>
                                            @if(in_array('customermanagesetstatus',$admin_permission))
                                            <p>
                                                @if ($customer->cus_status == 1)
                                                    {{-- <a href="/admin/customer/block/{{$customer->cus_id}}/blocked"><i class='fa fa-check fa-2x'></i></a> --}}
                                                    <a style="width:100%" class="btn btn-white btn-sm text-warning" href="/admin/customer/block/{{$customer->cus_id}}/blocked"><span><i class="fa fa-refresh"></i>  {{trans('localize.Block_Customer')}}</span></a>
                                                @else
                                                    {{-- <a href="/admin/customer/block/{{$customer->cus_id}}/unblocked"><i class='fa fa-ban fa-2x'></i></a> --}}
                                                    <a style="width:100%" class="btn btn-white btn-sm text-navy" href="/admin/customer/block/{{$customer->cus_id}}/unblocked"><span><i class="fa fa-refresh"></i>  {{trans('localize.set_to_active')}}</span></a>
                                                @endif
                                            </p>
                                            @endif
                                        </td>

                                        @if ($customer->cus_status == 1)
                                            <td class="text-nowrap text-navy"><i class='fa fa-check'></i> {{trans('localize.Active')}}</td>
                                        @else
                                            <td class="text-nowrap text-danger"><i class='fa fa-ban'></i> {{trans('localize.Blocked')}}</td>
                                        @endif

                                        <td>
                                            @if ($customer->cus_logintype==1)
												{{trans('localize.Admin_User')}}
                                            @elseif ($customer->cus_logintype==2)
                                                {{trans('localize.Website_User')}}
                                            @elseif ($customer->cus_logintype==3)
                                                {{trans('localize.Facebook_User')}}
                                            @endif
                                        </td>

                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    Showing {{$customers->firstItem()}} to {{$customers->lastItem()}} of {{$customers->Total()}} Records
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$customers->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    Go To Page
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$customers->lastPage()}}">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                        <i class="fa fa-share-square-o"></i> Go
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
<script src="/backend/js/custom.js"></script>
<script>
    $(document).ready(function() {
        $('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$customers->lastPage()}};
            if(input > max){
                $(this).val(max);
            }
        });

        $('button').on('click', function(){
            var this_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');

            if (this_action == 'vcoinlog') {
                vcoinlog(this_id);
            }
            if (this_action == 'gplog') {
                gplog(this_id);
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
