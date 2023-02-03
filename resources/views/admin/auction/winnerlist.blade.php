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
                <form class="form-horizontal" id="filter" action='/admin/auction/winner' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Auction ID</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="Search By Auction ID" class="form-control" id="id" name="id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Search</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['search']}}" placeholder="Search in table" class="form-control" id="search" name="search">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Type</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="type" name="type" style="font-family:'FontAwesome', sans-serif;">
                                <option value="" {{ ($input['type'] == "") ? 'selected' : '' }}>All</option>
                                <option value="1" {{ ($input['type'] == "1") ? 'selected' : '' }}>Client</option>
                                <option value="2" {{ ($input['type'] == "2") ? 'selected' : '' }}>Bot</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sort By</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
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
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center text-nowrap">Auction Item</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center text-nowrap">Customer Email</th>
                                    <th class="text-center text-nowrap">Customer GP</th>
                                    <th class="text-center text-nowrap">Target Customer GP</th>
                                    <th class="text-center text-nowrap">Bot GP</th>
                                    <th class="text-center text-nowrap">Target Bot GP</th>
                                    <th class="text-center text-nowrap">Bid Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($auctions as $key => $auction)
                                    <tr class="text-center">
                                        <td class="text-left">
                                            <a class="nolinkcolor" href="/admin/auction/view/{{ $auction->auc_id}}" data-toggle="tooltip" title="View auction details">{{ $auction->auc_id}} - {{ $auction->auc_title_en}}</a>
                                        </td>
                                        <td>
                                            @if($auction->oa_cus_id > 0)
                                            <p><a href="/admin/customer/view/{{ $auction->oa_cus_id}}" class="nolinkcolor" data-toggle="tooltip" title="View customer details">{{ $auction->oa_cus_id}} - {{ $auction->oa_cus_name}}</a></p>
                                            <p>[Client]</p>
                                            @else
                                            <p>{{ $auction->oa_cus_name}}</p>
                                            <p>[Bot]</p>
                                            @endif

                                        </td>
                                        <td>{{ $auction->oa_cus_email}}</td>
                                        <td>{{ $auction->auc_total_cus_bid }}</td>
                                        <td>{{ $auction->auc_total_bids }}</td>
                                        <td>{{ $auction->total_autobot_bid }}</td>
                                        <td>{{ $auction->auc_bot_target }}</td>
                                        <td>{{ date('d F Y H:i:s', strtotime($auction->oa_bid_date)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9">
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
