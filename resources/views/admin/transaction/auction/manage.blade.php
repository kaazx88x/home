@extends('admin.layouts.master')

@section('title', 'Manage Auction')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Manage Auction Bidders</h2>
        <ol class="breadcrumb">
            <li>
                Transaction
            </li>
            <li>
                Auction
            </li>
            <li class="active">
                <strong>Manage Auction Bidders</strong>
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
                <form class="form-horizontal" id="filter" action='/admin/transaction/auction/manage' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Auction ID</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="Search By Auction ID" class="form-control" id="id" name="id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Auction Name</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['search']}}" placeholder="Search by Auction Name" class="form-control" id="search" name="search">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sort By</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                <option value="new" {{($input['sort'] == 'new') ? 'selected' : ''}}>Newest</option>
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
            @include('admin.common.errors')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped">
                        <thead>
                            <tr>
                                <th nowrap class="text-center">Auction</th>
                                <th nowrap class="text-center">Customer GP</th>
                                <th nowrap class="text-center">Target Customer GP</th>
                                <th nowrap class="text-center">Bot GP</th>
                                <th nowrap class="text-center">Target Bot GP</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                            <tbody>
                                @foreach($manage_auction_bidder as $bidder)
                                <tr class="text-center">
                                    <td class="text-left"><a class="nolinkcolor" href="/admin/auction/view/{{ $bidder->auc_id }}" data-toggle="tooltip" title="View auction details">{{ $bidder->auc_id }} - {{ $bidder->auc_title_en }}</a></td>
                                    <td>{{ $bidder->auc_total_cus_bid }}</td>
                                    <td>{{ $bidder->auc_total_bids }}</td>
                                    <td>{{ $bidder->total_autobot_bid }}</td>
                                    <td>{{ $bidder->auc_bot_target }}</td>
                                    <td class="text-nowrap">
                                        <p class="text-center"><a style="width:100%" href="{{ url('auctions/detail/'.$bidder->auc_id) }}" class="btn btn-xs btn-info" target="_blank" >Preview Auction</a></p>
                                        @if($bidder->auc_total_cus_bid != 0 && $bidder->total_autobot_bid != 0)
                                            <p class="text-center"><a style="width:100%" href="{{ url('admin/transaction/auction/bidder/'.$bidder->auc_id) }}" class="btn btn-xs btn-success">List Of Bidders</a></p>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="6"><ul class="pull-right">{{ $manage_auction_bidder->appends(\Request::except('page'))->links() }}</ul></td>
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
<script>
    $(document).ready(function() {


    });
</script>
@endsection
