@extends('admin.layouts.master')

@section('title', 'Auction Bidders')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>View Auction Bidders</h2>
        <ol class="breadcrumb">
            <li>
                Transaction
            </li>
            <li>
                Auction
            </li>
            <li class="active">
                <strong>View Auction Bidders</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox float-e-margins border-bottom">
        <div class="ibox-title ibox-title-filter">
            <h5>Search Filter</h5>
            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-down"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content ibox-content-filter" style="display:none;">
            <div class="row">
                <form class="form-horizontal" id="filter" action='/admin/transaction/auction/view' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">ID</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="Search By ID/Auction ID" class="form-control" id="id" name="id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Search</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['search']}}" placeholder="Search by Auction/Customer Name" class="form-control" id="search" name="search">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sort By</label>
                        <div class="col-sm-9">
                             <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                <option value="new" {{($input['sort'] == "" || $input['sort'] == 'new') ? 'selected' : ''}}>{{trans('localize.newest')}}</option>
                                <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>{{trans('localize.oldest')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Type</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="type" name="type" style="font-family:'FontAwesome', sans-serif;">
                                <option value="" {{($input['type'] == "" ) ? 'selected' : ''}}>All</option>
                                <option value="client" {{($input['type'] == 'client') ? 'selected' : ''}}>Client</option>
                                <option value="bot" {{($input['type'] == 'bot') ? 'selected' : ''}}>Bot</option>
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
            @include('admin.common.errors')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="footable table table-stripped table-bordered">
                        <thead>
                            <tr>
                                <th nowrap class="text-center">#ID</th>
                                <th class="text-center">Auction</th>
                                <th nowrap class="text-center">Customer Name</th>
                                <th class="text-center">Customer Email</th>
                                <th class="text-center">Shipping Amount</th>
                                <th class="text-center">Bidding Amount</th>
                                <th class="text-center">Total Amount</th>
                            </tr>
                        </thead>
                            <tbody>
                            <?php $i = 1; ?>
                                @foreach($manage_auction_bidder as $bidder)
                                <tr class="text-center">
                                    <td>{{$bidder->oa_id}}</td>
                                    <td class="text-left"><a href="/admin/auction/view/{{ $bidder->auc_id }}" class="nolinkcolor" data-toggle="tooltip" title="View auction details">{{$bidder->auc_id}} - {{$bidder->auc_title }}</a></td>
                                    <td>{{$bidder->oa_cus_name }}</td>
                                    <td>{{$bidder->oa_cus_email }}</td>
                                    <td>{{$bidder->oa_bid_shipping_amt }}</td>
                                    <td>{{$bidder->oa_bid_amt }}</td>
                                    <td>{{$bidder->oa_bid_shipping_amt +$bidder->oa_bid_amt }}</td>
                                </tr>
                                <?php $i++; ?>
                                @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="7"><ul class="pull-right">{{ $manage_auction_bidder->appends(\Request::except('page'))->links() }}</ul></td>
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
