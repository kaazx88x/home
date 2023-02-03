@extends('admin.layouts.master')

@section('title', 'Deals COD')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Product COD</h2>
        {{-- <ol class="breadcrumb">
            <li>
                Transaction
            </li>
            <li class="active">
                <strong>Product Transactions</strong>
            </li>
        </ol> --}}
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            @include('admin.common.success')
            @include('admin.common.errors')
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <div class="row">
                            <div class="col-md-4 pull-right">
                                <select class="form-control" id="status">
                                    @foreach ($status_list as $key => $stat)
                                        @if ($key == $status)
                                            <option value="{{ $key }}" selected>{{ $stat }}</option>
                                        @else
                                            <option value="{{ $key }}">{{ $stat }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-4 pull-right">
                            <div class="input-group">
                                <input type="text" class="form-control" id="filter" placeholder="Search in table">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" style="height:34px;" type="button">Search!</button>
                                </span>
                            </div><!-- /input-group -->
                        </div>
                    </div>
                    <br>
                    <table class="footable table table-stripped" data-page-size="20" data-filter=#filter>
                      <thead>
                          <tr>
                              <th data-sort-ignore="true" class="text-center">#</th>
                              <th data-sort-ignore="true" class="text-center">Customer</th>
                              <th data-sort-ignore="true" class="text-center">Product Title</th>
                              <th data-sort-ignore="true" class="text-center">Amount (S/)</th>
                              <th data-sort-ignore="true" class="text-center">Tax (S/)</th>
                              <th data-sort-ignore="true" class="text-center">Status</th>
                              <th data-sort-ignore="true" class="text-center">Transaction Date</th>
                              <th data-sort-ignore="true" class="text-center">Transaction Type</th>
                              <th data-sort-ignore="true" class="text-center">Update Status</th>
                          </tr>
                      </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            @foreach ($coddetails as $cod)
                            <?php
                                $orderstatus = "";
                                if($cod->cod_paytype==1)
                                {
                                    $ordertype="Paypal";
                                }
                                else
                                {
                                    $ordertype="COD";
                                }

                                if($cod->cod_status==1)
                                {
                                    $orderstatus="success";
                                }
                                else if($cod->cod_status==2)
                                {
                                    $orderstatus="completed";
                                }
                                else if($cod->cod_status==3)
                                {
                                    $orderstatus="Hold";
                                }
                                else if($cod->cod_status==4)
                                {
                                    $orderstatus="failed";
                                }
                            ?>
                            <tr class="text-center">
                              <td>{{ $i }}</td>
                              <td>{{ $cod->cus_name }}</td>
                              <td>{{ $cod->pro_title_en }}</td>
                              <td>{{ $cod->cod_amt }}</td>
                              <td>{{ $cod->cod_tax }}</td>
                              <td>{{ $orderstatus }}</td>
                              <td>{{ $cod->cod_date }}</td>
                              <td>{{ $ordertype }}</td>
                              <td>
                                  <select name="" class="btn btn-primary btn-sm" onchange="update_order_cod(this.value,{{ $cod->cod_id }})">
                                        <option value="1" {{ ($cod->cod_status == 1)? 'selected' : '' }} >Success</option>
                                        <option value="2" {{ ($cod->cod_status == 2)? 'selected' : '' }} >Completed</option>
                                        <option value="3" {{ ($cod->cod_status == 3)? 'selected' : '' }} >Hold</option>
                                        <option value="4" {{ ($cod->cod_status == 4)? 'selected' : '' }} >Failed</option>
                                    </select>
                              </td>
                              {{-- <td class="text-center">
                                  <p><button type="button" class="btn btn-info btn-xs" data-toggle="modal"  data-id="{{ $allorders_list->order_id  }}" data-post="data-php" data-action="details">View Order Details</button></p>

                                  @if ($allorders_list->order_status == 1)
                                      <p><button type="button" class="btn btn-warning btn-xs" data-toggle="modal"  data-id="{{ $allorders_list->order_id  }}" data-post="data-php" data-action="accept">Accept Order</button></p>
                                      <!--<a href="/merchant_accept_orders/{{ $allorders_list->order_id }}/2" class="btn btn-success btn-xs">Accept</a>-->
                                  @elseif ($allorders_list->order_status == 2)
                                      <p><button type="button" class="btn btn-primary btn-xs" data-toggle="modal"  data-id="{{ $allorders_list->order_id  }}" data-post="data-php" data-action="shipment">Update Shipment Info</button></p>
                                  @elseif ($allorders_list->order_status == 3)
                                      <p><button type="button" class="btn btn-success btn-xs" data-toggle="modal"  data-id="{{ $allorders_list->order_id  }}" data-post="data-php" data-action="shipdetails">View Shipment Info</button></p>
                                  @elseif ($allorders_list->order_status == 4)
                                      <p><span class="label label-success" style="font-size:13px;">Complete</span></p>
                                  @elseif ($allorders_list->order_status == 5)
                                      <p><span class="label label-danger" style="font-size:13px;">Canceled</span></p>
                                  @endif
                              </td> --}}
                            </tr>
                            <?php $i++; ?>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="9"><ul class="pagination pull-right"></ul></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content load_modal">
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script>
    $(document).ready(function() {

        $('#status').change(function () {
                var href = window.location.href.substring(0, window.location.href.indexOf('?'));
                if ($('#status').val() == '') {
                    window.location.replace(href);
                }else {
                    var url = 's='+$('#status').val();
                    window.location.replace(href + '?' + url);
                }
        });

        $('.footable').footable();
    });

    function update_order_cod(id,orderid)
	{
        var passdata= 'status='+id+"&order_id="+orderid;
            $.ajax( {
                type: 'get',
                data: passdata,
                url: '{{ url('update_order_cod') }}',
                success: function(responseText){
            if(responseText=="success")
                {
                    window.location=window.location.origin+"/admin/transaction/product/cod?s="+id;
                //$('#Product_MainCategory').html(responseText);
                }
            }
        });
	}
</script>
@endsection
