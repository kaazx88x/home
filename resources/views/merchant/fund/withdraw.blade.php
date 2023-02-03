@extends('merchant.layouts.master')

@section('title', 'Fund Withdraw')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.withdraw_fund_request')}}</h2>
        <ol class="breadcrumb">
            <li>
                {{trans('localize.fund')}}
            </li>
            <li class="active">
                <strong>{{trans('localize.withdraw_fund_request')}}</strong>
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
             @include('merchant.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{trans('localize.withdraw_fund_request')}}</h5>
                    <div class="ibox-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">{{trans('localize.view_bank_info')}}</button>
                    </div>
                </div>
                <div class="ibox-content">
                    @include('merchant.common.errors')

                    @if ($requested && count($errors) == 0)
                        <div class="alert alert-danger">
                            <strong>{{trans('localize.whoopsomethingwhenwrong')}}</strong><br>
                            <ul>
                                <li>{{trans('localize.alreadywithdraw')}}.</li>
                            </ul>
                        </div>
                    @endif
                    <?php
                        // $commission = ($merchant->mer_type != 1)? (floatval($commission_amt) / 100) : 0;
                        $commission = 0;
                        $admin_commission = floatval($vt_balance) * $commission;
                        $max_withdrawal = floatval($vt_balance) - $admin_commission;
                    ?>
                    <form class="form-horizontal" action="{{ url('merchant/fund/withdraw') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                        <label class="col-lg-3 control-label">{{trans('localize.total_balance_of_vcoin')}}</label>
                            <div class="col-lg-9">
                                <label class="control-label">{{number_format($vt_balance,4)}}</label>
                                <img src="{{url('/assets/images/icon/icon_meicredit.png')}}" alt="" style="width:auto; height:25px;">
                            </div>
                        </div>
                        @if ($merchant->mer_type != 1)
                            <!--<div class="form-group">
                                <label class="col-lg-3 control-label">{{trans('localize.admin_commission_percentage')}}</label>
                                <div class="col-lg-9">
                                    <label class="control-label">{{$commission_amt}} %</label>
                                </div>
                            </div>-->
                        @endif
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.max_of_vcoin_withraw')}}</label>
                            <div class="col-lg-9">
                                <label class="control-label">{{number_format($max_withdrawal,4)}}</label>
                                <img src="{{url('/assets/images/icon/icon_meicredit.png')}}" alt="" style="width:auto; height:25px;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{trans('localize.total_vcoin_withdraw')}}</label>
                            <div class="col-lg-9">
                                <input type="number" class="form-control" id="pay" name="pay" step="0.0001">
                                <input type="hidden" id="total_withdraw" value="{{ $max_withdrawal }}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">@lang('localize.statement_reference')</label>
                            <div class="col-lg-9">
                                <input type="file" class="form-control files" name="file" id="file">
                                <hr>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="col-lg-9">
                                <label id="balVal" class="control-label">0.00</label>
                                <label class="control-label">{{trans('localize.total_balance_of_vcoin_after_withdrawal')}}</label>
                            </div>
                        </div>
                        @if ($merchant->mer_type != 1)
                            <!--<div class="form-group">
                                <label class="col-lg-3 control-label"></label>
                                <div class="col-lg-9">
                                    <label id="adminVal" class="control-label">0.00</label>
                                    <label class="control-label">{{trans('localize.total_of_admin_commision')}}</label>
                                </div>
                            </div>-->
                        @endif
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="col-lg-9">
                                <label id="merVal" class="control-label">0.00</label>
                                <label class="control-label">{{trans('localize.total_of_vcoin_withdraw')}}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"></label>
                            <div class="col-lg-9">
                              <label id="rateVal" class="control-label">0.00</label>
                              <labelclass="control-label">{{trans('localize.total_withdrawal')}} ({{ $merchant->co_curcode }} )</label>
                            </div>
                        </div>
                        @if (!$requested)
                            <div class="form-group">
                                <div class="col-lg-2 pull-right">
                                    <button class="btn btn-block btn-primary" type="submit" id="total_to_pay_submit">{{trans('localize.submit')}}</button>
                                </div>
                            </div>
                            <hr/>
                            <?PHP
                                $locale = App::getLocale();
                            ?>
                            <div class="alert alert-info" role="alert">
                            <strong>Disclaimer:</strong>
                            <br/>You can submit a fund withdrawal request after buyer confirming on shipment receiving.
                            <br/>You shall receive your payment after 5 days of the fund request.
                            <br/>If you request is made after 17:00 (GMT +8) on Friday, your request will be processed on coming Monday.
                            @if (App::isLocale('cn'))
                            <hr/>
                            <strong>备注：</strong>
                            <br/>您必须在买家确认收货后申请提款。
                            <br/>您将在5天后收到您的资金。如果您在星期五17:00 (GMT +8) 后提款，您的请求将在星期一进行处理。
                            @endif
                            </div>
                        @endif
                        <input type="hidden" class="form-control" value="{{ $vt_balance }}" id="vt">
                        <input type="hidden" class="form-control" value="{{ $commission }}" id="commission">
                        <input type="hidden" class="form-control" id="admin_comm">
                        <input type="hidden" class="form-control" id="wd_balance">
                        <input type="hidden" class="form-control" value="{{ $merchant->co_curcode }}" id="wd_currency">
                        <input type="hidden" class="form-control" value="{{ ($merchant->mer_type == 1) ? $merchant->co_offline_rate :$merchant->co_rate }}" id="wd_rate">
                        <input type="hidden" class="form-control" value="{{ $merchant->mer_type }}" id="mer_type">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{trans('localize.bank_information')}}</h4>
            </div>
            <div class="modal-body">
                <div class="row form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">{{trans('localize.account_holder_name')}}</label>
                        <div class="col-sm-7">
                             <p class="form-control-static">{{ $merchant->bank_acc_name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label">{{trans('localize.account_number')}}</label>
                        <div class="col-sm-7">
                             <p class="form-control-static">{{ $merchant->bank_acc_no}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label">{{trans('localize.name_of_bank')}}</label>
                        <div class="col-sm-7">
                             <p class="form-control-static">{{ $merchant->bank_name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label">{{trans('localize.country_of_bank')}}</label>
                        <div class="col-sm-7">
                             <p class="form-control-static">{{ $merchant->co_name}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label">{{trans('localize.address_of_bank')}}</label>
                        <div class="col-sm-7">
                             <p class="form-control-static">{{ $merchant->bank_address}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label">{{trans('localize.Bank_swift_code_BIC_ABA')}}</label>
                        <div class="col-sm-7">
                             <p class="form-control-static">{{ $merchant->bank_swift}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label">{{trans('localize.if_europe_bank_IBAN')}}</label>
                        <div class="col-sm-7">
                             <p class="form-control-static">{{ $merchant->bank_europe}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('localize.closebtn')}}</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('style')
<link href="/backend/lib/wysiwyg/wysihtml5.min.css" rel="stylesheet">
@endsection

@section('script')
<!-- plugins -->
<script src="/backend/lib/wysiwyg/wysihtml5x-toolbar.min.js"></script>
<script src="/backend/lib/wysiwyg/handlebars.runtime.min.js"></script>
<script src="/backend/lib/wysiwyg/wysihtml5.min.js"></script>

<script>
	$(document).ready(function() {

        $('.files').change(function() {
            var fileExtension = ['jpeg', 'jpg', 'png', 'pdf'];

            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                swal("@lang('localize.error')", "@lang('localize.file.error.type', ['values' => 'jpeg, jpg, png, pdf'])", "error");
                $(this).css('border', '1px solid red').focus().val('');
            } else if (($(this)[0].files[0].size) > 2000000){
                swal("@lang('localize.error')", "@lang('localize.file.error.maximum_size')", "error");
                $(this).css('border', '1px solid red').focus().val('');
            }
        });

        $('#pay').keypress(function (evt) {
            return isNumber(event, this)
        });

        $('#pay').keyup(function (e) {
            var mer_type = $('#mer_type').val();

            var vt = $('#vt').val();
            var pay = $('#pay').val();
            var withdraw = $('#total_withdraw').val();
            var commission = $('#commission').val();
            var rate = $('#wd_rate').val();
            var admin_commission = (pay * commission).toFixed(4);
            var total = (parseFloat(vt) - (parseFloat(pay) + parseFloat(admin_commission))).toFixed(4);
            var wdcomm = (withdraw * commission).toFixed(4);
            var wdtotal = (parseFloat(vt) - (parseFloat(withdraw) + parseFloat(wdcomm))).toFixed(4);
            var rateTotal = (pay * rate).toFixed(4);
            var wdRate = (withdraw * rate).toFixed(4);


            if( parseFloat(pay) > parseFloat(withdraw)) {
                $('#pay').val(withdraw);
                $('#merVal').html(isNaN(withdraw) ? '0.00' : withdraw);
                $('#adminVal').html(isNaN(wdcomm) ? '0.00' : wdcomm);
                $('#balVal').html(isNaN(wdtotal) ? '0.00' : wdtotal);
                $('#rateVal').html(isNaN(wdRate) ? '0.00' : wdRate);
            } else {
                $('#merVal').html(isNaN(pay) || pay == '' ? '0.00' : pay);
                $('#adminVal').html(isNaN(admin_commission) ? '0.00' : admin_commission);
                $('#admin_comm').val(admin_commission);
                $('#balVal').html(isNaN(total) ? '0.00' : total);
                $('#wd_balance').val(total);
                $('#rateVal').html(isNaN(rateTotal) ? '0.00' : rateTotal);
            }
        });

		$('#total_to_pay_submit').click(function() {

            if(isNaN($('#pay').val()) || !$('#pay').val() || parseFloat($('#pay').val()) > parseFloat($('#total_withdraw').val()) || $('#pay').val() <= 0)
			{
                $('#pay').css('border', '1px solid red').focus();
				return false;
			}
            $('#pay').css('border', '');

            var fileExtension = ['jpeg', 'jpg', 'png', 'pdf'];
            if(!$('#file').val()) {
                $('#file').css('border', '1px solid red').focus();
				return false;
            }

            if ($.inArray($('#file').val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                swal("@lang('localize.error')", "@lang('localize.file.error.type', ['values' => 'jpeg, jpg, png, pdf'])", "error");
                $('#file').css('border', '1px solid red').focus();
                return false;
            } else if (($('#file')[0].files[0].size) > 2000000){
                swal("@lang('localize.error')", "@lang('localize.file.error.maximum_size')", "error");
                $('#file').css('border', '1px solid red').focus();
                return false;
            }

            $('#spinner').show();
		});
	});

    function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            //(charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }
</script>
@endsection
