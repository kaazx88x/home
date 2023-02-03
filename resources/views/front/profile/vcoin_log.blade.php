@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.vcoinLog')</h4>
        <a href="/profile" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

    <div class="ContentWrapper">
        <div class="panel-general panel-table">
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('localize.credit')</th>
                        <th>@lang('localize.debit')</th>
                        <th>@lang('localize.from')</th>
                        <th>@lang('localize.remarks')</th>
                        <th class="text-nowrap">@lang('localize.wallet_type')</th>
                        <th>@lang('localize.date')</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($logs))
                        @foreach($logs as $key => $log)
                        <tr>
                            <td class="text-center">{{ $key+1 }}</td>
                            <td class="text-center">{{ ($log->credit_amount != 0) ? $log->credit_amount : '' }}</td>
                            <td class="text-center">{{ ($log->debit_amount != 0) ? $log->debit_amount :'' }}</td>
                            <td class="text-center">{{ $log->from }}</td>
                            <td class="text-center">
                                {{$log->remark}}
                                <br/>
                                <small>{{ ($log->order_id != 0) ? $log->title : '' }}</small>
                            </td>
                            <td class="text-center">
                                @if ($log->wallet_id)
                                    @if ($log->wallet_id == 0)
                                        Hemma
                                    @else
                                        {{ ($log->wallet) ? $log->wallet->name : '-' }}
                                    @endif
                                @endif
                            </td>
                            {{-- <td class="text-center">{{ \Carbon\Carbon::createFromTimestamp(strtotime($log->created_at))->timezone('Asia/Kuala_Lumpur')->format('d F Y h:i A') }}</td> --}}
                            <td class="text-center">{{ \Helper::UTCtoTZ($log->created_at) }}</td>
                        </tr>
                        @endforeach
                </tbody>
                @else
                <td colspan="6"><center>@lang('localize.table_no_record')</center></td>
                @endif
            </table>
            </div>
        </div>

        <div class="PaginationContainer">
            {{ $logs->links() }}
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/footable/footable.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    jQuery(function($){
        $('.responsive-table').footable({
            empty: "@lang('localize.table_no_record')"
        });
    });

    function paramPage($item, $val) {
        var href = window.location.href.substring(0, window.location.href.indexOf('?'));
        var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
        var newParam = $item + '=' + $val;

        if (qs.indexOf($item + '=') == -1) {
            if (qs == '') {
                qs = '?'
            }
            else {
                qs = qs + '&'
            }
            qs = newParam;

        }
        else {
            var start = qs.indexOf($item + "=");
            var end = qs.indexOf("&", start);
            if (end == -1) {
                end = qs.length;
            }
            var curParam = qs.substring(start, end);
            qs = qs.replace(curParam, newParam);
        }
        window.location.replace(href + '?' + qs);
    }
</script>
@endsection
