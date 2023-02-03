@extends('admin.layouts.master')

@section('title', trans('localize.credit_log'))

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>@lang('localize.credit_log')</h2>
            <ol class="breadcrumb">
                <li>
                @lang('localize.report')
                </li>
                <li class="active">
                    <strong>@lang('localize.credit_log')</strong>
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
					<form class="form-horizontal" id="filter" action='/admin/report/credit-log' method="GET">
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

						<div class="form-group">
                            <label class="col-sm-2 control-label">{{trans('localize.transaction_date')}}</label>
                            <div class="col-sm-9">
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control" name="start" id="sdate" placeholder="{{trans('localize.startDate')}}" value="{{ (isset($input)) ? $input['start'] : '' }}" />
                                    <span class="input-group-addon">{{trans('localize.to')}}</span>
                                    <input type="text" class="form-control" name="end" id="edate" placeholder="{{trans('localize.endDate')}}" value="{{ (isset($input)) ? $input['end'] : '' }}" />
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

						<div class="form-group">
							<label class="col-sm-2 control-label">{{trans('localize.user_id')}}</label>
							<div class="col-sm-3">
								<input type="text" value="{{$input['userid']}}" placeholder="{{trans('localize.user_id')}}" class="form-control" id="userid" name="userid">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">{{trans('localize.username')}}</label>
							<div class="col-sm-9">
								<input type="text" value="{{$input['username']}}" placeholder="{{trans('localize.username')}}" class="form-control" id="username" name="username">
							</div>
						</div>

						<div class="form-group" id='sort_by' name='sort_by' >
                        <label class="col-sm-2 control-label">{{trans('localize.sort')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort_by" name="sort_by" style="font-family:'FontAwesome', sans-serif;">
                                <option value="trans_date_asc" {{($input['sort_by'] == "trans_date_asc" ) ? 'selected' : ''}}>{{trans('localize.transaction_date')}} : &#xf162;</option>
                                <option value="trans_date_desc" {{($input['sort_by'] == 'trans_date_desc') ? 'selected' : ''}}>{{trans('localize.transaction_date')}} : &#xf163;</option>
                                <option value="username_asc" {{($input['sort_by'] == 'username_asc') ? 'selected' : ''}}>{{trans('localize.username')}} : &#xf162;</option>
                                <option value="username_desc" {{($input['sort_by'] == 'username_desc') ? 'selected' : ''}}>{{trans('localize.username')}}  : &#xf163;</option>
                                <option value="user_id_asc" {{($input['sort_by'] == 'user_id_asc') ? 'selected' : ''}}>{{trans('localize.user_id')}} : &#xf162;</option>
                                <option value="user_id_desc" {{($input['sort_by'] == 'user_id_desc') ? 'selected' : ''}}>{{trans('localize.user_id')}}: &#xf163;</option>
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
                                <button type="button" class="btn  btn-block btn-outline btn-primary btn-sm" href="/admin/report/credit-log" onclick="location.href='/admin/report/credit-log'">
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
											<li><a href="/admin/export/credit_log?action=export&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
											<li><a href="/admin/export/credit_log?action=export&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
											<li><a href="/admin/export/credit_log?action=export&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
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
										<th nowrap class="text-center">{{trans('localize.date')}}</th>
										<th nowrap class="text-center">{{trans('localize.country')}}</th>
										<th nowrap class="text-center">{{trans('localize.id')}}</th>
										<th nowrap class="text-center">{{trans('localize.username')}}</th>
										<th nowrap class="text-center">{{trans('localize.credit')}}</th>
										<th nowrap class="text-center">{{trans('localize.debit')}}</th>
										<th nowrap class="text-center">{{trans('localize.remarks')}}</th>
									</thead>

									<tbody>
									@php
										$total_debit_amount = 0.0;
										$total_credit_amount = 0.0;
									@endphp

									@foreach ($logs as $key => $log)
										<tr class="text-center">
											<td>{{ date_format($log->created_at,"d/m/Y") }}</td>
											<td>{{ ($log->co_name <> '') ? $log->co_name : "No Country" }}</td>
											@if(isset($input['user_type']) && $input['user_type'] == 0)
												<td>{{ $log->cus_id }}</td>
											@elseif(isset($input['user_type']) && $input['user_type'] == 1)
												<td>{{ $log->mer_id }}</td>
											@endif

											@if(isset($input['user_type']) && $input['user_type'] == 0)
											<td>{{ $log->cus_name }}</td>
											@elseif(isset($input['user_type']) && $input['user_type'] == 1)
											<td>{{ $log->username }}</td>
											@endif
											<td align='right' >{{ number_format($log->credit_amount,4) }}</td>
											<td align='right' >{{ number_format($log->debit_amount,4) }}</td>
											<td>{{ $log->remark }}</td>
										</tr>
										@php
											$total_debit_amount = $total_debit_amount + $log->debit_amount ;
											$total_credit_amount = $total_credit_amount + $log->credit_amount ;
										@endphp
									@endforeach

										<tr class="text-right" >
											<td colspan='4' ><b>{{trans('localize.total')}}</b></td>
											<td><b>{{ number_format($total_credit_amount, 4) }}</b></td>
											<td><b>{{ number_format($total_debit_amount, 4) }}</b></td>
											<td></td>
										</tr>
									</tbody>
								</table>
							@else
							{{trans('localize.No_Record_For_This_Report')}}
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

			search_filter();

        });

		function search_filter(){
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