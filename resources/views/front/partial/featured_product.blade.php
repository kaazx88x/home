<!-- featured category fashion -->
<div id="blocks-{{$key}}" class="grid--full grid--table grid-block-full">
	<div class="wrapper">
		<div class="bh-top home-block-title">
			<div class="collection-name"><img class="collection-icon" src="assets/images/{{ucwords(strtolower($feat['parent']->name))}}.png" alt="">{{$feat['parent']->name}}</div>
			<div class="collection-tags">
				<ul class="bh-tags">
					@foreach ($feat['child'] as $index => $child)
						@if ($index < 10)
						<li>
							<a href="/products/{{base64_encode($child->id)}}">{{ucwords(strtolower($child->name))}}</a>
						</li>
						@endif
					@endforeach
					<li>
						<a href="/products/{{base64_encode($feat['parent']->id)}}">{{trans('localize.viewallproduct')}}</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="wrapper">
		<div class="bh-btm">
			@if($key % 2 == 0)
			<div class="grid__item one-quarter bh-left small--one-whole medium--one-whole">
				<div class="banner-area">
					<a href="/products/{{base64_encode($featured['parent']->id)}}"><img src="assets/images/{{ucwords(strtolower($feat['parent']->name))}}-banner.png" alt=""></a>
				</div>
			</div>
			@endif
			<div class="grid__item three-quarters bh-right small--one-whole medium--one-whole">
				<div class="home-products-block bh-products">
					<div class="home-products-block-content">
						<div class="home-products-slider owl-carousel owl-theme">
								@foreach($feat['products'] as $product)
								<div class="grid__item">
								<div class="grid__item_wrapper">
									<div class="grid__image product-image">
										<div class="image-inner">
											<a href="/products/detail/{{$product->pro_id}}">
											<?php
												$image = $product->main_image;
												if (!str_contains($product->main_image, 'http://'))
													// $image = env('IMAGE_DIR') . '/product/' .$product->pro_mr_id.'/thumbnail_'. $product->main_image;
													$image = env('IMAGE_DIR') . '/product/' .$product->pro_mr_id.'/'. $product->main_image;
											?>
											<img alt="{{$product->title}}" src="/web/images/loading.gif" data-src="{{$image}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';">	</a>
										</div>
									</div>
									<h6 class="product-title"><a href="/products/detail/{{$product->pro_id}}">{{ $product->title }}</a></h6>
									<p class="product-price">
											@if ($product->purchase_price < $product->price)
										<span class="money" data-currency-usd="$19.99">{{$product->co_cursymbol}} {{$product->purchase_price}}</span>
										<s><span class="money" data-currency-usd="$24.99">{{$product->co_cursymbol}} {{$product->price}}</span></s>
										@else
										<span class="money" data-currency-usd="$19.99">{{$product->co_cursymbol}} {{$product->purchase_price}}</span>
										@endif

									</p>
									<ul class="action-button">
										<li class="add-to-cart-form">
											<a href="/products/detail/{{$product->pro_id}}" class="btn add-to-cart">
												<span id="AddToCartText"><i class="fa fa-shopping-cart"></i> {{trans('localize.addCart')}}</span>
											</a>
										</li>
									</ul>
								</div>
							</div>
								@endforeach
						</div>

					</div>
				</div>
			</div>
			@if($key % 2 == 1)
			<div class="grid__item one-quarter bh-right small--one-whole medium--one-whole">
				<div class="banner-area">
					<a href="/products/{{base64_encode($featured['parent']->id)}}"><img src="assets/images/{{ucwords(strtolower($feat['parent']->name))}}-banner.png" alt=""></a>
				</div>
			</div>
			@endif
		</div>
	</div>

</div>
<!-- end featured category fashion -->
