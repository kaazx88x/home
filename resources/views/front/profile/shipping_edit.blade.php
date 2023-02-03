@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
    <div class="ListingTopbar">
        <h4 class="ListingCategory">{{trans('localize.myaddress')}}</h4>
        <a href="{{ url('/profile/shippingaddress') }}" class="back"><i class="fa fa-angle-left"></i></a>
    </div>
    <div class="ContentWrapper">

        @include('layouts.partials.status')

        <div class="panel-general">
        <form id="customer-shipping" action="{{ url('/profile/shippingaddress/edit', [$shipping->ship_id]) }}" method="POST" accept-charset="UTF-8">
            {{ csrf_field() }}

            <div class="form-general">
                <h4 class="form-title">{{trans('localize.updateshipping')}}</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.address')}}</label>
                            <input type="text" class="form-control" name="address1" placeholder="@lang('localize.address1Input')" value="{{ old('address1', $shipping->ship_address1) }}">
                            <input type="text" class="form-control" name="address2" placeholder="@lang('localize.address2Input')" value="{{ old('address2', $shipping->ship_address2) }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.country')}}</label>
                            <select class="form-control input compulsary" id="country" name="country" onchange="get_states('#state', this.value)"></select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.state')}}</label>
                            <select class="form-control input compulsary" id="state" name="state">
                                <option val="">@lang('localize.selectState')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.city')}}</label>
                            <input type="text" class="form-control" id="city" id="city" name="city_name" placeholder="@lang('localize.city')" value="{{ old('city_name', $shipping->ship_city_name) }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.zipcode')}}</label>
                            <input type="text" class="form-control" id="zipcode" id="zipcode" name="zipcode" placeholder="@lang('localize.zipcode')" value="{{ old('zipcode', $shipping->ship_postalcode) }}">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">@lang('localize.phone')</label>
                            <div class="row">
                                <div class="col-sm-4">
                                    <select class="form-control compulsary" name="areacode" id="areacode"></select>
                                </div>

                                <div class="col-sm-8">
                                    <input class="form-control phone number compulsary" id="phone" name="phone" placeholder="{{trans('localize.phoneInput')}}" value="{{ old('phone', $shipping->ship_phone) }}">
                                    <span class="mobilehint hint" style="display:{{ $shipping && $shipping->areacode == 60? 'block' : 'none' }};"><i class="fa fa-info-circle"></i> <span class="mobilehint-text">@lang('localize.mobileHint')</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.name')}}</label>
                            <input type="text" class="form-control" name="ship_name" value="{{ old('ship_name', $shipping->ship_name)}}">
                        </div>
                    </div>
                </div>

                <label class="checkbox">
                    <input type="checkbox" name="isdefault" value="1" id="default" {{ $shipping->isdefault || old('isdefault') == 1 ? 'checked' : ''}}>
                    <ins></ins>
                    <span>{{trans('localize.addressdefault')}}</span>
                </label>
                <input type="hidden" name="ship_id" value="{{$shipping->ship_id}}">
                <button class="btn btn-block btn-primary">{{trans('localize.updateshipping')}}</button>
                </form>
            </div>

        </div>

    </div>
@endsection

@section('scripts')
<script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function() {

        get_countries('#country', "{{ old('country', (($shipping) && $shipping->ship_country) ? $shipping->ship_country : '0') }}", '#state', "{{ old('state', (($shipping) && $shipping->ship_state_id) ? $shipping->ship_state_id : '0') }}");
        get_phoneAreacode('#areacode', "{{ old('areacode', (($shipping) && $shipping->areacode) ? $shipping->areacode : '0') }}");

        $('#areacode').change(function() {
            var areacode = $(this).val();

            if (areacode == 60 || areacode == 886) {
                if(areacode == 60)
                    $('.mobilehint-text').text("@lang('localize.mobileHint-areacode.60')");

                if(areacode == 886)
                    $('.mobilehint-text').text("@lang('localize.mobileHint-areacode.886')");

                $('.mobilehint').show();
            } else {
                $('.mobilehint').hide();
            }
        });

        $('#phone,#zipcode').keydown(function (e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) {
                e.preventDefault();
            } else {
                var key = e.keyCode;
                if (!((key == 8) || (key == 46) || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
                    e.preventDefault();
                }
            }
        });

        $('#update-shipping').click(function() {
            var isValid = true

            $(':input, select').each(function(e) {
                if ($(this).hasClass('compulsary')) {
                    console.log($(this).attr('name'));
                    if (!$(this).val()) {
                        $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                        isValid = false;
                        return false;
                    }
                }

                $(this).css('border', '');
            });

            if (isValid) {
                $('#customer-shipping').submit();
            }
        });
    });

</script>

@endsection