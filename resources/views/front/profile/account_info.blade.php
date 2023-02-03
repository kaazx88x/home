@extends('layouts.web.master')

@section('header')
	@include('layouts.web.header.main')
@endsection

@section('content')
	<div class="ListingTopbar">
        <h4 class="ListingCategory">@lang('localize.myaccount')</h4>
        <a href="/profile" class="back"><i class="fa fa-angle-left"></i></a>
    </div>

	<div class="username">
		<div class="avatar">
			<img class="lazyload" data-src="{{ env('IMAGE_DIR') . '/avatar/' . $customer->cus_id . '/' . $customer->cus_pic }}" src="{{ asset('assets/images/general-avatar.png') }}">
		</div>
		<div class="usercontent">
			<h4>{{ $customer->cus_name }}</h4>
			@if ($customer->phone_area_code && $customer->cus_phone)
			<p>
				{{ \Helper::securePhone($customer->phone_area_code . $customer->cus_phone) }}
				<a href="{{ url('/profile/phone') }}">
					<span class="text-{{ !$customer->cellphone_verified? 'danger' : 'success' }}">
						<small>
							<i class="fa fa-{{ !$customer->cellphone_verified? 'exclamation-circle' : 'check' }}" style="float: none; font-size: inherit; color: {{ !$customer->cellphone_verified? '#a94442' : '#6dd400' }}; padding: 1px;"></i>
							{{ !$customer->cellphone_verified? trans('localize.phone_not_verified') : trans('localize.verified') }}
						</small>
					</span>
				</a>
			</p>
			@endif

			@if ($customer->email)
			<p>
				{{ \Helper::secureEmail($customer->email) }}
				<span class="text-{{ !$customer->email_verified? 'danger' : 'success' }}">
					<small>
						<i class="fa fa-{{ !$customer->email_verified? 'exclamation-circle' : 'check' }}" style="float: none; font-size: inherit; color: {{ !$customer->email_verified? '#a94442' : '#6dd400' }}; padding: 1px;"></i>
						@if (!$customer->email_verified)
							<a href="{{ url('/profile/send/verification/email') }}" style="color: #a94442;">@lang('localize.email_not_verified') @lang('localize.resend_activation')</a>
						@else
							<a href="{{ url('/profile/email') }}" style="color: #6dd400;">@lang('localize.verified')</a>
						@endif
					</small>
				</span>
			</p>
			@endif
		</div>
	</div>

	<div class="ContentWrapper">
		<a href="javascript:void(0)" data-toggle="modal" data-target="#profile-update">
			<div class="panel-general panel-list" >
				<img src="{{ asset('assets/images/icon/icon_account.png') }}">
				@lang('localize.avatarUpdate')
				<i class="fa fa-angle-right"></i>
			</div>
		</a>
		@include('layouts.partials.status')

		<div id="profile-update" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<div class="modal-title">@lang('localize.avatarUpdate')</div>
						<form action="/profile/upload" method="POST" enctype="multipart/form-data">
						{{ csrf_field() }}
						<div class="form-group">
							<label class="control-label">@lang('localize.please_select_an_image_to_upload')</label>
							<div class="input-group">
								<label class="input-group-btn">
									<span class="btn btn-default">
										Browse <input type="file" style="display: none;" name="file" onchange="$('#upload-file-info').val($(this).val());">
									</span>
								</label>
								<input type="text" id="upload-file-info" class="form-control file-name-input" readonly>
							</div>
						</div>

						<button type="submit" class="btn btn-primary btn-block">@lang('localize.upload')</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<form id="customer-info" method="POST" action="{{ url('profile/account') }}" accept-charset="UTF-8">
			{{ csrf_field() }}
			<div class="panel-general">
				<div class="form-general">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">@lang('localize.title')<span style="color:red;">*</span></label>
								<select style="font-size: 12px;" name="cus_title" class="form-control compulsary">
								<option value="">--</option>
								@foreach($select['person_titles'] as $id => $title)
								<option value="{{ $id }}" {{ (old('cus_title', (!empty($customer->info->cus_title)) ? $customer->info->cus_title : null) == $id) ? ' selected' : '' }}>{{ $title }}</option>
								@endforeach
							</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
								<label class="control-label">@lang('localize.name') <span style="color:red;">*</span></label>
								<input type="text" class="form-control compulsary" name="name" placeholder="@lang('localize.nameInput')" value="{{ old('name', $customer->cus_name) }}">
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group{{ $errors->has('address1') ? ' has-error' : '' }}">
								<label class="control-label">@lang('localize.address') <span style="color:red;">*</span></label>
								<input type="text" class="form-control compulsary" name="address1" placeholder="@lang('localize.address1Input')" value="{{ old('address1', $customer->cus_address1) }}">
								@if ($errors->has('address1'))
									<span class="help-block">
										{{ $errors->first('address1') }}
									</span>
								@endif
								<input type="text" class="form-control" name="address2" placeholder="@lang('localize.address2Input')" value="{{ old('address2', $customer->cus_address2) }}">
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group{{ $errors->has('zipcode') ? ' has-error' : '' }}">
								<label class="control-label">@lang('localize.zipcode') <span style="color:red;">*</span></label>
								<input type="text" class="form-control compulsary" id="zipcode" name="zipcode" placeholder="@lang('localize.zipcode')" value="{{ old('zipcode', $customer->cus_postalcode) }}">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
								<label class="control-label">@lang('localize.country') <span style="color:red;">*</span></label>
								<select class="form-control compulsary input" id="country" name="country" onchange="get_states('#state', this.value)"></select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
								<label class="control-label">@lang('localize.state') <span style="color:red;">*</span></label>
								<select class="form-control compulsary input" id="state" name="state">
								<option value="">@lang('localize.selectState')</option>
							</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">City <span style="color:red;">*</span></label>
								<input type="text" class="form-control compulsary" id="city" name="city" value="{{ old('city', $customer->cus_city_name) }}">
							</div>
						</div>

						<div class="col-sm-12 seperator"></div>

						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">@lang('localize.job') <span style="color:red;">*</span></label>
								<select class="form-control compulsary" name="cus_job">
									<option value="">@lang('localize.selectOption')</option>
									@foreach($select['jobs'] as $id => $title)
									<option value="{{ $id }}" {{ (old('cus_job', (!empty($customer->info->cus_job)) ? $customer->info->cus_job : null) == $id) ? ' selected' : '' }}>{{ $title }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">@lang('localize.monthly_incomes') <span style="color:red;">*</span></label>
								<select class="form-control compulsary" name="cus_incomes">
									<option value="">@lang('localize.selectOption')</option>
									@foreach($select['incomes'] as $id => $title)
									<option value="{{ $id }}" {{ (old('cus_incomes', (!empty($customer->info->cus_incomes)) ? $customer->info->cus_incomes : null) == $id) ? ' selected' : '' }}>{{ $title }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">@lang('localize.education') <span style="color:red;">*</span></label>
								<select class="form-control compulsary" name="cus_education">
									<option value="">@lang('localize.selectOption')</option>
									@foreach($select['educations'] as $id => $title)
									<option value="{{ $id }}" {{ (old('cus_education', (!empty($customer->info->cus_education)) ? $customer->info->cus_education : null) == $id) ? ' selected' : '' }}>{{ $title }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-sm-12 seperator"></div>

						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">@lang('localize.gender') <span style="color:red;">*</span></label>
								<label class="radio radio-inline">
									<input type="radio" name="cus_gender" class="i-checks compulsary" value="1" {{ (old('cus_gender', (!empty($customer->info->cus_gender)) ? $customer->info->cus_gender : 1) == 1) ? ' checked' : '' }}>
									<ins></ins> <span>@lang('localize.male')</span>
								</label>
								<label class="radio radio-inline">
									<input type="radio" name="cus_gender" class="i-checks compulsary" value="2" {{ (old('cus_gender', (!empty($customer->info->cus_gender)) ? $customer->info->cus_gender : null) == 2) ? ' checked' : '' }}>
									<ins></ins> <span>@lang('localize.female')</span>
								</label>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">@lang('localize.dob') <span style="color:red;">*</span></label>
								<input type="text" class="form-control compulsary" name="cus_dob" id="dob" placeholder="DOB" style="width:100%;" readonly value="{{ old('cus_dob', (!empty($customer->info->cus_dob)) ? date('d-m-Y', strtotime($customer->info->cus_dob)) : null) }}">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">@lang('localize.nationality') <span style="color:red;">*</span></label>
								<select class="form-control compulsary" name="cus_nationality">
									<option value="">@lang('localize.selectOption')</option>
									@foreach($select['nationality'] as $id => $title)
									<option value="{{ $id }}" {{ (old('cus_nationality', (!empty($customer->info->cus_nationality)) ? $customer->info->cus_nationality : null) == $id) ? ' selected' : '' }}>{{ $title }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">@lang('localize.race') <span style="color:red;">*</span></label>
								<select class="form-control compulsary" name="cus_race">
									<option value="">@lang('localize.selectOption')</option>
									@foreach($select['races'] as $id => $title)
									<option value="{{ $id }}" {{ (old('cus_race', (!empty($customer->info->cus_race)) ? $customer->info->cus_race : null) == $id) ? ' selected' : '' }}>{{ $title }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">@lang('localize.religion') <span style="color:red;">*</span></label>
								<select class="form-control compulsary" name="cus_religion">
									<option value="">@lang('localize.selectOption')</option>
									@foreach($select['religions'] as $id => $title)
									<option value="{{ $id }}" {{ (old('cus_religion', (!empty($customer->info->cus_religion)) ? $customer->info->cus_religion : null) == $id) ? ' selected' : '' }}>{{ $title }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-sm-12 seperator"></div>

						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">@lang('localize.marital_status') <span style="color:red;">*</span></label>
								<select class="form-control compulsary" name="cus_marital">
								<option value="">@lang('localize.selectOption')</option>
								@foreach($select['marital'] as $id => $title)
								<option value="{{ $id }}" {{ (old('cus_marital', (!empty($customer->info->cus_marital)) ? $customer->info->cus_marital : null) == $id) ? ' selected' : '' }}>{{ $title }}</option>
								@endforeach
							</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label">@lang('localize.no_of_children')</label>
								<input type="text" class="form-control number" name="cus_children" placeholder="" value="{{ old('cus_children', (!empty($customer->info->cus_children)) ? $customer->info->cus_children : 0) }}">
							</div>
						</div>

						<div class="col-sm-12 seperator"></div>

						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label">@lang('localize.hobby')</label>
								<input type="text" class="form-control" name="cus_hobby" placeholder="" value="{{ old('cus_hobby', (!empty($customer->info->cus_hobby)) ? $customer->info->cus_hobby : '') }}">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="action" style="margin-bottom:12px;">
				<button id="update-info" type="button" class="btn btn-block btn-primary">@lang('localize.update')</button>
			</div>
		</form>
	</div>
@endsection

@section('styles')
	<link href="{{ asset('backend/css/plugins/iCheck/custom.css') }}" rel="stylesheet">
	<link href="{{ asset('backend/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
@endsection

@section('scripts')
	<script src="{{ asset('backend/js/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('backend/js/custom.js') }}" type="text/javascript"></script>
	<script src="{{ asset('backend/js/plugins/datapicker/bootstrap-datepicker.js') }}" type="text/javascript"></script>

	<script type="text/javascript">
		$(function() {

			get_countries('#country', "{{ old('country', $customer->cus_country? $customer->cus_country : '0') }}", '#state', "{{ old('state', ($customer->cus_state)? $customer->cus_state : '0') }}");

			$('.number').keydown(function (e) {-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

			$('#dob').datepicker({
				keyboardNavigation: false,
				forceParse: false,
				autoclose: true,
				format: 'dd-mm-yyyy',
				autoclose: true,
				forceParse: false,
				Default: true,
				todayHighlight: true,
			});

			$('.open-calendar').click(function(event){
				event.preventDefault();
				$('#dob').focus();
			});

			$('.i-checks').iCheck({
				checkboxClass: 'icheckbox_square-green',
				radioClass: 'iradio_square-green',
			});

			$('#update-info').click(function() {
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
					$('#customer-info').submit();
				}
			});

		});
	</script>

	<script>
		( function ( document, window, index )
		{
			var inputs = document.querySelectorAll( '.inputfile' );
			Array.prototype.forEach.call( inputs, function( input )
			{
				var label	 = input.nextElementSibling,
					labelVal = label.innerHTML;

				input.addEventListener( 'change', function( e )
				{
					var fileName = '';
					if( this.files && this.files.length > 1 )
						fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
					else
						fileName = e.target.value.split( '\\' ).pop();

					if( fileName )
						label.querySelector( 'span' ).innerHTML = fileName;
					else
						label.innerHTML = labelVal;
				});
			});
		}( document, window, 0 ));
	</script>
@endsection