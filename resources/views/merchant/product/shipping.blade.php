@extends('merchant.layouts.master')

@section('title', 'Product Shipping')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{trans('localize.shipping_n_delivery')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url( $route . '/product/manage') }}">{{trans('localize.manage_products')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.shipping_n_delivery')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <form id="filter" action="{{ url('merchant/product/shipping') }}" method="GET">
                <div class="col-sm-2">
                    <div class="form-group">
                        <input type="text" value="{{$input['id']}}" placeholder="{{trans('localize.transaction_id')}}" class="form-control" id="id" name="id">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" value="{{$input['name']}}" placeholder="{{trans('localize.productName')}}" class="form-control" id="name" name="name">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="status" name="status">
                            @foreach ($status_list as $key => $stat)
                                @if ($key == $input['status'])
                                    <option value="{{ $key }}" selected>{{ $stat }}</option>
                                @else
                                    <option value="{{ $key }}">{{ $stat }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                            <option value="name_asc" {{ ($input['sort'] == "name_asc") ? 'selected' : '' }}>{{trans('localize.productName')}} : &#xf15d;</option>
                            <option value="name_desc" {{ ($input['sort'] == "name_desc") ? 'selected' : '' }}>{{trans('localize.productName')}} : &#xf15e;</option>
                            <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.newest')}}</option>
                            <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.oldest')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('merchant.common.success')
            @include('merchant.common.error')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">{{trans('localize.transaction_id')}}</th>
                                    <th>{{trans('localize.productName')}}</th>
                                    <th>{{trans('localize.name')}}</th>
                                    <th class="text-center">{{trans('localize.email')}}</th>
                                    <th class="text-center">{{trans('localize.quantity')}}</th>
                                    <th class="text-center text-nowrap">@lang('common.credit_name')</th>
                                    <th class="text-center">{{trans('localize.date')}}</th>
                                    <th class="text-center">{{trans('localize.status')}}</th>
                                    <th class="text-center">{{trans('localize.action')}}</th>
                                </tr>
                            </thead>
                            @if ($shippings->total())
                                <tbody>
                                    <?php $i = 1; ?> @foreach ($shippings as $key => $shipping)
                                    <tr class="text-center">
                                        <td>{{$shipping->transaction_id}}</td>
                                        <td class="text-left">{{$shipping->pro_title_en}}</td>
                                        <td class="text-left">{{$shipping->cus_name}} </td>
                                        <td>{{$shipping->email}} </td>
                                        <td>{{$shipping->order_qty}}</td>
                                        <td>{{$shipping->order_vtokens}}</td>
                                        {{-- <td>{{ Carbon\Carbon::createFromTimestamp(strtotime($shipping->order_date))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                        <td>{{ \Helper::UTCtoTZ($shipping->order_date) }}</td>
                                        @if ($shipping->order_status == 3)
                                            <td class="text-center text-primary">{{trans('localize.shipped')}}</td>
                                        @elseif ($shipping->order_status == 4)
                                            <td class="text-center text-navy">{{trans('localize.completed')}}</td>
                                        @elseif ($shipping->order_status == 5)
                                            <td class="text-center text-danger">{{trans('localize.cancelled')}}</td>
                                        @endif
                                        <td class="text-center">
                                            <button type="button" class="btn btn-white btn-sm" data-toggle="modal" data-id="{{ $shipping->order_id }}" data-post="data-php" data-action="details"><i class="fa fa-file-text-o"></i> {{trans('localize.view_order')}}</button>
                                        </td>
                                    </tr>
                                    <?php $i++; ?> @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <div class=" col-xs-6">
                                                <span class="pagination">
                                                    Showing {{$shippings->firstItem()}} to {{$shippings->lastItem()}} of {{$shippings->Total()}} Records
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <div class="col-xs-7 text-right">
                                                    {{$shippings->appends(Request::except('page'))->links()}}
                                                </div>
                                                <div class="col-xs-5 text-right pagination">
                                                    Go To Page
                                                    <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$shippings->lastPage()}}">
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
                                    <td colspan="9" class="text-center">@lang('localize.nodata')</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content load_modal">
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection

@section('script')
<script src="/backend/js/custom.js"></script>

<script>
    $(document).ready(function() {

        $('button').on('click', function(){
            var this_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');
            if (this_action == 'details') {
                 view_order(this_id);
            }
        });

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$shippings->lastPage()}};
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
