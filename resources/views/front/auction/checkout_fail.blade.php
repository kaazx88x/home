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
                <div class="order-fail-content">
                    <div class="box-border">
                        <h3>{{trans('localize.transDetail')}}</h3>
                        <!-- <p>{{trans('localize.transDetail_failed')}}</p> -->
                        <p>Sorry that your transaction is failed. Please return to checkout page to try again. Thank you.</p>
                        <div class="cart_navigation">
                            <a class="prev-btn" href="/auctions/checkout/{{$auc_id}}">Return to checkout</a>
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
