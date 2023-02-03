@extends('admin.layouts.master')

@section('title', 'Archive Auction')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Archive Auction</h2>
        <ol class="breadcrumb">
            <li>
                Auction
            </li>
            <li class="active">
                <strong>Archive</strong>
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
                <form class="form-horizontal" id="filter" action='/admin/auction/archive' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Search</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['search']}}" placeholder="Search in table" class="form-control" id="search" name="search">
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
                        <div class="col-sm-9 col-sm-offset-2">
                            <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
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
                                    <th class="text-center text-nowrap">Auction ID</th>
                                    <th class="text-nowrap">Auction Title</th>
                                    <th class="text-center">Shop Name</th>
                                    <th class="text-center text-nowrap">Auction Price</th>
                                    <th class="text-center text-nowrap">Bid Increment ($)</th>
                                    <th class="text-center text-nowrap">Auction Image</th>
                                    <th class="text-center text-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($auctions as $key => $auction)
                                    <tr class="text-center">
                                        <td><a class="nolinkcolor" href="/admin/auction/view/{{ $auction->auc_id}}" data-toggle="tooltip" title="View auction details">{{ $auction->auc_id}}</a></td>
                                        <td class="text-left">{{ $auction->auc_title_en}}</td>
                                        <td>{{ $auction->stor_name}}</td>
                                        <td>{{ $auction->auc_auction_price}}</td>
                                        <td>{{ $auction->auc_bitinc}}</td>
                                        <?php
                                            $auc_img = $auction->auc_image;

                                            if (empty($auc_img)) {
                                                $image = env('IMAGE_DIR').'/images/product/'.$auc_img;
                                            }
                                        ?>
                                        <td><img style="height:40px;" src="{{ $auc_img }}"></td>
                                        <td class="text-nowrap">
                                            <a href="/admin/auction/view/{{$auction->auc_id}}" class="btn btn-white btn-sm"><span><i class="fa fa-file-text-o"></i> View</span></a>
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
