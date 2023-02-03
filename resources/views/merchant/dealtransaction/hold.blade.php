@extends('merchant.layouts.master')

@section('title', 'Edit Merchant')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Deals Hold Orders</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
             @include('merchant.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                      
                    </div>
                </div>
                <div class="ibox-content">
                    <input type="text" class="form-control input-sm m-b-xs" id="filter"
                           placeholder="Search in table">

                    <table class="footable table table-stripped" data-page-size="20" data-filter=#filter>
                      <thead>
                          <tr>
                              <th>SNo</th>
                              <th>Customer</th>
                              <th>Deal Tittle</th>
                              <th>Amount(s)</th>
                              <th>Tax(s)</th>
                              <th>Status</th>
                              <th>Transaction Date</th>
                              <th>Transaction Type</th>
                          </tr>
                      </thead>
                        <tbody>
                          <tr>
                            <td>SNo</td>
                            <td>Customer</td>
                            <td>Deal Tittle</td>
                            <td>Amount(s)</td>
                            <td>Tax(s)</td>
                            <td>Status</td>
                            <td>Transaction Date</td>
                            <td>Transaction Type</td>
                          </tr>
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
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>

<script>
    $(document).ready(function() {

        $('.footable').footable();

    });
</script>
@endsection
