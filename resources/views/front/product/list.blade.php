@extends('layouts.web.master')

@section('header')
    @include('layouts.web.header.main')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('web/lib/jquery-ui/jquery-ui.css') }}">
    <div class="ListingTopbar">
        <h4 class="ListingCategory">{{ ucwords(strtolower($category)) }}</h4>
        <a href="javascript:void(0)" class="back goBack"><i class="fa fa-angle-left"></i></a>
        <div class="Sort">
            <div class="CategoryToggle"><img src="{{ asset('assets/images/icon/icon_category.png') }}"> @lang('localize.category')</div>
            <div class="FilterToggle"><img src="{{ asset('assets/images/icon/icon_filter.png') }}"> @lang('localize.filter')</div>
        </div>
    </div>
    <div class="FilterPanel">
        <ul>
            <li><a href="javascript:void(0)" class="CloseToggle">@lang('localize.close_')</a></li>
            <li>
                <h4>@lang('localize.price')</h4>
                <div class="layered-content slider-range">
                <?php
                    $min = (isset($rangeParams[0])) ? $rangeParams[0] : 0.1;
                    $max = (isset($rangeParams[1])) ? $rangeParams[1] : 3000;
                    $rp = $min.'-'.$max;
                ?>
                <center><div data-label-result="@lang('localize.price_range'): {{$co_cursymbol}}" data-min="0.1" data-max="3000" data-unit="$" class="slider-range-price" data-value-min="{{$min}}" data-value-max="{{$max}}"></div>
                <div class="amount-range-price"><br/>
                    <span class="range-price-span">@lang('localize.price_range'): {{$co_cursymbol}} {{$min}} - {{$max}}</span>
                    <input type="hidden" id="min_range" value="{{$min}}">
                    <input type="hidden" id="max_range" value="{{$max}}">
                </div><button type="button" class="btn btn-primary btn-xs btn_range_submit">GO</button></center><br/>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" id="p1" name="pr_checkbox" data-val="1-500" {{($rp == '1-500') ? 'checked' : ''}}/>
                    <span for="p1" class="button">{{$co_cursymbol}}  1 - 500</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" id="p2" name="pr_checkbox" data-val="501-1000" {{($rp == '501-1000') ? 'checked' : ''}}/>
                    <span for="p2" class="button">{{$co_cursymbol}} 501 - 1000</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" id="p3" name="pr_checkbox" data-val="1001-1500" {{($rp == '1001-1500') ? 'checked' : ''}}/>
                    <span for="p3" class="button">{{$co_cursymbol}} 1001 - 1500</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" id="p4" name="pr_checkbox" data-val="1501-2000" {{($rp == '1501-2000') ? 'checked' : ''}}/>
                    <span for="p4" class="button">{{$co_cursymbol}} 1501 - 2000</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" id="p5" name="pr_checkbox" data-val="2001-2500" {{($rp == '2001-2500') ? 'checked' : ''}}/>
                    <span for="p5" class="button">{{$co_cursymbol}} 2001 - 2500</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" id="p6" name="pr_checkbox" data-val="2501-3000" {{($rp == '1501-3000') ? 'checked' : ''}}/>
                    <span for="p6" class="button">{{$co_cursymbol}} 2501 - 3000</span>
                </label>
            </li>
            <li style="font-family:'FontAwesome', sans-serif; ">
            <h4>{{trans('localize.sort')}}</h4>
                <label class="checkbox checkbox-inline" >
                    <input type="checkbox" onclick="paramPage('sort', value)" value="name_asc" id="name_asc" name="sort" data-val="name_asc" {{($sortParams == 'name_asc') ? 'checked' : ''}}/>
                    <span for="name_asc" class="button">@lang('localize.productName') : &#xf15d;</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" onclick="paramPage('sort', value)" value="name_desc" id="name_desc" name="sort" data-val="name_desc" {{($sortParams == 'name_desc') ? 'checked' : ''}}/>
                    <span for="name_desc" class="button">@lang('localize.productName') : &#xf15e;</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" onclick="paramPage('sort', value)" value="price_asc" id="price_asc" name="sort" data-val="price_asc" {{($sortParams == 'price_asc') ? 'checked' : ''}}/>
                    <span for="price_asc" class="button">@lang('localize.price') : &#xf162;</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" onclick="paramPage('sort', value)" value="price_desc" id="price_desc" name="sort" data-val="price_desc" {{($sortParams == 'price_desc') ? 'checked' : ''}}/>
                    <span for="price_desc" class="button">@lang('localize.price') : &#xf163;</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" onclick="paramPage('sort', value)" value="new" id="new" name="sort" data-val="new" {{($sortParams == 'new') ? 'checked' : ''}}/>
                    <span for="new" class="button">@lang('localize.newest')</span>
                </label>
            </li>
            <li>
            <h4>{{trans('localize.show')}}</h4>
                <label class="checkbox checkbox-inline" >
                    <input type="checkbox" onclick="paramPage('items', value)" value="15" id="show15" name="items" data-val="name_asc" {{($itemParams == '15') ? 'checked' : ''}}/>
                    <span for="show15" class="button">@lang('localize.show') 15</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" onclick="paramPage('items', value)" value="25" id="show25" name="items" data-val="name_desc" {{($itemParams == '25') ? 'checked' : ''}}/>
                    <span for="show25" class="button">@lang('localize.show') 25</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" onclick="paramPage('items', value)" value="50" id="show50" name="items" data-val="price_asc" {{($itemParams == '50') ? 'checked' : ''}}/>
                    <span for="show50" class="button">@lang('localize.show') 50</span>
                </label>
                <label class="checkbox checkbox-inline">
                    <input type="checkbox" onclick="paramPage('items', value)" value="100" id="show100" name="items" data-val="price_desc" {{($itemParams == '100') ? 'checked' : ''}}/>
                    <span for="show100" class="button">@lang('localize.show') 100</span>
                </label>
            </li>
            <li>
            @foreach($filters_array as $key => $filters)
                <h4>{{ $filters['filter']->name }}</h4>
                @foreach($filters['items'] as $attribute_item)
                    <label class="checkbox checkbox-inline">
                    @if($filterParams)
                        <input type="checkbox" id="attribute_item{{$attribute_item->id}}" name="filters" data-val="{{ $attribute_item->id }}" {{(in_array($attribute_item->id, $filterParams)) ? 'checked' : ''}}>
                    @else
                        <input type="checkbox" id="attribute_item{{$attribute_item->id}}" name="filters" data-val="{{ $attribute_item->id }}"/>
                    @endif
                    <span for="attribute_item{{$attribute_item->id}}" class="button">{{ $attribute_item->name }}</span></label>
                @endforeach
            @endforeach
            </li>
        </ul>

    </div>

    <div class="ProductList">
        @foreach ($products as $key => $pro)
            <div class="List {{ ($pro->pro_qty < 1) ? 'unavailable' : '' }}">
                <a href="/products/detail/{{ $pro->pro_id }}" title="{{ $pro->title }}">
                @if ($pro->purchase_price < $pro->price)
                <div class="DiscountTag">-{{ $pro->discounted_rate }}%</div>
                @endif
                <div class="ProductThumb">
                    <img class="lazyload" data-src="{{ env('IMAGE_DIR') . '/product/' .$pro->pro_mr_id.'/'. $pro->main_image }}" src="{{ asset('assets/images/stock.png') }}">
                </div>
                {{--  @if ($pro->pro_qty < 1)
                    <div class="stock-availability">@lang('localize.outStock')</div>
                @endif  --}}
                <div class="ProductName">{{ $pro->title }}</div>
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
                <a href="/products/detail/{{$pro->pro_id}}" class="view">{{ ($pro->pro_qty < 1) ? trans('localize.outStock') : trans('localize.view_product') }}</a>
            </div>
        @endforeach
    </div>
    <!-- PAGINATION -->
    <div class="PaginationContainer">
        {{--  <ul class="pagination">  --}}
            {{ $products->links() }}
        {{--  </ul>  --}}
    </div>
