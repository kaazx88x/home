@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
    <div class="ProductHeader">
        <a href="javascript:void(0)" class="back transition goBack"><i class="fa fa-angle-left"></i></a>

        <!-- * NEW ADDED * -->
        <a href="javascript:void(0)" class="CategoryToggle"><img src="{{ asset('assets/images/icon/icon_category.png') }}"></a>
        <!-- * NEW ADDED END* -->

        <h4>{{ $product['details']->title }}</h4>
    </div>

    <div class="ProductSlide owl-carousel">
        @foreach($product['images'] as $pic)
            <div class="item">
                <img src="{{ env('IMAGE_DIR').'/product/'.$product['details']->pro_mr_id.'/'.$pic->image }}">
            </div>
        @endforeach
    </div>

    <div class="ProductView">
        <h4>{{ $product['details']->title }}</h4>

        <span class="with_discounted" style="display: {{ ($product['pricing']->purchase_price < $product['pricing']->price)? 'block' : 'none' }};">
            <div class="ProductPrice">
                {{$product['pricing']->co_cursymbol}} <span class="discounted_purchase_price">{{$product['pricing']->purchase_price}}</span>
                <span class="NormalPrice">
                    {{$product['pricing']->co_cursymbol}} <span class="discounted_price">{{ number_format($product['pricing']->price, 2) }}</span>
                </span>
                <div class="DiscountTag">
                    <span class="discounted_rate">{{ $product['pricing']->discounted_rate }}</span> %
                </div>
            </div>
        </span>

        <span class="without_discounted" style="display: {{ ($product['pricing']->purchase_price == $product['pricing']->price)? 'block' : 'none' }};">
            <div class="ProductPrice">
                {{$product['pricing']->co_cursymbol}}<span class="discounted_purchase_price">{{$product['pricing']->purchase_price}}</span>
            </div>
        </span>

        <p>@lang('localize.availability') :  {{ ($product['pricing']->quantity > 0) ? trans('localize.inStock') : trans('localize.outStock') }}</p>
        <p>
            @lang('localize.shipping_fees') :
            @if($product['pricing']->shipping_fees_type == 1)
                {{$product['pricing']->co_cursymbol}} {{$product['pricing']->shipping_fees}} - @lang('localize.shipping_fees_product')
            @elseif($product['pricing']->shipping_fees_type == 2)
                {{$product['pricing']->co_cursymbol}} {{$product['pricing']->shipping_fees}} - @lang('localize.shipping_fees_transaction')
            @else
                @lang('localize.shipping_fees_free')
            @endif
        </p>

        @if($product['pricing']->shipping_fees_type == 3)
        <br><p>
            @lang('localize.member_self_pickup_disclaimer')
        </p>
        @endif
    </div>

    <div class="ProductSelection">
        <img src="{{ asset('assets/images/icon/icon_selection.png') }}">
        <h4>@lang('localize.filter_selection')</h4>
        <label>@lang('localize.product_color'), @lang('localize.product_size')</label>
        <i class="fa fa-angle-right"></i>
    </div>
    <div class="PanelSelection">
        <form action="/carts/add" method="post" enctype="multipart/form-data" id="detailcart" class="form-vertical">
            {{ csrf_field() }}
            @if(count($attribute_listing['parent']) > 0)
                <div id="attributes">
                    @foreach($attribute_listing['parent'] as $parent_id => $attribute)
                        <div class="form-group">
                            <label class="selection-label">{{ $attribute['name'] }}</label>
                            <div id="parent_attribute_{{ $parent_id }}">
                                @if($parent_id == 0)
                                    @foreach($attribute['items'] as $key => $name)
                                        <label class="radio radio-inline" >
                                            <input type="radio" name="attribute_selection[{{ $parent_id }}]" id="attribute_{{ $key }}" value="{{ $parent_id }}:{{ $key }}" class="attribute_selection">
                                            <span>{{ $name }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" id="selected_attribute" name="selected_attribute" value="{{ json_encode($product['attributes']) }}">
            @endif
            <div class="form-group">
                <label class="selection-label">@lang('localize.quantity')</label>
                <div class="quantity-selector">
                    <button type="button" id="sub" class="sub">-</button>
                    <input type="number" class="number" id="qty" name="qty" value="1" min="1"/>
                    <button type="button" id="add" class="add">+</button>
                </div>
            </div>

            <span class="with_discounted" style="display: {{ ($product['pricing']->purchase_price < $product['pricing']->price)? 'block' : 'none' }};">
                <div class="ProductPrice">
                    {{$product['pricing']->co_cursymbol}} <span class="discounted_purchase_price">{{$product['pricing']->purchase_price}}</span>
                    <span class="NormalPrice">
                        {{$product['pricing']->co_cursymbol}} <span class="discounted_price">{{ number_format($product['pricing']->price, 2) }}</span>
                    </span>
                    <div class="DiscountTag">
                        <span class="discounted_rate">{{ $product['pricing']->discounted_rate }}</span> %
                    </div>
                </div>
            </span>

            <span class="without_discounted" style="display: {{ ($product['pricing']->purchase_price == $product['pricing']->price)? 'block' : 'none' }};">
                <div class="ProductPrice">
                    {{$product['pricing']->co_cursymbol}}<span class="discounted_purchase_price">{{$product['pricing']->purchase_price}}</span>
                </div>
            </span>

            <button name="add" id="addCart" class="btn-buy">
                <span id="AddToCartText">@lang('localize.addCart')</span>
            </button>
            <input type="hidden" id="max_quantity" value="{{ $product['pricing']->quantity }}">
            <input type="hidden" name="product_id" value="{{$product['details']->pro_id}}" />
            <input type="hidden" name="merchant_id" value="{{$product['details']->pro_mr_id}}" />
            <input type="hidden" name="price_id" id="price_id" value="{{$product['pricing']->id}}" />
        </form>
    </div>

    <div class="ProductDetail">
        <ul class="DetailSelection">
            <li class="active"><a data-toggle="tab" href="#productdetail">@lang('localize.pro_details')</a></li>
            <li><a data-toggle="tab" href="#info">@lang('localize.information')</a></li>
        </ul>
        <div class="tab-content">
            <div id="productdetail" class="tab-pane in active">
                <br/>
                <dl class="dl-horizontal">
                    <dt>@lang('localize.product')</dt>
                    <dd>{{ $product['details']->title }}</dd>
                    <dt>@lang('localize.category')</dt>
                    <dd>{{ ucwords(strtolower($product['category']->name)) }}</dd>
                    @if($product['details']->pro_type == 4)
                    <dt>@lang('localize.e-card.validity')</dt>
                    @if($product['details']->start_date && $product['details']->end_date)
                    <dd>{{ \Helper::UTCtoTZ($product['details']->start_date) }} @lang('localize.to') {{ \Helper::UTCtoTZ($product['details']->end_date) }}</dd>
                    @else
                    <dd>-</dd>
                    @endif
                    @endif
                </dl>
            </div>
            <div id="info" class="tab-pane">
                <br/>
                {!! $product['details']->desc !!}
            </div>
    </div>

    <div class="ProductAction">
        <a href="/" class="home transition"><img src="{{ asset('assets/images/icon/icon_home.png') }}">@lang('localize.home')</a>
        <a href="/carts" class="cart transition"><img src="{{ asset('assets/images/icon/icon_shoppingcart.png') }}">@lang('localize.cart') <div class="CartCount">{{ count($carts) }}</div></a>
        <button class="btn-buy">@lang('localize.addCart')</button>
    </div>
@stop

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/js/owlcarousel/owl.carousel.css') }}">
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/owlcarousel/owl.carousel.js') }}"></script>

    @if (session('error'))
        <script>
            swal('{{ session('error') }}');
        </script>
    @endif

    <script>
        $(window).load(function() {
            $('.ProductSlide').owlCarousel({
                loop: true,
                items: 1,
                autoHeight:true,
                lazyLoad: true
            });

            $('.PageContainer').addClass('Product');
        });

        $(document).ready(function() {
            $('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

            var parent_listing = {!! json_encode($attribute_listing['parent']) !!};
            var pricing_attribute = {!! $attribute_listing['pricing'] !!};
            var attribute_end = parseInt("{{ count($attribute_listing['parent']) }}")-1;
            var this_attribute_onload = {!! json_encode($product['attributes']) !!};

            if(this_attribute_onload.length > 0) {
                var onload_attributes = new Array;
                var next_parent_id = 1;
                $.each(this_attribute_onload, function( parent, attribute_id ) {
                    onload_attributes[parseInt(parent)] = parseInt(attribute_id);

                    if(parent == 0 ) {
                        $("#attribute_"+attribute_id).prop('checked', true);
                    }

                    // $("#attribute_"+next_parent_id).html(""); //reset child options
                    // $("#attribute_"+next_parent_id).append("<option value='0'>"+'{{ trans('localize.selectOption') }}'+"</option>");


                    var result = get_child_attribute_list(pricing_attribute, onload_attributes);

                    if(next_parent_id <= attribute_end) {
                        $.each(result, function(key, result_attribute_id) {
                            var attribute_name = String(parent_listing[next_parent_id]["items"][result_attribute_id]);
                            var val = next_parent_id+":"+result_attribute_id;
                                // $("#attribute_"+next_parent_id).append("<option value='"+val+"'>"+attribute_name+"</option>");
                                $("#parent_attribute_"+next_parent_id).append("<label class='radio radio-inline'><input type='radio' name='attribute_selection["+next_parent_id+"]' id='attribute_"+result_attribute_id+"' value='"+val+"' class='attribute_selection'><span>"+attribute_name+"</span></label>");
                        });
                    }
                    $("#attribute_"+attribute_id).prop('checked', true);

                    next_parent_id++;
                });
            }

            $(document).on('change', 'input.attribute_selection', function() {
                var selected = this.value.split(':');
                var selected_parent = parseInt(selected[0]);
                var selected_attribute = parseInt(selected[1]);
                var attributes = new Array;

                if(selected_parent == attribute_end) {
                    $(".attribute_selection").each(function(){
                        if ($(this).prop('checked')) {
                            attributes.push(parseInt(this.value.split(':')[1]));
                        }
                    });

                    get_pricing_by_selected_attribute(attributes);
                    return false;
                }

                if(selected_parent > 0)
                {
                    var parent = 0;
                    while(parent <= selected_parent) {
                        attributes[parent] = parseInt($("input[name='attribute_selection["+parent+"]']:checked").val().split(':')[1]);
                        parent++;
                    }
                } else {
                    attributes[selected_parent] = selected_attribute;
                }

                var loop = (selected_parent + 1 );
                while ( loop <= attribute_end) {

                    $("#parent_attribute_"+loop).html(""); //reset child options
                    loop++;
                }

                var childs = get_child_attribute_list(pricing_attribute,attributes);
                var next_parent_id = attributes.length;

                $.each(childs, function(key,attribute_id) {
                    var attribute_name = String(parent_listing[next_parent_id]["items"][attribute_id]);
                    var value = next_parent_id+":"+attribute_id;
                    $("#parent_attribute_"+next_parent_id).append("<label class='radio radio-inline'><input type='radio' name='attribute_selection["+next_parent_id+"]' id='attribute_"+attribute_id+"' value='"+value+"' class='attribute_selection'><span>"+attribute_name+"</span></label>");
                });
            });
        });

        $("#detailcart").on('submit', function(e){
            var valid = true;
            var total_in_cart = {!! json_encode($total_in_cart) !!};
            var total = parseInt(total_in_cart[$('#price_id').val()]);
            if(isNaN(total))
                total = 0;

            // Check all attributes selected
            var attribute_end = parseInt("{{ count($attribute_listing['parent']) }}");
            var attribute_selected = $(":radio[class='attribute_selection']:checked").length;

            if (attribute_selected != attribute_end) {
                swal("@lang('localize.alert')!", "@lang('localize.select_option')", "error");
                valid = false;
            }

            if ($('#qty').val() == '' || ( parseInt($('#qty').val()) + total) > parseInt($('#max_quantity').val())) {
                swal("@lang('localize.alert')!", "@lang('localize.checkout_insufficient')" + $('#max_quantity').val(), "error");
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            } else {
                $('.loading').show();
            }
        });

        function get_child_attribute_list(pricing_attribute, attributes)
        {
            var contains = new Array;
            var max_check = parseInt(attributes.length) - 1;
            var next_parent_id = attributes.length;
            $.each(pricing_attribute, function(pricing_id, items) {
                var i = 0;
                var has = 0;
                while ( i <= max_check) {
                    if(items[i] == attributes[i]) {
                        has++;
                    }
                    i++;
                }

                if(has > max_check) {
                    var stats = $.inArray(pricing_attribute[pricing_id][next_parent_id], contains);
                    if (stats == -1) {
                        contains.push(pricing_attribute[pricing_id][next_parent_id]);
                    }
                }
            });

            return contains;
        }

        //function to populate child select box
        function get_pricing_by_selected_attribute(attributes) {

            $.ajax({
                type: "GET",
                url: '/product_detail/get_attribute_selection/'+"{{$product['details']->pro_id}}",
                data: {attributes: attributes},
                beforeSend : function() {

                },
                success: function (response) {
                    if(response != 'empty') {
                        $(".loading").show();
                        if(parseFloat(response['purchase_price']) < parseFloat(response['price'])) {
                            $('.with_discounted').show();
                            $('.without_discounted').hide();
                            $('.discounted_rate').text(parseFloat(response['discounted_rate']).toFixed(2));
                            $('.discounted_purchase_price').text(parseFloat(response['purchase_price']).toFixed(2));
                            $('.discounted_price').text(parseFloat(response['price']).toFixed(2));
                            $('#max_quantity').val(response['quantity']);
                        } else {
                            $('.with_discounted').hide();
                            $('.without_discounted').show();
                            $('.discounted_purchase_price').text(parseFloat(response['purchase_price']).toFixed(2));
                            $('#max_quantity').val(response['quantity']);
                        }

                        if(response['quantity'] > 0) {
                            $('#submit').show();
                            $('#outstock').hide();
                            $('#instock').show();
                        } else {
                            $('#submit').hide();
                            $('#outstock').show();
                            $('#instock').hide();
                        }

                        $('#selected_attribute').val(JSON.stringify(attributes));
                        $('#price_id').val(response['price_id']);
                        $(".loading").hide();
                    } else {
                        swal("@lang('localize.not_available')!", "@lang('localize.product_not_available')", "warning");
                    }
                }
            });
        }
    </script>
@endsection
