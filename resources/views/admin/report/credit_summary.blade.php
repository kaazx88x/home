@extends('admin.layouts.master')

@section('title', trans('localize.credit_summary'))

@section('content')
	<div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>@lang('localize.credit_summary')</h2>
            <ol class="breadcrumb">
                <li>
                @lang('localize.report')
                </li>
                <li class="active">
                    <strong>@lang('localize.credit_summary')</strong>
                </li>
            </ol>
        </div>
    </div>

	<div class="wrapper wrapper-content animated fadeInRight ecommerce">
        <div class="ibox float-e-margins border-bottom">
       		<a class="collapse-link nolinkcolor">
				<div class="ibox-title ibox-title-filter">
					<h5>@lang('localize.Search_Filter')</h5>
					<div class="ibox-tools">
						<i class="fa fa-chevron-down"></i>
					</div>
				</div>
			</a>
			<div class="ibox-content ibox-content-filter" style="display:block;">
				<div class="row">
					<form class="form-horizontal" id="filter" action='/admin/report/credit-summary' method="GET">
						<div class="form-group">
							<label class="col-sm-2 control-label">{{trans('localize.user_type')}}</label>
							<div class="col-sm-9">
							<select class="form-control" id="user_type" name="user_type" onchange='search_filter();'>
								@foreach ($user_type as $key => $usertype)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['user_type']) ? 'selected' : '' }}>{{ $usertype }}</option>
                                @endforeach
							</select>
							</div>
						</div>

						<div class="form-group" id='user_transaction_type_div' name='user_transaction_type_div' >
							<label class="col-sm-2 control-label">{{trans('localize.user_transaction_type')}}</label>
							<div class="col-sm-9">
							<select class="form-control" id="user_transaction_type" name="user_transaction_type" >
								@foreach ($user_transaction_type as $key => $usertransactiontype)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['user_transaction_type']) ? 'selected' : '' }}>{{ $usertransactiontype }}</option>
                                @endforeach
							</select>
							</div>
						</div>

						<div class="form-group" id='merchant_transaction_type_div' name='merchant_transaction_type_div' hidden>
							<label class="col-sm-2 control-label">{{trans('localize.merchant_transaction_type')}}</label>
							<div class="col-sm-9">
							<select class="form-control" id="merchant_transaction_type" name="merchant_transaction_type" >
								@foreach ($merchant_transaction_type as $key => $merchanttransactiontype)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['merchant_transaction_type']) ? 'selected' : '' }}>{{ $merchanttransactiontype }}</option>
                                @endforeach
							</select>
							</div>
						</div>

						<div class="form-group" id='report_view_div' name='report_view_div' >
							<label class="col-sm-2 control-label">{{trans('localize.report_view')}}</label>
							<div class="col-sm-9">
							<select class="form-control" id="report_view" name="report_view" onchange ='search_filter();'>
								@foreach ($report_view as $key => $reportview)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['report_view']) ? 'selected' : '' }}>{{ $reportview }}</option>
                                @endforeach
							</select>
							</div>
						</div>

						<!-- For Daily -->
						<div class="form-group" id='transaction_date_div' name='transaction_date_div' hidden>
							<label class="col-sm-2 control-label">{{trans('localize.transaction_date')}}</label>
							<div class="col-sm-9">
								<div class="input-daterange input-group">
									<input type="text" class="form-control" name="start_date" id="start_date" placeholder="{{trans('localize.startDate')}}" value="{{$input['start_date']}}"/>
									<span class="input-group-addon">{{trans('localize.to')}}</span>
									<input type="text" class="form-control" name="end_date" id="end_date" placeholder="{{trans('localize.endDate')}}" value="{{$input['end_date']}}"/>
								</div>
							</div>
						</div>

						<!-- For Monthly -->
						<div class="form-group" id='transaction_month_range_div' name='transaction_month_range_div' hidden>
							<label class="col-sm-2 control-label">{{trans('localize.transaction_month_range')}}</label>
							<div class="col-sm-9">
								<div class="input-daterange input-group">
									<input type="text" class="form-control" name="start_date_month" id="start_date_month" placeholder="{{trans('localize.start_date_month')}}" value="{{$input['start_date_month']}}"/>
									<span class="input-group-addon">{{trans('localize.to')}}</span>
									<input type="text" class="form-control" name="end_date_month" id="end_date_month" placeholder="{{trans('localize.end_date_month')}}" value="{{$input['end_date_month']}}"/>
								</div>
							</div>
						</div>

						<!-- For Yearly -->
						<div class="form-group" id='transaction_year_range_div' name='transaction_year_range_div' hidden>
							<label class="col-sm-2 control-label">{{trans('localize.transaction_year_range')}}</label>
							<div class="col-sm-9">
								<div class="input-daterange input-group">
									<input type="text" class="form-control" name="start_date_year" id="start_date_year" placeholder="{{trans('localize.start_date_year')}}" value="{{$input['start_date_year']}}"/>
									<span class="input-group-addon">{{trans('localize.to')}}</span>
									<input type="text" class="form-control" name="end_date_year" id="end_date_year" placeholder="{{trans('localize.end_date_year')}}" value="{{$input['end_date_year']}}"/>
								</div>
							</div>
						</div>

						<div class="form-group" id='country' name='country' >
                            <label class="col-sm-2 control-label">{{trans('localize.country')}}</label>
                            <div class="col-sm-9">
                                <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="country[]" value="0" {{ (isset($input['country']) && (in_array("0", $input['country']))? 'checked' : '' ) }}>&nbsp; No Country</label>&nbsp;&nbsp;
								@foreach($country as $country_val)
									<label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="country[]" value="{{ $country_val->co_id }}" {{ (isset($input['country']) && (in_array($country_val->co_id, $input['country'])) ? 'checked' : '' ) }}>&nbsp;{{ $country_val->co_name }}</label> &nbsp; &nbsp;
                                @endforeach
                            </div>
                        </div>

						<div class="form-group" id='sort_by' name='sort_by' >
                        <label class="col-sm-2 control-label">{{trans('localize.sort')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort_by" name="sort_by" style="font-family:'FontAwesome', sans-serif;">
                                <option value="trans_date_asc" {{($input['sort_by'] == "trans_date_asc" ) ? 'selected' : ''}}>{{trans('localize.transaction_date')}} : &#xf162;</option>
                                <option value="trans_date_desc" {{($input['sort_by'] == 'trans_date_desc') ? 'selected' : ''}}>{{trans('localize.transaction_date')}} : &#xf163;</option>
                                <option value="credit_asc" {{($input['sort_by'] == 'credit_asc') ? 'selected' : ''}}>{{trans('localize.credit')}} : &#xf162;</option>
                                <option value="credit_desc" {{($input['sort_by'] == 'credit_desc') ? 'selected' : ''}}>{{trans('localize.credit')}}  : &#xf163;</option>
                                <option value="debit_asc" {{($input['sort_by'] == 'debit_asc') ? 'selected' : ''}}>{{trans('localize.debit')}} : &#xf162;</option>
                                <option value="debit_desc" {{($input['sort_by'] == 'debit_desc') ? 'selected' : ''}}>{{trans('localize.debit')}}  : &#xf163;</option>

							</select>
                        </div>
						</div>

						<div class="form-group">
                        <div class="col-sm-9 col-sm-offset-2">
                            <div  class="col-sm-6">
                                <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                            </div>
                            <div  class="col-sm-6">
                                <button type="button" class="btn  btn-block btn-outline btn-primary btn-sm" href="/admin/report/credit-summary" onclick="location.href='/admin/report/credit-summary'">
                                    <i class="fa fa-share-square-o"></i>{{trans('localize.reset')}}
                                </button>
                            </div>
                        </div>
						</div>
						</div>
					</form>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					@include('admin.common.notifications')
					<div class="ibox">
					@if(isset($input['user_type']) && $input['user_type'] <> NULL)
					@if(count($logs) > 0)
						<div class="ibox-title" style="display: block;">
							<div class="ibox-tools" style="margin-bottom:10px;">
								
							   <div class="btn-group">
										@if($export_permission)
										<button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> {{trans('localize.Export_All')}} <span class="caret"></span></button>
										<ul class="dropdown-menu">
											<li><a href="/admin/export/credit_summary?action=export&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
											<li><a href="/admin/export/credit_summary?action=export&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
											<li><a href="/admin/export/credit_summary?action=export&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
										</ul>
										@endif
									</div>
							</div>
						</div>
					@endif
					@endif

					<div class="ibox-content">
					@if(isset($input['user_type']) && $input['user_type'] <> NULL)
						<div class="table-responsive">
							@if(count($logs) > 0)
								<table class="table table-stripped table-bordered">
									<thead>
										<tr>
											<th nowrap class="text-center"></th>
											<th nowrap class="text-center">{{trans('localize.date')}}</th>
											<th nowrap class="text-center">{{trans('localize.country')}}</th>
											<th nowrap class="text-center">{{trans('localize.credit')}}</th>
											<th nowrap class="text-center">{{trans('localize.debit')}}</th>
										</tr>
									</thead>

									<tbody>
										@php
											$total_credit_amount = 0;
											$total_debit_amount = 0;
											$previous_date = '';
											$previous_month = '';
											$previous_year = '';
											$previous_country = NULL;

											$collapse_id = 0;
										@endphp

										@foreach ($logs as $key => $log)
											@php
											$group_level_write1 = 0;
											$group_level_write2 = 0;

											$total_credit_amount = $total_credit_amount + $log->l2_credit_amount;
											$total_debit_amount = $total_debit_amount + $log->l2_debit_amount;

											if(isset($input['report_view']) && $input['report_view'] == 0 ){
												if($previous_date <> $log->l1_date){
													$group_level_write1 = 1;
													$group_level_write2 = 1;
												}

											}else if(isset($input['report_view']) && $input['report_view'] == 1 ){
												if($previous_year <> $log->l1_year || $previous_month <> $log->l1_month){
													$group_level_write1 = 1;
													$group_level_write2 = 1;
												}

											}else if(isset($input['report_view']) && $input['report_view'] == 2 ){
												if($previous_year <> $log->l1_year ){
													$group_level_write1 = 1;
													$group_level_write2 = 1;
												}
											}

											if($group_level_write1 == 1){
												$collapse_id = $collapse_id + 1;
											}

											if($previous_country <> $log->l2_country_id){
												$group_level_write2 = 1;
											}

											@endphp

											@if($group_level_write1 == 1)
												<tr class="text-center " bgcolor='#F2F2F2' >
													<td>
														<button style='height: 30px;' type="button" class="btn btn-primary btn-sm accordion-toggle btn_export" id="{{ $collapse_id }}" data-toggle="collapse"  data-target=".collapse_{{$collapse_id}}">
														<i class="fa fa-plus-circle" aria-hidden="true" id="btnicon-{{ $collapse_id }}"></i> <span id="btnlabel-{{ $collapse_id }}"></span>
														</button></i>
													</td>

													@if(isset($input['report_view']) && $input['report_view'] == 0 )
														<td><b> {{ date('d-m-Y',strtotime($log->l1_date)) }} </b></td>
													@elseif(isset($input['report_view']) && $input['report_view'] == 1 )
														<td><b> {{ $log->l1_year .'-'. $log->l1_month }} </b></td>
													@elseif(isset($input['report_view']) && $input['report_view'] == 2 )
														<td><b> {{ $log->l1_year  }} </b></td>
													@endif

													<td><b></b></td>
													<td align='right'><b>{{ number_format($log->l1_credit_amount,4) }}</b></td>
													<td align='right'><b>{{ number_format($log->l1_debit_amount,4) }}</b></td>
												</tr>
											@endif

											@if($group_level_write2 == 1)
												<tr class="collapse collapse_{{$collapse_id}} text-center">
													<td></td>
													<td></td>
													<td>{{ ($log->l2_country_name <> "" ? $log->l2_country_name : "No Country") }}</td>
													<td align='right'>{{ number_format($log->l2_credit_amount,4) }}</td>
													<td align='right'>{{ number_format($log->l2_debit_amount,4) }}</td>

												</tr>
											@endif

											@php

												if(isset($input['report_view']) && $input['report_view'] == 0 ){
													$previous_date = $log->l1_date;
													$previous_country = $log->l2_country_id;
												}else if(isset($input['report_view']) && $input['report_view'] == 1 ){
													$previous_year = $log->l1_year;
													$previous_month = $log->l1_month;
													$previous_country = $log->l2_country_id;
												}else if(isset($input['report_view']) && $input['report_view'] == 2 ){
													$previous_year = $log->l1_year;
													$previous_country = $log->l2_country_id;
												}
											@endphp

										@endforeach
										<tr class="text-right" bgcolor='#F2F2F2'>
											<td colspan='3'><b> Total</b></td>
											<td><b>{{ number_format($total_credit_amount,4) }}</b></td>
											<td><b>{{ number_format($total_debit_amount,4) }}</b></td>
										</tr>
									</tbody>
								</table>
							@endif
						</div>
					@endif
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection


@section('style')
	<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/backend/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <link href="/backend/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
@endsection

<style>
tr:hover {
  background-color: #ffa;
}
.active2 {
    background-color: green !important;
}


</style>

@section('script')
	<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
    <script src="/backend/js/plugins/datapicker/bootstrap-datepicker.js"></script>
  <!--  <script src="/backend/js/plugins/daterangepicker/daterangepicker.js"></script> -->
  <script src="/backend/js/custom.js"></script>


    <script>
        $(document).ready(function() {

            $('.i-checks').iCheck({
                radioClass: 'iradio_square-green',
                checkboxClass: 'icheckbox_square-green',
            });

			$('#check_all').on('ifToggled', function(event) {
				if(this.checked == true) {
					$('.input_checkbox').iCheck('check');
				} else {
					$('.input_checkbox').iCheck('uncheck');
				}
			});

            $('#start_date').datepicker({
				keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
				format: 'dd-mm-yyyy',
            }).on('changeDate', function(){
                $('#end_date').datepicker('setStartDate', $('#start_date').val());

            });

            $('#end_date').datepicker({
                keyboardNavigation: false,
                forceParse: false,
				autoclose: true,
				format: 'dd-mm-yyyy',
            }).on('changeDate', function(){
                $('#start_date').datepicker('setEndDate', $('#end_date').val());

            });

            $('#start_date_month').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
				startView: "months",
				minViewMode: "months",
				format: 'mm-yyyy',
            }).on('changeDate', function(){
                $('#end_date_month').datepicker('setStartDate', $('#end_date_month').val());
            });

            $('#end_date_month').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
				changeYear: true,
				startView: "months",
				minViewMode: "months",
				format: 'mm-yyyy',
            }).on('changeDate', function(){
                $('#start_date_month').datepicker('setEndDate', $('#start_date_month').val());
            });

			$('#start_date_year').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
				startView: "years",
				minViewMode: "years",
				format: 'yyyy',
            }).on('changeDate', function(){
                $('#end_date_year').datepicker('setStartDate', $('#end_date_year').val());
            });

            $('#end_date_year').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
				changeYear: true,
				startView: "years",
				minViewMode: "years",
				format: 'yyyy',
            }).on('changeDate', function(){
                $('#start_date_year').datepicker('setEndDate', $('#start_date_year').val());
            });

			search_filter();

			$(".btn_export").click(function(){
            var btnid = $(this).attr('id');
            if($(this).hasClass("active2")) {
                $(this).removeClass("active2");
                $('#btnicon-'+btnid).removeClass('fa-minus-circle');
                $('#btnicon-'+btnid).addClass('fa-plus-circle');
                {{-- $('#btnlabel-'+btnid).text('Expand'); --}}
            }
            else {
                $(this).addClass("active2");
                $('#btnicon-'+btnid).removeClass('fa-plus-circle');
                $('#btnicon-'+btnid).addClass('fa-minus-circle');
                {{-- $('#btnlabel-'+btnid).text('Collapse'); --}}
            }
			});


        });

		function search_filter(){

			if($('#report_view').val() == 0){ //Daily
				$('#transaction_date_div').show();
				$('#transaction_month_range_div').hide();
				$('#transaction_year_range_div').hide();
			}else if($('#report_view').val() == 1){ //Monthly
				$('#transaction_date_div').hide();
				$('#transaction_month_range_div').show();
				$('#transaction_year_range_div').hide();

			}else if($('#report_view').val() == 2){ //Yearly
				$('#transaction_date_div').hide();
				$('#transaction_month_range_div').hide();
				$('#transaction_year_range_div').show();
			}

			if($('#user_type').val() == 0){
				$('#user_transaction_type_div').show();
				$('#merchant_transaction_type_div').hide();
			}else if($('#user_type').val() == 1){
				$('#user_transaction_type_div').hide();
				$('#merchant_transaction_type_div').show();
			}

		}


    </script>
@endsection