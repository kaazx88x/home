@extends('admin.layouts.master')

@section('title', trans('localize.sales'))

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>@lang('localize.sales')</h2>
            <ol class="breadcrumb">
                <li>
                @lang('localize.report')
                </li>
                <li class="active">
                    <strong>@lang('localize.sales')</strong>
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
					<form class="form-horizontal" id="filter" action='/admin/report/sales' method="GET">
						<div class="form-group">
							<label class="col-sm-2 control-label">{{trans('localize.report_type')}}</label>
							<div class="col-sm-9">
							<select class="form-control" id="report_type" name="report_type" onchange='search_filter();'>
								@foreach ($report_type as $key => $reporttype)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['report_type']) ? 'selected' : '' }}>{{ $reporttype }}</option>
                                @endforeach
							</select>
							</div>
						</div>

						<div class="form-group" id='report_view_div' name='report_view_div' hidden>
							<label class="col-sm-2 control-label">{{trans('localize.report_view')}}</label>
							<div class="col-sm-9">
							<select class="form-control" id="report_view" name="report_view" onchange ='search_filter();'>
								@foreach ($report_view as $key => $reportview)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['report_view']) ? 'selected' : '' }}>{{ $reportview }}</option>
                                @endforeach
							</select>
							</div>
						</div>

						<div class="form-group" id='transaction_type_div' name='transaction_type_div' hidden>
							<label class="col-sm-2 control-label">{{trans('localize.transaction_type')}}</label>
							<div class="col-sm-9">
							<select class="form-control" id="transaction_type" name="transaction_type" onchange='search_filter();'>
								@foreach ($transaction_type as $key => $transactiontype)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['transaction_type']) ? 'selected' : '' }}>{{ $transactiontype }}</option>
                                @endforeach
							</select>
							</div>
						</div>

						<div class="form-group" id='transaction_status_div' name='transaction_status_div' hidden>
							<label class="col-sm-2 control-label">{{trans('localize.status')}}</label>
							<div class="col-sm-9">
							<select class="form-control" id="status" name="status" onchange='search_filter();'>
								@foreach ($status_list as $key => $statuslist)
                                    <option value="{{ $key }}" {{ (strval($key) == $input['status']) ? 'selected' : '' }}>{{ $statuslist }}</option>
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

						<div class="form-group" id='merchant_online_countries_div' name='merchant_online_countries_div' hidden>
                            <label class="col-sm-2 control-label">{{trans('localize.merchant_online_countries')}}</label>
                            <div class="col-sm-9">
                                <label style="cursor: pointer;"></label>
                                @foreach($all_merchant_online_countries as $country)
									<label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="merchant_online_countries[]" value="{{ $country->co_id }}" {{ (isset($input['merchant_online_countries']) && (in_array($country->co_id, $input['merchant_online_countries'])) ? 'checked' : '' ) }}>&nbsp;{{ $country->co_name }}</label> &nbsp; &nbsp;
                                @endforeach
                            </div>
                        </div>

						<div class="form-group" id='store_online_countries_div' name='store_online_countries_div' hidden>
                            <label class="col-sm-2 control-label">{{trans('localize.store_online_countries')}}</label>
                            <div class="col-sm-9">
                                <label style="cursor: pointer;"></label>
                                @foreach($all_store_online_countries as $country1)
                                    <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="store_online_countries[]" value="{{ $country1->co_id }}" {{ (isset($input['store_online_countries']) && (in_array($country1->co_id, $input['store_online_countries']))? 'checked' : '' ) }}> &nbsp;{{ $country1->co_name }}</label> &nbsp; &nbsp;
                                @endforeach
                            </div>
                        </div>

						<div class="form-group" id='merchant_offline_countries_div' name='merchant_offline_countries_div' hidden>
                            <label class="col-sm-2 control-label">{{trans('localize.merchant_offline_countries')}}</label>
                            <div class="col-sm-9">
                                <label style="cursor: pointer;"></label>
                                @foreach($all_merchant_offline_countries as $country2)
                                    <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="merchant_offline_countries[]" value="{{ $country2->co_id }}" {{ (isset($input['merchant_offline_countries']) && (in_array($country2->co_id, $input['merchant_offline_countries']))? 'checked' : '' ) }}>&nbsp;{{ $country2->co_name }}</label> &nbsp; &nbsp;
                                @endforeach
                            </div>
                        </div>

						<div class="form-group" id='store_offline_countries_div' name='store_offline_countries_div' hidden>
                            <label class="col-sm-2 control-label">{{trans('localize.store_offline_countries')}}</label>
                            <div class="col-sm-9">
                                <label style="cursor: pointer;"></label>
                                @foreach($all_store_offline_countries as $country3)
                                    <label style="cursor: pointer;"><input type="checkbox" class="i-checks input_checkbox" name="store_offline_countries[]" value="{{ $country3->co_id }}" {{ (isset($input['store_offline_countries']) && (in_array($country3->co_id, $input['store_offline_countries']))? 'checked' : '' ) }}>&nbsp;{{ $country3->co_name }}</label> &nbsp; &nbsp;
                                @endforeach
                            </div>
                        </div>

						<div class="form-group" id='sort_by_div' name='sort_by_div' hidden>
                        <label class="col-sm-2 control-label">{{trans('localize.sort')}}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sortby" name="sortby" style="font-family:'FontAwesome', sans-serif;">
                                <option value="trans_date_asc" {{($input['sortby'] == "trans_date_asc" ) ? 'selected' : ''}}>{{trans('localize.transaction_date')}} : &#xf162;</option>
                                <option value="trans_date_desc" {{($input['sortby'] == 'trans_date_desc') ? 'selected' : ''}}>{{trans('localize.transaction_date')}} : &#xf163;</option>
                                <option value="totalsale_asc" {{($input['sortby'] == 'totalsale_asc') ? 'selected' : ''}}>{{trans('localize.total_sale')}} : &#xf162;</option>
                                <option value="totalsale_desc" {{($input['sortby'] == 'totalsale_desc') ? 'selected' : ''}}>{{trans('localize.total_sale')}}  : &#xf163;</option>
                                <option value="platformcharge_asc" {{($input['sortby'] == 'platformcharge_asc') ? 'selected' : ''}}>{{trans('localize.platform_charge')}} : &#xf162;</option>
                                <option value="platformcharge_desc" {{($input['sortby'] == 'platformcharge_desc') ? 'selected' : ''}}>{{trans('localize.platform_charge')}}: &#xf163;</option>
                                <option value="customercharge_asc" {{($input['sortby'] == 'customercharge_asc') ? 'selected' : ''}}>{{trans('localize.customer_charge')}} : &#xf162;</option>
                                <option value="customercharge_desc" {{($input['sortby'] == 'customercharge_desc') ? 'selected' : ''}}>{{trans('localize.customer_charge')}}  : &#xf163;</option>
                                <option value="merchant_charge_asc" {{($input['sortby'] == 'merchant_charge_asc') ? 'selected' : ''}}>{{trans('localize.merchant_charge')}} : &#xf162;</option>
                                <option value="merchant_charge_desc" {{($input['sortby'] == 'merchant_charge_desc') ? 'selected' : ''}}>{{trans('localize.merchant_charge')}}  : &#xf163;</option>
                                <option value="mercommision_asc" {{($input['sortby'] == 'mercommision_asc') ? 'selected' : ''}}>{{trans('localize.merchant_commission')}} : &#xf162;</option>
                                <option value="mercommision_desc" {{($input['sortby'] == 'mercommision_desc') ? 'selected' : ''}}>{{trans('localize.merchant_commission')}}  : &#xf163;</option>
                                <option value="earning_asc" {{($input['sortby'] == 'earning_asc') ? 'selected' : ''}}>{{trans('localize.earning')}} : &#xf162;</option>
                                <option value="earning_desc" {{($input['sortby'] == 'earning_desc') ? 'selected' : ''}}>{{trans('localize.earning')}}  : &#xf163;</option>

							</select>
                        </div>
						</div>
						<div class="form-group">
                        <div class="col-sm-9 col-sm-offset-2">
                            <div  class="col-sm-6">
                                <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">{{trans('localize.search')}}</button>
                            </div>
                            <div  class="col-sm-6">
                                <button type="button" class="btn  btn-block btn-outline btn-primary btn-sm" href="/admin/report/sales" onclick="location.href='/admin/report/sales'">
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
			@if(isset($input['report_type']) && $input['report_type'] <> NULL)
			@if(count($orders) > 0)
                <div class="ibox-title" style="display: block;">
                    <div class="ibox-tools" style="margin-bottom:10px;">
                       <div class="btn-group">
							@if($export_permission)
                                <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle"> {{trans('localize.Export_All')}} <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="/admin/export/sale_by_transaction_date?action=export&export_as=csv{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Csv</a></li>
                                    <li><a href="/admin/export/sale_by_transaction_date?action=export&export_as=xlsx{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xlsx</a></li>
                                    <li><a href="/admin/export/sale_by_transaction_date?action=export&export_as=xls{{ (!empty($input))? '&' . http_build_query($input) : '' }}">Xls</a></li>
                                </ul>
							@endif
                            </div>
                    </div>
                </div>

			@endif
			@endif
			<div class="ibox-content">
				@if(isset($input['report_type']) && $input['report_type'] <> NULL)
                    <div class="table-responsive">
						@if(count($orders) > 0)
                        <table class="table table-stripped table-bordered">
                        <thead>
                            <tr>
							@if($input['report_type'] == 0)
								<th nowrap class="text-center"></th>
								<th nowrap class="text-center">{{trans('localize.date')}}</th>
								<th class="text-center">{{trans('localize.country')}}</th>
								<th class="text-center">{{trans('localize.Merchants')}}</th>
								<th class="text-center">{{trans('localize.stores')}}</th>
								<th class="text-center">{{trans('localize.total_order')}}</th>

								@if(isset($input['transaction_type']) && $input['transaction_type'] == 0) <!-- Online -->
									<th class="text-center">{{trans('localize.total_product')}}</th>
								@endif

								<th class="text-center">{{trans('localize.total_sale')}} <br> (Mei Point)</th>
								<th nowrap class="text-center">{{trans('localize.platform_charge')}} <br> (Mei Point)</th>
								<th nowrap class="text-center">{{trans('localize.customer_charge')}} <br> (Mei Point)</th>
								<th nowrap class="text-center">{{trans('localize.merchant_charge')}}<br>(Mei Point)</th>
								<th nowrap class="text-center">{{trans('localize.merchant_commission')}}<br>(Mei Point)</th>
								<th nowrap class="text-center">{{trans('localize.earning')}}<br>(Mei Point)</th>

							@endif
                            </tr>

                        </thead>

						<tbody>
							@php
								$total_order = 0;
								$total_product = 0;
								$total_sales = 0;
								$total_platform_charge = 0;
								$total_customer_charge = 0;
								$total_merchant_charge = 0;
								$total_merchant_commission = 0;
								$total_merchant_earning = 0;

								$pre_date = '';
								$pre_merchant_country = 0;
								$pre_merchant = 0;
								$pre_store = 0;
								$pre_year = 0;
								$pre_month = 0;
								$collapse_id = 0;

							@endphp

							@if($input['report_type'] == 0) <!-- Report Type By Transaction Date -->
								@foreach ($orders as $key => $order)
								@php
									$group_level_write1 = 0;
									$group_level_write2 = 0;
									$group_level_write3 = 0;
									$group_level_write4 = 0;


									if(isset($input['report_view']) && $input['report_view'] == 0 ){
										if($pre_date <> $order->l1_transaction_date){
											$group_level_write1 = 1;
											$group_level_write2 = 1;
											$group_level_write3 = 1;
											$group_level_write4 = 1;
										}

									}else if(isset($input['report_view']) && $input['report_view'] == 1 ){
										if($pre_year <> $order->l1_year || $pre_month <> $order->l1_month){
											$group_level_write1 = 1;
											$group_level_write2 = 1;
											$group_level_write3 = 1;
											$group_level_write4 = 1;
										}

									}else if(isset($input['report_view']) && $input['report_view'] == 2 ){
										if($pre_year <> $order->l1_year ){
											$group_level_write1 = 1;
											$group_level_write2 = 1;
											$group_level_write3 = 1;
											$group_level_write4 = 1;
										}
									}

									if($group_level_write1 == 1){
										$collapse_id = $collapse_id + 1;
									}

									$total_order = $total_order + $order->l4_total_order;
									$total_product = $total_product + $order->l4_total_product;
									$total_sales = $total_sales + round( $order->l4_sales_amount , 4);
									$total_platform_charge = $total_platform_charge + round( $order->l4_platform_charge , 4);
									$total_customer_charge = $total_customer_charge + round( $order->l4_customer_charge , 4);
									$total_merchant_charge = $total_merchant_charge + round( $order->l4_merchant_commission , 4);
									$total_merchant_commission = $total_merchant_commission + round( $order->l4_merchant_earning , 4);
									$total_merchant_earning = $total_merchant_earning + round( $order->l4_earning , 4);


									if($pre_merchant_country <> $order->l2_merchant_country_id){
										$group_level_write2 = 1;
										$group_level_write3 = 1;
										$group_level_write4 = 1;
									}
									if($pre_merchant <> $order->l3_merchant_id){
										$group_level_write3 = 1;
										$group_level_write4 = 1;
									}
									if($pre_store <> $order->l4_store_id){
										$group_level_write4 = 1;
									}

								@endphp

									@if($group_level_write1 == 1)
										<tr class="text-center " bgcolor='#F2F2F2' >
											<td>
												<button style='height: 30px;' type="button" class="btn btn-primary btn-sm accordion-toggle btn_export" id="{{ $collapse_id }}" data-toggle="collapse"     data-target=".collapse_{{$collapse_id}}">
												<i class="fa fa-plus-circle" aria-hidden="true" id="btnicon-{{ $collapse_id }}"></i> <span id="btnlabel-{{ $collapse_id }}"></span>
												</button></i>
											</td>

											@if(isset($input['report_view']) && $input['report_view'] == 0 )
												<td><b> {{ date('d-m-Y',strtotime($order->l1_transaction_date)) }} </b></td>
											@elseif(isset($input['report_view']) && $input['report_view'] == 1 )
												<td><b> {{ $order->l1_year .'-'. $order->l1_month }} </b></td>
											@elseif(isset($input['report_view']) && $input['report_view'] == 2 )
												<td><b> {{ $order->l1_year  }} </b></td>
											@endif

											<td></td>
											<td></td>
											<td></td>
											<td align='center'><b> {{ $order->l1_total_order }} </b></td>

											@if(isset($input['transaction_type']) && $input['transaction_type'] == 0) <!-- Online -->
												<td align='center'><b> {{ $order->l1_total_product }} </b></td>
											@endif

											<td align='right'><b>{{ number_format( $order->l1_sales_amount , 4) }} </b></td>
											<td align='right'><b>{{ number_format( $order->l1_platform_charge , 4) }} </b></td>
											<td align='right'><b>{{ number_format( $order->l1_customer_charge , 4) }} </b></td>
											<td align='right'><b>{{ number_format( $order->l1_merchant_commission , 4) }} </b></td>
											<td align='right'><b>{{ number_format( $order->l1_merchant_earning , 4) }} </b></td>
											<td align='right'><b>{{ number_format( $order->l1_earning , 4) }} </b></td>
										</tr>
									@endif

									@if($group_level_write2 == 1)
										<tr bgcolor='#E6E6E6' class="collapse collapse_{{$collapse_id}} text-center">
											<td></td>
											<td></td>
											<td><b> {{ $order->l2_merchant_country }} </b></td>
											<td></td>
											<td></td>
											<td align='center'><b> {{ $order->l2_total_order }} </b></td>

											@if(isset($input['transaction_type']) && $input['transaction_type'] == 0) <!-- Online -->
											<td align='center'><b> {{ $order->l2_total_product }} </b></td>
											@endif

											<td align='right'><b> {{ number_format( $order->l2_sales_amount , 4) }} </b></td>
											<td align='right'><b> {{ number_format( $order->l2_platform_charge , 4) }} </b></td>
											<td align='right'><b> {{ number_format( $order->l2_customer_charge , 4) }} </b></td>
											<td align='right'><b> {{ number_format( $order->l2_merchant_commission , 4) }} </b></td>
											<td align='right'><b> {{ number_format( $order->l2_merchant_earning , 4) }} </b></td>
											<td align='right'><b> {{ number_format( $order->l2_earning , 4) }} </b></td>
										</tr>
									@endif

									@if($group_level_write3 == 1)
										<tr class="collapse collapse_{{$collapse_id}} text-center">
											<td></td>
											<td></td>
											<td></td>
											<td><b> {{ $order->l3_merchant_name1 }}  <br>  {{ $order->l3_merchant_name2 }} <br> (id = {{ $order->l3_merchant_id }})  </b></td>
											<td></td>
											<td  align='center'><b> {{ $order->l3_total_order }} </b></td>

											@if(isset($input['transaction_type']) && $input['transaction_type'] == 0) <!-- Online -->
											<td  align='center'><b> {{ $order->l3_total_product }} </b></td>
											@endif

											<td  align='right'><b> {{ number_format( $order->l3_sales_amount , 4) }} </b></td>
											<td  align='right'><b> {{ number_format( $order->l3_platform_charge , 4) }} </b></td>
											<td  align='right'><b> {{ number_format( $order->l3_customer_charge , 4) }} </b></td>
											<td  align='right'><b> {{ number_format( $order->l3_merchant_commission , 4) }} </b></td>
											<td  align='right'><b> {{ number_format( $order->l3_merchant_earning , 4) }} </b></td>
											<td  align='right'><b> {{ number_format( $order->l3_earning , 4) }} </b></td>
										</tr>
									@endif

									@if($group_level_write4 == 1)
										<tr class="collapse collapse_{{$collapse_id}} text-center">
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td align='center'>{{ $order->l4_stor_name }} <br> ({{ $order->l4_store_country  }}) <br> (id = {{ $order->l4_store_id }}) </td>
											<td align='center'> {{ $order->l4_total_order }} </td>

											@if(isset($input['transaction_type']) && $input['transaction_type'] == 0) <!-- Online -->
											<td align='center'> {{ $order->l4_total_product }} </td>
											@endif

											<td align='right'> {{ number_format( $order->l4_sales_amount , 4) }} </td>
											<td align='right'> {{ number_format( $order->l4_platform_charge , 4) }} </td>
											<td align='right'> {{ number_format( $order->l4_customer_charge , 4) }} </td>
											<td align='right'> {{ number_format( $order->l4_merchant_commission , 4) }} </td>
											<td align='right'> {{ number_format( $order->l4_merchant_earning , 4) }} </td>
											<td align='right'> {{ number_format( $order->l4_earning , 4) }} </td>
										</tr>
									@endif

									@php
										if(isset($input['report_view']) && $input['report_view'] == 0 ){
											$pre_date = $order->l1_transaction_date ;

										}else if(isset($input['report_view']) && $input['report_view'] == 1 ){
											$pre_year = $order->l1_year ;
											$pre_month = $order->l1_month ;

										}else if(isset($input['report_view']) && $input['report_view'] == 2 ){
											$pre_year = $order->l1_year ;
										}

										$pre_merchant_country = $order->l2_merchant_country_id ;
										$pre_merchant = $order->l3_merchant_id ;
										$pre_store = $order->l4_store_id ;
									@endphp

								@endforeach

								<tr class="text-right" bgcolor='#F2F2F2'>
									<td></td>
									<td colspan='4' ><b> {{trans('localize.grand_total')}} </b></td>
									<td class="text-center" ><b> {{$total_order}}</b></td>

									@if(isset($input['transaction_type']) && $input['transaction_type'] == 0) <!-- Online -->
									<td class="text-center"><b> {{$total_product}}</b></td>
									@endif

									<td><b> {{ number_format( $total_sales , 4) }}</b></td>
									<td><b> {{ number_format( $total_platform_charge , 4) }}</b></td>
									<td><b> {{ number_format( $total_customer_charge , 4) }}</b></td>
									<td><b> {{ number_format( $total_merchant_charge , 4) }}</b></td>
									<td><b> {{ number_format( $total_merchant_commission , 4) }}</b></td>
									<td><b> {{ number_format( $total_merchant_earning , 4) }}</b></td>
								</tr>

							@else
`
							@endif
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
			if($('#report_type').val() == 0){ //Sale Report By Transaction Date
				$('#report_view_div').show();
				$('#transaction_type_div').show();

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

				if($('#transaction_type').val() == 0){
					$('#merchant_online_countries_div').show();
					$('#store_online_countries_div').show();
					$('#merchant_offline_countries_div').hide();
					$('#store_offline_countries_div').hide();
					store_offline_countries_div
				}else if($('#transaction_type').val() == 1){
					$('#merchant_online_countries_div').hide();
					$('#store_online_countries_div').hide();
					$('#merchant_offline_countries_div').show();
					$('#store_offline_countries_div').show();
				}
				$('#transaction_status_div').show();
				$('#sort_by_div').show();
			}
		}


    </script>
@endsection