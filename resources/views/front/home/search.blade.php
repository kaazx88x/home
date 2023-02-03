@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">"{{ $search }}" - {{ $results->total() }} @lang('localize.results')</h4>
        <a href="javascript:void(0)" class="back goBack"><i class="fa fa-angle-left"></i></a>
    </div>

    <div class="ProductList">
        @foreach ($results as $key => $pro)
            <div class="List" style="height:280px">
                <a href="/products/detail/{{$pro->pro_id}}" title="{{ ucwords(strtolower($pro->title)) }}">
                <?php
                    // $pro_img = explode('/**/', $pro->pro_Img);
                    $image = $pro->main_image;
                    if (!str_contains($pro->main_image, 'http://'))
                        $image = env('IMAGE_DIR').'/product/'.$pro->pro_mr_id.'/'.$pro->main_image;
                        //dd($image);
                ?>
                <div class="ProductThumb">
                    <img class="lazyload" src="/web/images/loading.gif" data-src="{{$image}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';">
                </div>
                <div class="ProductName">{{$pro->title}}</div>
                <div class="ProductPrice">
                    {{ $pro->co_cursymbol . ' ' . $pro->purchase_price }}
                    @if ($pro->purchase_price < $pro->price)
                    <span class="NormalPrice">{{ $pro->co_cursymbol . ' ' . $pro->price }}</span>
                    @endif
                </div>
                {{--  @if ($pro->shipping_fees_type == 0)
                    <span class="label label-primary">@lang('localize.shipping_fees_free') @lang('localize.shipping_fees')</span>
                @endif  --}}
                </a>
                <a href="/products/detail/{{$pro->pro_id}}" class="view">View Product</a>
            </div>
        @endforeach
        <!-- INFINITE SCROLL -->
        {{--  <div class="next">
            <a href="/search">More Products...</a>
        </div>  --}}

        <!-- PAGINATION -->
        <div class="PaginationContainer">
            <ul class="pagination" style="margin-top:10px">
                {{ $results->links() }}
            </ul>
        </div>

    </div>

@endsection

@section('scripts')
<script type="text/javascript" src="assets/js/jscroll-master/jquery.jscroll.js"></script>
<script type="text/javascript" src="assets/js/jquery.lazy-master/jquery.lazy.min.js"></script>

	<script>
        function paramPage($item, $val) {
            var href = window.location.href.substring(0, window.location.href.indexOf('?'));
            var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
            var newParam = $item + '=' + $val;
            // console.log(href);
            // console.log(qs);
            // console.log(qs.indexOf($item + '='));

            if (qs.indexOf($item + '=') == -1) {
                if (href == '') {
                    qs = '?'
                }
                else {
                    qs = '?' + qs + '&'
                }
                qs += newParam;

            }
            else {
                var start = qs.indexOf($item + "=");
                var end = qs.indexOf("&", start);
                if (end == -1) {
                    end = qs.length;
                }
                var curParam = qs.substring(start, end);
                qs = '?' + qs.replace(curParam, newParam);
            }
            // console.log(qs);
            window.location.replace(href + qs);
        }

		$(document).ready(function(){
			$('.ProductList').jscroll({
				debug: false,
				loadingHtml: '<img src="assets/images/loading.gif" alt="Loading" /> Loading...',
				padding: 20,
				nextSelector: '.next a',
				contentSelector: '.ProductList',
				autoTrigger: true,
				autoTriggerUntil: 1,
				callback: function(){
					$('.lazyload').lazy({
						effect: 'fadeIn',
						effectTime: 2000,
						threshold: 0
					});
				}
			});
            //console.log(history.length);

            $("#back").click(function(){

                    if(history.length == 1){
                        console.log("test");
                        document.location.href = "/";
                    }else{
                        window.history.go(-1);
                        console.log("teqst");
                    }
            });
		});


	</script>
@endsection
