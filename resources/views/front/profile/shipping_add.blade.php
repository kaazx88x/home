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
        <form id="customer-shipping" action="{{ url('/profile/shippingaddress/add') }}" method="POST" accept-charset="UTF-8">
            {{ csrf_field() }}

            <div class="form-general">
                <h4 class="form-title">{{trans('localize.addnewshipping')}}</h4>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.address')}}</label>
                            <input type="text" class="form-control" name="address1" value="{{old('address1')}}">
                            <input type="text" class="form-control" name="address2" value="{{old('address2')}}">
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
                            <input type="text" class="form-control" id="city" name="city_name" placeholder="@lang('localize.city')" value="{{ old('city_name') }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.zipcode')}}</label>
                            <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="@lang('localize.zipcode')" value="{{ old('zipcode') }}">
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
                                    <input class="form-control phone number compulsary" id="phone" name="phone" placeholder="{{trans('localize.phoneInput')}}" value="{{ old('phone') }}">
                                    <span class="mobilehint hint"><i class="fa fa-info-circle"></i> <span class="mobilehint-text">@lang('localize.mobileHint')</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">{{trans('localize.name')}}</label>
                            <input type="text" class="form-control" name="ship_name" value="{{ old('ship_name') }}">
                        </div>
                    </div>
                </div>

                <label class="checkbox">
                    <input type="checkbox" name="isdefault" value="1" id="default">
                    <ins></ins>
                    <span>{{trans('localize.addressdefault')}}</span>
                </label>

                <button class="btn btn-block btn-primary">{{trans('localize.addnewshipping')}}</button>
                </form>
            </div>

        </div>

    </div>
@endsection

@section('scripts')
<script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function() {

        get_countries('#country', "{{ old('country', '0') }}", '#state', "{{ old('state', '0') }}");
        get_phoneAreacode('#areacode', "{{ old('areacode', '0') }}");

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