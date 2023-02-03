<header class="MainHeader">
    <div class="logo">
        <a href="/">
            <img src="{{ asset('assets/images/meihome_logo.png') }}">
        </a>
    </div>
    <a href="javascript:history.go(-1)" class="back transition"><i class="fa fa-angle-left"></i></a>
    {{--  <div class="Search">
        <a href="/search" class="transition">
        <div class="search-input">
            <img src="{{ asset('assets/images/icon/icon_search.png') }}">
            @lang('localize.search')
        </div>
        </a>
    </div>  --}}
    <div class="Search">
        <form action="/search">
        <img src="{{ asset('assets/images/icon/icon_search.png') }}">
        <input type="search" class="form-control" placeholder="@lang('localize.search')" results="5" name="search" value="{{ $search or '' }}">
        </form>
    </div>

    @if (Auth::check())
        <div class="Wallet">
            <div class="wallet-backdrop"></div>
            <div class="wallet-inner">
                <img src="{{ asset('assets/images/icon_wallet.png') }}">
                @foreach ($wallets as $wallet)
                <div class="list">
                    <label>{{ ucfirst($wallet->wallet->name) }}</label>
                    <h4>{{ $wallet->credit }}</h4>
                </div>
                @endforeach
                @if ($cus_details->special_wallet > 0)
                <div class="list">
                    <label>Hemma</label>
                    <h4>{{ $cus_details->special_wallet }}</h4>
                </div>
                @endif
            </div>
        </div>
    @endif

    <div class="MemberNav MemberRightNav">
        <ul>
            <li>
                <a href="javascript:void(0)" id="MoreToggle">
                    <img src="{{ asset('assets/images/icon/icon_more.png') }}">
                    <span>@lang('localize.more')</span>
                </a>
                <ul class="MoreMenu">
                    <li>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#country"><img src="{{ asset('assets/images/icon/icon_country.png') }}"> @lang('localize.country') <h5>{{ $country_name }}</h5></a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#language"><img src="{{ asset('assets/images/icon/icon_language.png') }}"> @lang('localize.lang') <h5>@lang('localize.language')</h5></a>
                    </li>
                    @if (Auth::check())
                        <li>
                            <a href="javascript:void(0)" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <img src="{{ asset('assets/images/icon/icon_logout.png') }}">
                                <label>@lang('localize.logout')</label>
                            </a>
                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display:none;">{{ csrf_field() }}</form>
                        </li>
                    @endif
                </ul>
            </li>
            <li class="WalletToggle">
                <a>
                <img src="assets/images/icon/icon_wallet.png">
                </a>
            </li>
            <li>
                @if (Auth::check())
                    <a href="/profile">
                        <img src="{{ asset('assets/images/icon/icon_account.png') }}">
                        <span>@lang('localize.profile')</span>
                    </a>
                @else
                    <a href="/login">
                        <img src="{{ asset('assets/images/icon/icon_login.png') }}">
                        <span>@lang('localize.login')</span>
                    </a>
                @endif
            </li>
            <li>
                <a href="/carts">
                    <img src="{{ asset('assets/images/icon/icon_shoppingcart.png') }}">
                    <div class="CartCount">{{count($carts)}}</div>
                    <span>{{ count($carts) }} @lang('localize.items')</span>
                </a>
            </li>
        </ul>
    </div>
</header>