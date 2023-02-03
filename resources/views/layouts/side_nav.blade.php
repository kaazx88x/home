<div class="side-nav {{ (Request::is('/')) ? 'home' : '' }}">
    <div class="details">
        <div class="username text-center">
            @if (Auth::check())
                <img src="{{ env('IMAGE_DIR').'/avatar/'.$cus_details->cus_id.'/'.$cus_details->cus_pic }}"onerror="if (this.src != 'error.jpg') this.src = '{{ asset('web/images/stock.png') }}';">
                <br/>
                <a href="/profile"><span>{{ ucwords($cus_details->username) }}</span></a>
            @else
                <img src="{{ asset('assets/images/meihome_logo.png') }}">
                <br/>
                <a href="/login"><span>{{ trans('localize.login') }}</span></a> | <a href="/register"><span>{{ trans('localize.register') }}</span></a>
            @endif
        </div>
        @if (Auth::check())
        <div class="credit">
            <div class="this-title">@lang('common.credit_name')</div>
            <div class="inner">
                <div class="credit-list">
                    <img src="{{ asset('assets/images/icon_meicredit.png') }}">
                    <span>{{ $cus_vc }}</span>
                </div>
                {{-- <div class="credit-list">
                    <img src="images/v_token_icon.png">
                    <span>0.00</span>
                </div> --}}
            </div>
        </div>
        @endif
        <div class="top-down-nav">
            <div class="this-title" id="nav_up"><i class="fa fa-angle-up"></i> @lang('localize.top')</div>
            <div class="this-title" id="nav_down"><i class="fa fa-angle-down"></i> @lang('localize.down')</div>
        </div>
    </div>

    <img src="/assets/images/sb-banner.png" class="side-ads">
</div>