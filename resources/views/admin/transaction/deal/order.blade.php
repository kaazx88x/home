@extends('admin.layouts.master')

@section('title', 'Deals COD')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Deals Orders</h2>
        <ol class="breadcrumb">
            <li>
                Transaction
            </li>
            <li>
                Product
            </li>
            <li class="active">
                <strong>Deals Orders</strong>
            </li>
        </ol>
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
                              <th data-sort-ignore="true" class="text-center">Deal Title</th>
                              <th data-sort-ignore="true" class="text-center">Amount (S/)</th>
                              <th data-sort-ignore="true" class="text-center">Tax (S/)</th>
                              <th data-sort-ignore="true" class="text-center">Status</th>
                              <th data-sort-ignore="true" class="text-center">Transaction Date</th>
                              <th data-sort-ignore="true" class="text-center">Transaction Type</th>
                          </tr>
                      </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            @foreach ($deals as $deal)
                            <?php
                                $orderstatus = "";
                                $ordertype = 'Paypal';
                                if($deal->order_status==1)
                                {
                                    $orderstatus="success";
                                }
                                else if($deal->order_status==2)
                                {
                                    $orderstatus="completed";
                                }
                                else if($deal->order_status==3)
                                {
                                    $orderstatus="Hold";
                                }
                                else if($deal->order_status==4)
                                {
                                    $orderstatus="failed";
                                }
                            ?>
                            <tr class="text-center">
                              <td>{{ $i }}</td>
                              <td>{{ $deal->cus_name }}</td>
                              <td>{{ $deal->deal_title }}</td>
                              <td>{{ $deal->order_amt }}</td>
                              <td>{{ $deal->order_tax }}</td>
                              <td>{{ $orderstatus }}</td>
                              <td>{{ $deal->order_date }}</td>
                              <td>{{ $ordertype }}</td>
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

</script>
@endsection
