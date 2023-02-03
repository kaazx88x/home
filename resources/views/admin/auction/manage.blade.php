@extends('admin.layouts.master')

@section('title', 'Manage Product')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Manage Auction</h2>
        <ol class="breadcrumb">
            <li>
                Auction
            </li>
            <li class="active">
                <strong>Manage Auction</strong>
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
                <form class="form-horizontal" id="filter" action='/admin/auction/manage' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Search</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['search']}}" placeholder="Search in table" class="form-control" id="search" name="search">
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
                                    <th class="text-center text-nowrap">Auction Item</th>
                                    <th class="text-center">Customer GP</th>
                                    <th class="text-center text-nowrap">Target Customer GP</th>
                                    <th class="text-center text-nowrap">Bot GP</th>
                                    <th class="text-center text-nowrap">Target Bot GP</th>
                                    <th class="text-center text-nowrap">Status</th>
                                    <th class="text-center text-nowrap">Auction Image</th>
                                    <th class="text-center text-nowrap">Auctions</th>
                                    <th class="text-center text-nowrap">Preview</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($auctions as $key => $auction)
                                    <tr class="text-center">
                                        <td class="text-left"><a class="nolinkcolor" href="/admin/auction/view/{{ $auction->auc_id}}" data-toggle="tooltip" title="View auction details">{{ $auction->auc_id}} - {{ $auction->auc_title}}</a></td>
                                        <td>{{ $auction->auc_total_cus_bid}}</td>
                                        <td>{{ $auction->auc_total_bids}}</td>
                                        <td>{{ $auction->total_autobot_bid}}</td>
                                        <td>{{ $auction->auc_bot_target}}</td>
                                        @if (($auction->auc_status) == 1)
                                            <td class="text-nowrap text-navy"><i class='fa fa-check'></i> Active</td>
                                        @else
                                            <td class="text-nowrap text-warning"><i class='fa fa-ban'></i> Inactive</td>
                                        @endif</td>
                                        <?php
                                            $auc_img = $auction->auc_image;

                                            if (empty($auc_img)) {
                                                $image = env('IMAGE_DIR').'/images/product/'.$auc_img;
                                            }
                                        ?>
                                        <td><img style="height:40px;" src="{{ $auc_img }}"></td>
                                        <td class="text-nowrap">
                                            <p>
                                                <a class="btn btn-white btn-sm" href="/admin/auction/edit/{{$auction->auc_id}}"><span><i class="fa fa-edit"></i> Edit</span></a>
                                                <a href="/admin/auction/view/{{$auction->auc_id}}" class="btn btn-white btn-sm"><span><i class="fa fa-file-text-o"></i> View</span></a>
                                            </p>
                                            <p>
                                                @if($auction->auc_status == 1)
                                                    <a style="width:100%" class="btn btn-white btn-sm text-warning" href="/update_auction_status/{{$auction->auc_id}}/blocked"><span><i class="fa fa-refresh"></i>  Set To Inactive</span></a>
                                                @else
                                                    <a style="width:100%" class="btn btn-white btn-sm text-navy" href="/update_auction_status/{{$auction->auc_id}}/unblocked"><span><i class="fa fa-refresh"></i>  Set To Active</span></a>
                                                @endif
                                            </p>
                                        </td>

                                        <td class="text-nowrap">
                                            @if($auction->auc_status == 1)
                                                <a href="/auctions/detail/{{$auction->auc_id}}" class="btn btn-white btn-sm"><span><i class="fa fa-search"></i> Preview</span></a>
                                            @else
                                                <p class="text-danger">Auction Inactive</p>
                                            @endif
                                        </td>
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
