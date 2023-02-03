@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
<div class="ListingTopbar">
    <h4 class="ListingCategory">@lang('localize.my_transaction_limit')</h4>
    <a href="/profile" class="back"><i class="fa fa-angle-left"></i></a>
</div>

<div class="ContentWrapper">
    <div class="panel-general">
        <div class="row">
            @foreach($transactions as $key => $trans)
            <div class="col-lg-12" style="padding: 0 20px 0; border-bottom:1px solid #eee;">
                <div class="ibox float-e-margins" style="margin:20px 0 20px;">
                    <div class="ibox-title">
                        <span style="font-size:large;">{{ $types[$key] }}</span>
                        <span class="label label-{{ ($trans['block_amount'] || $trans['block_count'])? 'warning' : 'success' }} pull-right">{{ ($trans['block_amount'] || $trans['block_count'])? trans('localize.limited') :  trans('localize.unlimited') }}</span>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                @if($trans['block_amount'])
                                @if($key == 1)
                                <h3 class="no-margins"><small>{{ $customer->country ? $customer->country->co_curcode : '' }}</small>
                                    {{ number_format($trans['limit_amount_exceed'], 2) }}
                                </h3>
                                @lang('localize.total_transaction')
                                @else
                                <h3 class="no-margins"><small>{{ $customer->country ? $customer->country->co_curcode : '' }}</small>
                                    {{ number_format($trans['current_amount'], 2) }} / {{ number_format($trans['limit_amount_exceed'], 2) }}
                                </h3>
                                <div class="stat-percent font-bold text-{{ ($trans['limit_amount_usage'] >= 100)? 'danger' : 'info' }}">{{ $trans['limit_amount_usage'] }} %</div>
                                @lang('localize.total_transaction')
                                @endif
                                @else
                                @if($key>1)
                                <h3 class="no-margins"><small>{{ $customer->country ? $customer->country->co_curcode : '' }}</small> {{ number_format($trans['current_amount'], 2) }}</h3>
                                @lang('localize.total_transaction')
                                @endif
                                @endif
                            </div>
                            @if($key > 1)
                            <div class="col-lg-12">
                                @if($trans['block_count'])
                                <h3 class="no-margins">{{ $trans['current_count'] }} / {{ $trans['limit_count_exceed'] }}</h3>
                                <div class="stat-percent font-bold text-{{ ($trans['limit_count_usage'] >= 100)? 'danger' : 'info' }}">{{ $trans['limit_count_usage'] }} %</div>
                                @lang('localize.total_transaction_number')
                                @else
                                <h3 class="no-margins">{{ $trans['current_count'] }}</h3> @lang('localize.total_transaction_number')
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">


</script>
@endsection
