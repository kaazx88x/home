@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h2>@lang('localize.contactus')</h2>
        </div>
    </div>

    @if (isset($success))
        <div class="alert alert-success">
            {{ $success }}
        </div>
    @endif

    <div class="grid auth-panel">
		<div class="grid__item">			
            <!-- ../page heading-->
            <div id="contact" class="page-content page-contact">
                <div id="message-box-conact"></div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="contact-form-box">
                            <form class="form-styling" action="/contact-us" method="POST">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.sub_heading')}}</label>
                                    <select class="form-control input-sm" name="subject">
                                        <option value="Customer service">{{trans('localize.cus_service')}}</option>
                                        <option value="Webmaster">{{trans('localize.webmaster')}}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.email_add')}}</label>
                                    <input type="text" class="form-control input-sm" name="email"/>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.order_ref')}}</label>
                                    <input type="text" class="form-control input-sm" name="order_reference"/>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{trans('localize.message')}}</label>
                                    <textarea class="form-control input-sm" rows="10" name="message" style="height:auto;"></textarea>
                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group btn-send-contact">
                                    <button type="submit" id="btn-send-contact" class="btn">{{trans('localize.send')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-5 pull-right box-border" id="contact_form_map">
                        <ul class="store_info">
                            <li>
                                <i class="fa fa-home"></i>
                                T6-7-1 Menara 6, Lingkaran Maju, Jalan Lingkaran Tengah 2, Bandar Tasik Selatan, 57000, KL
                            </li>
                            <li>
                                <i class="fa fa-envelope"></i>
                                <span><a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></span>
                            </li>
                        </ul>
                        <p></p>
                        <br/>
                    </div>
                </div>
            </div>
		</div>
	</div>
@endsection
