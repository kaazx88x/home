@extends('admin.layouts.master')
@section('title', 'Auction Winners')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Auction Winners</h2>
        <ol class="breadcrumb">
            <li>
                Auction
            </li>
            <li class="active">
                <strong>Winners</strong>
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            <div class="ibox-content m-b-sm border-bottom">
                <div class="row">
                    <form id="filter" action='/admin/auction/winner' method="GET">
                        {{-- <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" value="{{$input['search']}}" placeholder="Search.." class="form-control" id="search" name="search">
                            </div>
                        </div> --}}
                        <div class="col-sm-2 col-sm-offset-6">
                            <div class="form-group">
                                <select class="form-control" id="type" name="type" style="font-family:'FontAwesome', sans-serif;">
                                    <option value="" {{ ($input['type'] == "") ? 'selected' : '' }}>All</option>
                                    <option value="1" {{ ($input['type'] == "1") ? 'selected' : '' }}>Client</option>
                                    <option value="2" {{ ($input['type'] == "2") ? 'selected' : '' }}>Bot</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
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
                @include('admin.common.status')
                @include('admin.common.success')
                @include('admin.common.errors')
                @include('admin.common.error')
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-nowrap">Auction ID</th>
                                            <th class="text-nowrap">Auction Title</th>
                                            <th class="text-center">Customer Name</th>
                                            <th class="text-center text-nowrap">Customer Email</th>
                                            <th class="text-center text-nowrap">Customer Address</th>
                                            <th class="text-center text-nowrap">Bidding Amount</th>
                                            <th class="text-center text-nowrap">Status</th>
                                            <th class="text-center text-nowrap">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($auctions as $key => $auction)
                                            <tr class="text-center">
                                                <td>{{ $auction->auc_id}}</td>
                                                <td class="text-left">{{ $auction->auc_title_en}}</td>
                                                <td>{{ $auction->oa_cus_name}}</td>
                                                <td>{{ $auction->oa_cus_email}}</td>
                                                <td>{{ $auction->oa_cus_address }}</td>
                                                <td>{{ $auction->oa_bid_amt }}</td>
                                                <td>
                                                @if ($auction->oa_bid_item_status == 1)
                                                    On Process
                                                @elseif ($auction->oa_bid_item_status == 2)
                                                    Send
                                                @elseif ($auction->oa_bid_item_status == 3)
                                                    Cancel
                                                @else
                                                    Checking
                                                @endif
                                                </td>
                                                <td>
                                                    @if ($auction->oa_bid_item_status == 2)
                                                        <a href="" class="btn btn-block btn-sm btn-danger">Cancel</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="10">
                                                <ul class="pagination pull-right">{{$auctions->appends(Request::except('page'))->links()}}</ul>
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
    </div>
</div>

@endsection @section('style')
    <link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
    <script src="/backend/js/plugins/footable/footable.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // $('.footable').footable(); --}}
        });
    </script>
@endsection
