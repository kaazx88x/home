@extends('layouts.master')

@section('content')
    <div class="columns-container">
        <div class="container" id="columns">
            <!-- breadcrumb -->
            <div class="breadcrumb clearfix">
                <a class="home" href="#" title="Return to Home">{{trans('localize.home')}}</a>
                <span class="navigation-pipe">&nbsp;</span>
                <a href="#">{{trans('localize.bid')}}</a>
                <span class="navigation-pipe">&nbsp;</span>
                <span class="navigation_page">{{trans('localize.checkout_result')}}</span>
            </div>
            <!-- ./breadcrumb -->
        <div class="general2">
            <!-- page heading-->
            <h2 class="page-heading no-line">
                <span class="page-heading-title2">{{trans('localize.checkout_result')}}</span>
            </h2>
            <!-- ../page heading-->
            <div class="page-content page-order">
                <div class="order-success-content">
                    <div class="box-border">
                        <h3>{{trans('localize.transDetail')}}</h3>
                        <p>Thank you for shopping with meihome. You shall receive a status update within 3 working days, if
                            not your bid will be cancelled and game point will be refunded.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered cart_summary">
                                <thead>
                                <tr>
                                    <th>{{trans('localize.payerName')}}</th>
                                    <th>{{trans('localize.transID')}}</th>
                                    <th class="text-center">{{trans('localize.orderStatus')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$order->payer_name}}</td>
                                    <td>{{$order->transaction_id}}</td>
                                    <td class="text-center">
                                        <span class="order-status order-status-received">Received</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="box-border">
                        <h3>{{trans('localize.curDetail')}}</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered cart_summary">
                                <thead>
                                <tr>
                                    <th>{{trans('localize.productName')}}</th>
                                    <th class="text-center">{{trans('localize.quantity')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$auction->title}}</td>
                                    <td class="text-center">1</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
