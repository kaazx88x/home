@extends('admin.layouts.master')

@section('title', 'Add Customer')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Add_New_Customer')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="">{{trans('localize.customer')}}</a>
            </li>
            <li class="active">
                <strong>{{trans('localize.Add_Customer')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    @include('admin.common.notifications')

    <form id="customer-add" action='/admin/customer/add' method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{trans('localize.customer')}} {{trans('localize.information')}}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label class="control-label">{{trans('localize.Name')}} <span style="color:red;">*</span></label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <select class="form-control" name="cus_title" style="width:100px;">
                                    <option value="">--</option>
                                    @foreach($select['person_titles'] as $id => $title)
                                    <option value="{{ $id }}" {{ (old('cus_title') == $id) ? 'selected' : '' }}>{{ $title }}</option>
                                    @endforeach
                                </select>
                            </span>
                            <input type="text" class="form-control compulsary" name='name' value="{{ old('name') }}">
                        </div>
                    </div>

                    {{--  <div class="form-group">
                        <label class="control-label">Username <span style="color:red;">*</span></label>
                        <input type="text" class="form-control compulsary" id='username' name='username' value="{{ old('username') }}">
                    </div>  --}}

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.email')}}</label>
                        <input type="text" class="form-control" id='email' name='email' value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.Phone')}} <span style="color:red;">*</span></label>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select class="form-control compulsary" name="areacode" id="areacode"></select>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <input class="form-control phone number compulsary" id="phone" name="cus_phone" value="{{ old('cus_phone') }}">
                                    <span class="mobilehint hint"><i class="fa fa-info-circle"></i> @lang('localize.mobileHint')</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.address')}}</label>
                        <input type="text" class="form-control" id="cus_address1" name='cus_address1' value="{{ old('cus_address1') }}">
                        <br>
                        <input type="text" class="form-control" id="cus_address2" name='cus_address2' value="{{ old('cus_address2') }}">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.zipcode')}}</label>
                        <input type="text" class="form-control" name='cus_postalcode' value="{{ old('cus_postalcode') }}">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.country')}}</label>
                        <select class="form-control" id="cus_country" name="cus_country" onchange="get_states('#cus_state', this.value)"></select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.State')}}</label>
                        <select class="form-control" id="cus_state" name="cus_state">
                            <option value="">{{ trans('localize.selectCountry_first') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{trans('localize.city')}}</label>
                        <input type="text" class="form-control" name='cus_city' value="{{ old('cus_city') }}" >
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{trans('localize.other')}} {{trans('localize.information')}}</h5>
                </div>
                <div class="ibox-content form-horizontal" id="box1">
                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.job')</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="cus_job">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['jobs'] as $id => $title)
                                <option value="{{ $id }}" {{ (old('cus_job') == $id)? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.monthly_incomes')</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="cus_incomes">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['incomes'] as $id => $title)
                                <option value="{{ $id }}" {{ (old('cus_incomes') == $id)? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.education')</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="cus_education">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['educations'] as $id => $title)
                                <option value="{{ $id }}" {{ (old('cus_education') == $id)? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.gender')</label>
                        <div class="col-lg-9 i-checks">
                            <label class="col-lg-4 control-label"><input type="radio" name="cus_gender" value="1" {{ ( old('cus_gender') == 1)? ' checked' : 'checked' }}>&nbsp;&nbsp; {{trans('localize.male')}}</label>
                            <label class="col-lg-4 control-label"><input type="radio" name="cus_gender" value="2" {{ ( old('cus_gender') == 2)? ' checked' : '' }}>&nbsp;&nbsp; {{trans('localize.female')}}</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.dob')</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <span class="input-group date">
                                    <input type="text" class="form-control" name="cus_dob" id="dob" style="width:100%;" readonly value="{{ old('cus_dob') }}"/>
                                    <span class="input-group-addon open-calendar"><i class="fa fa-calendar open-calendar"></i></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.nationality')</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="cus_nationality">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['nationality'] as $id => $title)
                                <option value="{{ $id }}" {{ (old('cus_nationality') == $id)? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.race')</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="cus_race">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['races'] as $id => $title)
                                <option value="{{ $id }}" {{ (old('cus_race') == $id)? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.religion')</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="cus_religion">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['religions'] as $id => $title)
                                <option value="{{ $id }}" {{ (old('cus_religion') == $id)? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.marital_status')</label>
                        <div class="col-lg-9">
                            <select class="form-control" name="cus_marital">
                                <option value="">@lang('localize.selectOption')</option>
                                @foreach($select['marital'] as $id => $title)
                                <option value="{{ $id }}" {{ (old('cus_marital') == $id)? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.no_of_children')</label>
                        <div class="col-lg-9">
                            <input type="number" class="form-control" name="cus_children" value="{{ old('cus_children', 0) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label">@lang('localize.hobby')</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" name="cus_hobby" value="{{ old('cus_hobby') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-lg-2 pull-right">
                    <button id="update-info" type="button" class="btn btn-block btn-primary">Add Customer</button><br/>
                </div>
                <div class="col-lg-2 pull-right">
                    <button class="btn btn-block btn-default" type="reset">Reset Form</button>
                </div>
            </div>
        </div>
    </div>

    {{--  <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{trans('localize.Shipping')}} {{trans('localize.information')}}</h5><br/><br/>
                    <input type="checkbox" name="copy" id="copyCheck" class="i-checks">
					{{trans('localize.Check_this_box_if_Billing_Address_and_Mailing_Address_are_the_same')}}
                </div>
                <div class="ibox-content" id="box2">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.Phone')}}</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" id="phone" name="phone"  value="{{old("phone")}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.address')}}</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" id="address1" name='address1' value='{{old("address1")}}'>
                                <br>
                                <input type="text" class="form-control" id="address2" name='address2' value='{{old("address2")}}' >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.zipcode')}}</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name='zipcode' value='{{old("zipcode")}}' >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.country')}}</label>
                            <div class="col-lg-9">
                                <select class="form-control" id="country" name="country">
                                    <option value="">{{trans('localize.selectCountry')}}</option>
                                    @foreach($countries as $key => $co)
                                    <option value="{{$co->co_id}}" {{ ( old('country') == $co->co_id)? 'selected' : '' }}>{{$co->co_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.State')}}</label>
                            <div class="col-lg-9">
                                <select class="form-control" id="state" name="state">
                                    <option value="">{{trans('localize.selectCountry_first')}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.city')}}</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name='city_name' value='{{old("city_name")}}' >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-lg-2 pull-right">
                    <input id="update-info" type="button" class="btn btn-block btn-primary" value="{{trans('localize.Add_Customer')}}"><br/>
                </div>
                <div class="col-lg-2 pull-right">
                    <button class="btn btn-block btn-default" type="reset">{{trans('localize.reset_form')}}</button>
                </div>
            </div>
        </div>
    </div>  --}}
    <br>
    </form>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/backend/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
<link href="/backend/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script src="/backend/js/plugins/datapicker/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/backend/js/custom.js"></script>

<script>
    $(function() {

        get_countries('#cus_country', "{{ old('cus_country', '0') }}", '#cus_state', "{{ old('cus_state', '0') }}");
        get_phoneAreacode('#areacode', "{{ old('areacode', '0') }}");

        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        {{--  $("#username").on({
            keydown: function(e) {
                if (e.which === 32)
                return false;
            },
            change: function(e) {
                this.value = this.value.replace(/\s/g, "");
                check_member_username($(this), $(this).val(), '', e);
            }
        });  --}}

        $("#email").on({
            keydown: function(e) {
                if (e.which === 32)
                return false;
            },
            change: function(e) {
                this.value = this.value.replace(/\s/g, "");
                if(this.value.length != 0)
                    check_member_email($(this), $(this).val(), '', e);
            }
        });

        $("#phone").on({
            keydown: function(e) {
                if (e.which === 32)
                return false;
            },
            change: function(e) {
                this.value = this.value.replace(/\s/g, "");
                check_member_phone($(this), $(this).val(), '', e);
            }
        });

        $('#areacode').change(function() {
            var areacode = $(this).val();

            if (areacode == 60) {
                $('.mobilehint').show();
            } else {
                $('.mobilehint').hide();
            }
        });

        $('#copyCheck').on('ifToggled', function(event) {
            if(this.checked == true) {
                bindGroups();
            }
        });

        $('#dob').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            format: 'dd/mm/yyyy',
            autoclose: true,
            forceParse: false,
            Default: true,
            todayHighlight: true,
        });

        $('.open-calendar').click(function(event){
            event.preventDefault();
            $('#dob').focus();
        });

        $('#update-info').click(function() {
            var isValid = true
            
            $(':input, select').each(function(e) {
                if ($(this).hasClass('compulsary')) {
                    if (!$(this).val()) {
                        $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                        isValid = false;
                        return false;
                    }
                }

                $(this).css('border', '');
            });

            if (isValid) {
                $('#customer-add').submit();
            }
        });
    });

    var bindGroups = function() {
        // First copy values
        $("input[name='phone']").val($("input[name='cus_phone']").val());
        $("input[name='address1']").val($("input[name='cus_address1']").val());
        $("input[name='address2']").val($("input[name='cus_address2']").val());
        $("input[name='city_name']").val($("input[name='cus_city']").val());
        $("#country").val($("#cus_country").val());
        get_states('#state', $("#cus_country").val(), $("#cus_state option:selected").val());
        $("input[name='zipcode']").val($("input[name='cus_postalcode']").val());

        // Then bind fields
        $("input[name='cus_phone']").keyup(function() {
            $("input[name='phone']").val($(this).val());
        });
        $("input[name='cus_address1']").keyup(function() {
            $("input[name='address1']").val($(this).val());
        });
        $("input[name='cus_address2']").keyup(function() {
            $("input[name='address2']").val($(this).val());
        });
        $("input[name='cus_city']").keyup(function() {
            $("input[name='city_name']").val($(this).val());
        });
        $("input[name='cus_postalcode']").keyup(function() {
            $("input[name='zipcode']").val($(this).val());
        });
        $("#cus_country").keyup(function() {
            $("#country").val($(this).val());
        });
        $("#cus_state").keyup(function() {
            $("#state").val($(this).val());
        });
    };
</script>

@endsection
