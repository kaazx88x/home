@extends('layouts.web.master')

@section('header')
    @include('layouts.web.header.main')
@endsection

@section('content')
    <!-- Category Listing Toggle -->
    <div class="ListingTopbar home">
        <div class="Sort">
            <div class="CategoryToggle" style="left: 0px;"><img src="assets/images/icon/icon_category.png"> @lang('localize.category')</div>
        </div>
    </div>

    <div class="HeroBoard owl-carousel">
        @foreach ($sliders as $key => $slider)
        <div class="item">
            <a href="{{ $slider->bn_redirecturl }}" target="{{ ($slider->bn_open == 1) ? '_blank' : '_self' }}">
                <img alt="{{ $slider->bn_title }}" src="{{ env('IMAGE_DIR') . '/banner/' . $slider->bn_img }}" title="{{ $slider->bn_title }}"/>
            </a>
        </div>
        @endforeach
    </div>

    <div class="FeaturedCategory" data-loader="customLoaderName">
        <div class="CategoryList">
            @foreach ($featured as $cat)
            <div class="List">
                <a href="#{{ strtolower(str_replace(' ', '', preg_replace('/[^a-zA-Z]/', '', $cat['parent']->name))) }}" class="page-scroll">
                <img class="lazyload" data-src="{{ env('IMAGE_DIR') . '/category/image/' . $cat['parent']->image }}">
                <button>{{ ucwords(strtolower($cat['parent']->name)) }}</button>
                </a>
            </div>
            @endforeach
        </div>

        <!-- * NEW ADDED * -->
        <div class="CategoryToggle">@lang('localize.view_all_categories')</div>
        <!-- * NEW ADDED END * <--></-->
    </div>

    @foreach ($featured as $key => $feat)
    <div class="SingleCategory" id="{{ strtolower(str_replace(' ', '', preg_replace('/[^a-zA-Z]/', '', $feat['parent']->name))) }}">
        <div class="ListTitle">
            <span>{{ ucwords(strtolower($feat['parent']->name)) }}</span>
        </div>
        <div class="ProductBanner">
            <a href="/products/category/{{ ($feat['parent']->url_slug)?$feat['parent']->url_slug:base64_encode($feat['parent']->id)}}">
                <img class="lazyload" data-src="{{ env('IMAGE_DIR') . '/category/banner/' . $feat['parent']->banner }}">
            </a>
        </div>
        <div class="ProductList">
            @foreach($feat['products'] as $product)
            <div class="List {{ ($product->pro_qty < 1) ? 'unavailable' : '' }}">
                <a href="/products/detail/{{$product->pro_id}}" title="{{ $product->title }}">
                    @if ($product->purchase_price < $product->price)
                    <div class="DiscountTag">-{{ $product->discounted_rate }}%</div>
                    @endif
                    <div class="ProductThumb">
                        <img class="lazyload" data-src="{{ env('IMAGE_DIR') . '/product/' .$product->pro_mr_id.'/'. $product->main_image }}" src="{{ asset('assets/images/stock.png') }}">
                    </div>
                    {{--  @if ($product->pro_qty < 1)
                        <div class="stock-availability">@lang('localize.outStock')</div>
                    @endif  --}}
                    <div class="ProductName">{{ $product->title }}</div>
                    <div class="ProductPrice">
                        {{$product->co_cursymbol . ' ' . $product->purchase_price}}
                        @if ($product->purchase_price < $product->price)
                        <span class="NormalPrice">{{$product->co_cursymbol . ' ' . $product->price}}</span>
                        @endif
                    </div>
                    {{--  @if ($product->shipping_fees_type == 0)
                        <span class="label label-primary">@lang('localize.shipping_fees_free') @lang('localize.shipping_fees')</span>
                    @endif  --}}
                </a>
                <a href="/products/detail/{{$product->pro_id}}" class="view">{{ ($product->pro_qty < 1) ? trans('localize.outStock') : trans('localize.view_product') }}</a>
            </div>
            @endforeach
        </div>
        <a href="/products/category/{{ ($feat['parent']->url_slug)?$feat['parent']->url_slug:base64_encode($feat['parent']->id) }}" class="more">@lang('localize.more')</a>
    </div>
    @endforeach
@stop

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/js/owlcarousel/owl.carousel.css') }}">
@endsection

@section('scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js/owlcarousel/owl.carousel.js') }}"></script>

    <script>
		$(document).ready(function() {
		  $('.HeroBoard').owlCarousel({
			loop: true,
			responsiveClass: true,
			items: 1,
			autoplay: true,
			lazyLoad: true
		  });

		  $(document).on('click', '.page-scroll', function(event) {
			var $anchor = $(this);
			$('html, body').stop().animate({
				scrollTop: ($($anchor.attr('href')).offset().top - 50)
			}, 1250, 'easeInOutExpo');
			event.preventDefault();
		  });

		})
	</script>
@endsection