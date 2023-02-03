@extends('admin.layouts.master')

@section('title', 'Deals COD')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>List Auction Bidder</h2>
        <ol class="breadcrumb">
            <li>
                Transaction
            </li>
            <li>
                Auction
            </li>
            <li class="active">
                <strong>List Auction Bidder</strong>
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            @include('admin.common.success')
            @include('admin.common.error')
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                         <div class="row">
                             <div class="col-md-6">
                                <span class="pull-left" style="font-size:14px" ><strong>Item :</strong> {{ $auction_title }}</span>
                            </div>
                            {{-- <a href="{{ URL::previous() }}" class="pull-right" style="margin:0 10px 0 0">
                                <button class="btn btn-warning btn-sm btn-grad">Back</button>
                            </a> --}}
                            <div class="col-md-4 pull-right">
                                <select class="form-control" id="bidderType">
                                    <option value="1" {{ ($type == 1) ? 'selected' : '' }}>All</option>
                                    <option value="2" {{ ($type == 2) ? 'selected' : '' }}>Client</option>
                                    <option value="3" {{ ($type == 3) ? 'selected' : '' }}>Bot</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    {{-- <div class="row">
                        <div class="col-md-4 pull-right">
                            <div class="input-group">
                                <input type="text" class="form-control" id="filter" placeholder="Search in table">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" style="height:34px;" type="button">Search!</button>
                                </span>
                            </div><!-- /input-group -->
                        </div>
                    </div> --}}
                    <br>
                    <div class="table-responsive">
                        <table class="table table-stripped table-bordered">
                        <thead>
                            <tr>
                                <th nowrap class="text-center">S.NO</th>
                                <th nowrap class="text-center">Customer Name</th>
                                <th nowrap class="text-center">Customer Email</th>
                                <th nowrap class="text-center">Customer Address</th>
                                <th nowrap class="text-center">Bid Date</th>
                                <th class="text-center">Bidding GP</th>
                                @if($check_winner == 0)
                                <th class="text-center">Action</th>
                                @endif
                                <th nowrap class="text-center">Status</th>
                            </tr>
                        </thead>
                            <tbody>
                            <?php $i = 1; ?>
                                @foreach($manage_auction_bidder as $bidder)
                                <tr class="text-center">
                                    <td>{{ $bidder->oa_id }}</td>
                                    <td class="text-center">
                                        {{ $bidder->oa_cus_name }}
                                        <br/>
                                        <small>{{ ($bidder->oa_cus_id > 0) ? 'CLIENT' : 'BOT' }}</small>
                                    </td>
                                    <td>{{ $bidder->oa_cus_email }}		</td>
                                    <td>{{ $bidder->oa_cus_address }} </td>
                                    <td>{{ $bidder->oa_bid_date }}</td>
                                    <td>{{ $bidder->oa_game_point }}</td>
                                    @if($check_winner == 0)
                                    <td>
                                        <button type="button" class="btn btn-success btn-block btn-sm" id="selectwinner" data-oaid="{{ $bidder->oa_id  }}" data-aucid="{{ $bidder->auc_id  }}"><i class="fa fa-trophy"></i> Select As Winner</button>
                                    </td>
                                    @endif
                                    <td>
                                        @if($check_winner != 0)
                                            @if ($bidder->oa_bid_winner == 1)
                                                <?php $image = env('IMAGE_DIR').'/images/winner.png'; ?>
                                                <p><img src="{{$image}}" height="60" width="50"></p>
                                                <p><span class="text-navy">Auction Won</span></p>
                                                <p><button type="button" class="btn btn-primary btn-block btn-sm" data-toggle="modal" data-oaid="{{ $bidder->oa_id  }}" data-aucid="{{ $bidder->auc_id  }}" data-post="data-php" data-action="send"><i class="fa fa-share "></i> Send Auction</button></p>
                                                <p>Item Status :
                                                @if ($bidder->oa_bid_item_status == 0)
                                                    <span> On Process</span>
                                                @elseif ($bidder->oa_bid_item_status == 1)
                                                    <span class="text-navy">Item Send</span> <br> {{$bidder->oa_delivery_date}}
                                                @elseif ($bidder->oa_bid_item_status == 3)
                                                    <span class="text-danger">Cancelled</span> <br> {{$bidder->oa_delivery_date}}
                                                @endif
                                                </p>
                                            @else
                                                <span class="text-warning">Lose</span>
                                            @endif
                                        @else
                                        <span>In Process to Choose Winner</span>
                                        @endif
                                    </td>
                                </tr>
                                <?php $i++; ?>
                                @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="9"><ul class="pull-right">{{ $manage_auction_bidder->appends(\Request::except('page'))->links() }}</ul></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content load_modal">
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/custom.js"></script>
<script>
    $(document).ready(function() {
        $('#bidderType').change(function () {
            var href = window.location.href.substring(0, window.location.href.indexOf('?'));
            if ($('#bidderType').val() == '') {
                window.location.replace(href);
            }else {
                var url = 's='+$('#bidderType').val();
                window.location.replace(href + '?' + url);
            }
        });

        $('button').on('click', function(e){
                var oa_id = $(this).attr('data-oaid');
                var auc_id = $(this).attr('data-aucid');
                var this_action = $(this).attr('data-action');
                if (this_action == 'send'){
                   send_auction_winner(oa_id);
                }
        });
        $('#selectwinner').on('click', function(e){
            var oa_id = $(this).attr('data-oaid');
            var auc_id = $(this).attr('data-aucid');
            swal({
                title: "Are you sure?",
                text: "This action cannot be undone",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#5cb85c",
                confirmButtonText: "Yes, Select as winner!",
                closeOnConfirm: false }
                , function(){
                    var url = '/update_auction_winner/' + oa_id + '/' + auc_id;
                    window.location.href = url;
                });
        });


    });
</script>
@endsection