@endsection

@section('styles')
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/jscroll-master/jquery.jscroll.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/js/jquery-ui-1.10.4.min.js') }}"></script>

    <script>
        $(document).ready(function(){
            $(document).ready(function() {
                $('.PageContainer').addClass('ProductListing');
            });

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
        });

        $(document).ready(function() {

            $('input[name="filters"]').on('change', function(e){
                var data = [];
                $('input[name="filters"]').each(function(i){
                    var this_val = $(this).attr('data-val')
                    if(this.checked){
                        data.push(this_val);
                    }
                });
                data = data.join('-');
                data = btoa(data);
                paramPage('options', data);
            });

            $('.slider-range-price').each(function () {
                var min = $(this).data('min');
                var max = $(this).data('max');
                var unit = $(this).data('unit');
                var value_min = $(this).data('value-min');
                var value_max = $(this).data('value-max');
                var label_reasult = $(this).data('label-result');
                var t = $(this);
                $(this).slider({
                    range: true,
                    min: min,
                    max: max,
                    values: [value_min, value_max],
                    slide: function (event, ui) {
                        var result = label_reasult + " " + ui.values[0] + ' - ' + ui.values[1];
                        t.closest('.slider-range').find('.range-price-span').html(result);
                        $('#min_range').val(ui.values[0]);
                        $('#max_range').val(ui.values[1]);
                    }
                });
            });

            $('.btn_range_submit').on('click', function(e) {
                var data = [];
                data.push($('#min_range').val());
                data.push($('#max_range').val());
                data = data.join('-');
                paramPage('range', data);
            });

            $('input[name="pr_checkbox"]').on('change', function(e){
                var this_val = $(this).attr('data-val');
                paramPage('range', this_val);
            });
        });

        function paramPage($item, $val) {
            var href = window.location.href.substring(0, window.location.href.indexOf('?'));
            var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
            qs = qs.replace(/&?page=([^&]$|[^&]*)/ig, "");
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
    </script>
@endsection
