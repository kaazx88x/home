@extends('admin.layouts.master')

@section('title', 'Product Shipping')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Shipping and Delivery</h2>
        <ol class="breadcrumb">
            <li>
                Product
            </li>
            <li class="active">
                <strong>Shipping</strong>
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
                <form class="form-horizontal" id="filter" action='/admin/product/shipping' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Transaction ID</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="Search By Transaction ID" class="form-control" id="id" name="id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Product Name</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['name']}}" placeholder="Search By Product Name" class="form-control" id="name" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="status" name="status">
                                @foreach ($status_list as $key => $stat)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['status']) ? 'selected' : '' }}>{{ $stat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sort By</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                <option value="name_asc" {{ ($input['sort'] == "name_asc") ? 'selected' : '' }}>Product Name : &#xf15d;</option>
                                <option value="name_desc" {{ ($input['sort'] == "name_desc") ? 'selected' : '' }}>Product Name : &#xf15e;</option>
                                <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>Newest</option>
                                <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>Oldest</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-2">
                            <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">Search</button>
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
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">Transaction ID</th>
                                    <th>Product</th>
                                    <th>Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center text-nowrap">@lang('common.credit_name')</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?> @foreach ($shippings as $key => $shipping)
                                <tr class="text-center">
                                    <td>{{$shipping->transaction_id}}</td>
                                    <td class="text-left">
                                        <a class="nolinkcolor" href="/admin/product/view/{{$shipping->pro_mr_id}}/{{$shipping->pro_id}}" data-toggle="tooltip" title="View product details">{{$shipping->pro_id}} - {{$shipping->pro_title_en}}</a>
                                    </td>
                                    <td class="text-left">
                                        <a class="nolinkcolor" href="/admin/customer/view/{{$shipping->cus_id}}" data-toggle="tooltip" title="View customer details">{{$shipping->cus_id}} - {{$shipping->cus_name}}</a>
                                    </td>
                                    <td>{{$shipping->email}}</td>
                                    <td>{{$shipping->order_qty}}</td>
                                    <td>{{$shipping->order_vtokens}}</td>
                                    {{-- <td>{{ Carbon\Carbon::createFromTimestamp(strtotime($shipping->order_date))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                                    <td>{{ \Helper::UTCtoTZ($shipping->order_date) }}</td>
                                    @if ($shipping->order_status == 3)
                                        <td class="text-center text-primary">Shipped</td>
                                    @elseif ($shipping->order_status == 4)
                                        <td class="text-center text-navy">Completed</td>
                                    @elseif ($shipping->order_status == 5)
                                        <td class="text-center text-danger">Canceled</td>
                                    @endif
                                    <td class="text-center">
                                        <button type="button" class="btn btn-white btn-sm" data-toggle="modal" data-id="{{ $shipping->order_id }}" data-post="data-php" data-action="details"><i class="fa fa-file-text-o"></i> View Order</button>
                                    </td>
                                </tr>
                                <?php $i++; ?> @endforeach
                            </tbody>
                            {{-- <tfoot>
                                <tr>
                                    <td colspan="10">
                                        <ul class="pagination pull-right">{{$shippings->appends(Request::except('page'))->links()}}</ul>
                                    </td>
                                </tr>
                            </tfoot> --}}
                            <tfoot>
                                <tr>
                                    <td colspan="10">
                                        <div class="row">
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
                                        </div>
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
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/custom.js"></script>

<script>
    $(document).ready(function() {

        $('#orderStatus').change(function () {
                var href = window.location.href.substring(0, window.location.href.indexOf('?'));
                var url = 's='+$('#orderStatus').val();

                window.location.replace(href + '?' + url);
        });

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
</script> @endsection