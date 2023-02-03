@extends('merchant.layouts.master')

@section('title', 'Quantity Log')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>@lang('localize.product_quantity')</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url( $route . '/product/manage') }}">@lang('localize.product')</a>
            </li>
            <li>
                @lang('localize.quantity_log')
            </li>
            <li class="active">
                <strong>{{$product['details']->pro_id}} - {{$product['details']->pro_title_en}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    @include('merchant.common.notifications')

    <div class="row">
        <div class="tabs-container">

            @include('merchant.product.nav-tabs', ['link' => 'log'])

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>@lang('localize.quantity_log')</h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-nowrap">#</th>
                                                    <th class="text-center text-nowrap">@lang('localize.product_item')</th>
                                                    <th class="text-center text-nowrap">@lang('localize.credit')</th>
                                                    <th class="text-center text-nowrap">@lang('localize.debit')</th>
                                                    <th class="text-center text-nowrap">@lang('localize.current_quantity')</th>
                                                    <th class="text-center text-nowrap">@lang('localize.remarks')</th>
                                                    <th class="text-center text-nowrap">@lang('localize.date')</th>
                                                </tr>
                                            </thead>
                                            <tbody class="sort">
                                                @foreach ($logs as $key => $log)
                                                <tr class="text-center">
                                                    <td>{{ (($logs->currentPage() - 1 ) * $logs->perPage() ) + $key+1 }}</td>
                                                    <td>
                                                        @if($log->attributes)
                                                            @foreach(json_decode($log->attributes, true) as $attribute => $attribute_item)
                                                            <b>{{$attribute}} : </b>{{$attribute_item}}</br>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>{{ ($log->credit != 0)? $log->credit : '' }}</td>
                                                    <td>{{ ($log->debit != 0)? $log->debit : '' }}</td>
                                                    <td>{{ $log->current_quantity }}</td>
                                                    <td>{{ $log->remarks }}</td>
                                                    {{-- <td>{{ $log->created_at }}</td> --}}
                                                    <td>{{ \Helper::UTCtoTZ($log->created_at) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="7">
                                                        <ul class="pagination pull-right">{{$logs->appends(Request::except('page'))->links()}}</ul>
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
    </div>
</div>
@stop
