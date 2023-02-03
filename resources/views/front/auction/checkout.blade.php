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
                <span class="navigation_page">{{trans('localize.checkout')}}</span>
            </div>
            <!-- ./breadcrumb -->
        <div class="general2">
            <!-- page heading-->
            <h2 class="page-heading">
                <span class="page-heading-title2">{{trans('localize.checkoutAuction')}}</span>
            </h2>
            <!-- ../page heading-->
            <div class="page-content checkout-page">
                {{-- @include('front.common.errors') --}}
                <form id="checkout" action='/auctions/checkout' method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="pull-right">
                        <label>{{ trans('localize.loadDetails') }} : </label>
                        <span>
                            <input type="radio" class="address_detail" data-val="1" name="shipping_addr" id="shipping_addr_1rad" value="yes"> {{ trans('localize.yes') }}
                            &nbsp;
                            <input type="radio" class="address_detail" data-val="0" name="shipping_addr" id="shipping_addr_2rad" value="no" checked="true"> {{ trans('localize.no') }}
                        </span>
                    </div>
                    <h3 class="checkout-sep">{{trans('localize.shipping_information')}}</h3>
                    <div class="box-border">
                        <div class="row">
                            <div class="form-group col-xs-12 {{ $errors->has('name') ? 'has-error' : '' }}">
                                <label for="name">{{trans('localize.name')}}</label>
                                <input class="input form-control" type="text" name="name" id="name" value="{{old('name')}}">
                                <span class="bar"></span>
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div><!--/ [col] -->
                            <div class="form-group col-xs-12 {{ $errors->has('address_1') ? 'has-error' : '' }}">
                                <label for="address_1">{{trans('localize.address1')}}</label>
                                <input class="input form-control" type="text" name="address_1" id="address_1" value="{{old('address_1')}}">
                                <span class="bar"></span>
                                @if ($errors->has('address_1'))
                                    <span class="help-block">
                                        {{ $errors->first('address_1') }}
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-xs-12 {{ $errors->has('address_2') ? 'has-error' : '' }}">
                                <label for="address_2">{{trans('localize.address2')}}</label>
                                <input class="input form-control" type="text" name="address_2" id="address_2" value="{{old('address_2')}}">
                                <span class="bar"></span>
                                @if ($errors->has('address_2'))
                                    <span class="help-block">
                                        {{ $errors->first('address_2') }}
                                    </span>
                                @endif
                            </div>
<<<<<<< HEAD
=======
                            <div class="form-group col-xs-6 {{ $errors->has('city') ? 'has-error' : '' }}">
                                <label for="city" class="required">{{trans('localize.city')}}</label>
                                <div class="custom_select">
                                    <select class="input form-control" name="city">
                                        <option value="">-- Please Select City --</option>
                                        @foreach($cities as $key => $city)
                                            <option value="{{$city->ci_id}}">{{$city->ci_name}}</option>
                                        @endforeach
                                    </select>
                                <span class="bar"></span>
                                </div>
                                @if ($errors->has('city'))
                                    <span class="help-block">
                                        {{ $errors->first('city') }}
                                    </span>
                                @endif
                            </div>
>>>>>>> mobile-interface
                            <div class="form-group col-xs-6 {{ $errors->has('country') ? 'has-error' : '' }}">
                                <label>{{trans('localize.country')}}</label>
                                <div class="custom_select">
                                    <select class="input form-control" name="country" id="country">
                                        <option value="0">@lang('localize.selectCountry')</option>
                                        @foreach($countries as $key => $country)
                                            <option value="{{$country->co_id}}">{{$country->co_name}}</option>
                                        @endforeach
                                    </select>
                                <span class="bar"></span>
                                </div>
                                @if ($errors->has('country'))
                                    <span class="help-block">
                                        {{ $errors->first('country') }}
                                    </span>
                                @endif
                            </div>
<<<<<<< HEAD

=======
                            <div class="form-group col-sm-6">
                                <label for="postal_code" class="required">{{trans('localize.zipcode')}}</label>
                                <input class="input form-control" type="text" name="postal_code" id="postal_code" value="{{old('postal_code')}}">
                                <span class="bar"></span>
                            </div>
