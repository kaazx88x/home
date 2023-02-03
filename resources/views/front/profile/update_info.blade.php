@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
<div class="ListingTopbar">
    <h4 class="ListingCategory">@lang('localize.updateAccount')</h4>
</div>

<div class="ContentWrapper">
    <div class="login-selection">
        <div class="alert alert-danger">
            @lang('localize.required_before_shopping')
        </div>
    </div>

    @include('layouts.partials.status')

    <form id="update_info" role="form" method="POST" action="{{ url('profile/update') }}">
        {{ csrf_field() }}

        <div class="form-group">
            <label class="control-label">@lang('localize.name_as_ic')</label>
            <input type="text" name="cus_name" id="cus_name" class="form-control compulsary" placeholder="@lang('localize.name_error')" value="{{ old('cus_name') }}" autocorrect="off" autocapitalize="off" autofocus="" required>
        </div>

        <div class="form-group">
            <label class="control-label">@lang('localize.ic_number')</label>
            <input type="text" name="identity_card" id="identity_card" class="form-control compulsary" placeholder="@lang('localize.ic_error')" value="{{ old('identity_card') }}" autocorrect="off" autocapitalize="off" autofocus="" required>
        </div>

        <div class="form-group">
            <label class="control-label">@lang('localize.address') <span style="color:red;">*</span></label>
            <input type="text" class="form-control compulsary" name="address1" placeholder="@lang('localize.address1Input')" value="{{ old('address1') }}">
            <input type="text" class="form-control" name="address2" placeholder="@lang('localize.address2Input')" value="{{ old('address2') }}">
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label">@lang('localize.country') <span style="color:red;">*</span></label>
                    <select class="form-control compulsary input" id="country" name="country">
                    <option value="">@lang('localize.selectCountry')</option>
                    @foreach($countries as $key => $country)
                        <option value="{{ $country->co_id }}"{{ (old('country') == $country->co_id) ? ' selected' : '' }}>{{ $country->co_name }}</option>
                    @endforeach
                </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label">@lang('localize.state') <span style="color:red;">*</span></label>
                    <select class="form-control compulsary input" id="state" name="state">
                    <option value="">@lang('localize.selectState')</option>
                </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label">@lang('localize.zipcode') <span style="color:red;">*</span></label>
                    <input type="text" class="form-control compulsary" id="zipcode" name="zipcode" placeholder="@lang('localize.zipcode')" value="{{ old('zipcode') }}">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label">@lang('localize.city') <span style="color:red;">*</span></label>
                    <input type="text" class="form-control compulsary" id="city" name="city" value="{{ old('city') }}">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="checkbox">
                <input type="checkbox" name="declaration" value="1" id="declaration" class="form-control  compulsary" required>
                <ins></ins>
                <span style="font-size:14px;">{{trans('localize.declare_correct_info')}}</span>
            </label>
        </div>

        <button type="button" class="btn btn-primary btn-block" id="update_submit">@lang('localize.update')</button>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        $('#country').change(function() {
            var update_input = '#state';
            var country_id = $(this).val();

            load_state(update_input, country_id);
        });

        $('#update_submit').click(function() {
            var isValid = true

            $(':input, checkbox').each(function(e) {
                if ($(this).hasClass('compulsary')) {
                    if (!$(this).val()) {
                        $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                        isValid = false;
                        return false;
                    }

                    if ($(this).attr('name') == 'declaration') {
                        if ($(this).prop('checked') == false){
                            swal ( "Oops" ,  "{{trans('localize.confirm_correct_info')}}" ,  "info" )
                            isValid = false;
                            return false;
                        }
                    }
                }

                $(this).css('border', '');
            });

            if (isValid) {
                $('#update_info').submit();
            }
        });
    });
</script>
@endsection