>>>>>>> mobile-interface
                            <div class="form-group col-sm-6 {{ $errors->has('telephone') ? 'has-error' : '' }}">
                                <label for="telephone">{{trans('localize.phone')}}</label>
                                <input class="input form-control" type="text" name="telephone" id="telephone" value="{{old('telephone')}}">
                                <span class="bar"></span>
                                @if ($errors->has('telephone'))
                                    <span class="help-block">
                                        {{ $errors->first('telephone') }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-xs-6 {{ $errors->has('state') ? 'has-error' : '' }}">
                                <label for="city">{{trans('localize.state')}}</label>
                                <div class="custom_select">
                                    <select class="input form-control" name="state" id="state">
                                        <option value="0">@lang('localize.selectCountry_first')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="postal_code">{{trans('localize.zipcode')}}</label>
                                <input class="input form-control" type="text" name="postal_code" id="postal_code" value="{{old('postal_code')}}">
                            </div>

                            <div class="form-group col-sm-6 {{ $errors->has('city') ? 'has-error' : '' }}">
                                <label for="telephone">{{trans('localize.city')}}</label>
                                <input class="input form-control" type="text" name="city" id="city" value="{{old('city')}}">
                                @if ($errors->has('city'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('city') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h3 class="checkout-sep">{{trans('localize.winning_item')}}</h3>
                    <div class="box-border">
                        <div class="table-responsive">
                            <table class="table table-bordered cart_summary">
                                <thead>
                                <tr>
                                    <th class="cart_product text-center">{{trans('localize.product')}}</th>
                                    <th class="cart_product">{{trans('localize.productName')}}</th>
                                    <th class="text-center">{{trans('localize.quantity')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="cart_product">
                                            <a href="#">
                                                <?php $img = explode('/**/', $checkout->auc_image); ?>
                                                <img class="img-responsive" alt="product" src="/web/images/loading.gif" data-src="{{env('IMAGE_DIR').'/auction/'.$img[0]}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';"/>
                                            </a>
                                        </td>
                                        <td class="cart_description">
                                            <p class="product-name"><a href="#">{{$checkout->title}}</a></p>
                                        </td>
                                        <td class="qty">1</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="btn-place-order">
                            <input type="hidden" name="auc_id" value="{{$checkout->auc_id}}"/>
                            <input type="hidden" name="cus_id" value="{{\Auth::user()->cus_id}}"/>
                            <input type="hidden" name="cus_name" value="{{\Auth::user()->cus_name}}"/>
                            <button class="button pull-right" type="submit">{{trans('localize.checkout')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
@stop

@section('script')
<script src="/backend/js/custom.js"></script>
    <script>
        $(document).ready(function() {
             $('#country').change(function() {
                if($('#country').val() != "0") {
                    var update_input = '#state';
                    var country_id = $(this).val();

                    load_state(update_input, country_id);
                }

            });

            $('input[type=radio][name=shipping_addr]').change(function() {
                if (this.value == 'yes') {
                    var det = $(this).attr('data-val');
                    @if($shipping)
                        $('#name').val((det == 1) ? '{{$shipping->ship_name}}' : '');
                        $('#address_1').val((det == 1) ? '{{$shipping->ship_address1}}' : '');
                        $('#address_2').val((det == 1) ? '{{$shipping->ship_address2}}' : '');
                        $('#postal_code').val((det == 1) ? '{{$shipping->ship_postalcode}}' : '');
                        $('#telephone').val((det == 1) ? '{{$shipping->ship_phone}}' : '');
                        $('#city').val((det == 1) ? '{{$shipping->ship_city_name}}' : '');
                        $('select[name=country]').val((det == 1) ? '{{$shipping->ship_country}}' : '');
                        load_state('#state', {{$shipping->ship_country}}, '{{($shipping->ship_state_id !== 0) ? $shipping->ship_state_id : ''}}');
                    @else
                        $('#shipping_addr_2rad').prop('checked', true);
                        $('#shipping_addr_1rad').prop('checked', false);
                        swal("Sorry!", "No Address Details In The Profile To Load.", "warning");
                    @endif
                }
                if (this.value == 'no') {
                    $('#checkout').trigger("reset");
                    $('#country').val("0");
                    $('#state').empty();
                    $('#state').append('<option value="0" selected>@lang('localize.selectCountry_first')</option>');
                }
            });
        });
    </script>
@endsection
