<!-- Top Other -->
<div id="top-header" class="grid--full grid--table">
    <div class="wrapper">
        <a href="javascript:void(0)" class="top-link-toggle">
            <span class="fa fa-ellipsis-v"></span>
        </a>
        <div class="top-link">
            <ul>
                @if (Auth::check())
                <li>
                    <a href="/profile">
                        <span>{{ trans('localize.myaccount') }}</span>
                    </a>
                </li>
                @endif
                <li class="expand">
                    <a href="javascript:void(0)" class="expand-toggle">{{trans('front.language')}}</a>
                    <ul class="expandable-menu">
                        <li><a href="javascript:void(0)" class="locale" data-lang="en">English</a></li>
                        <li><a href="javascript:void(0)" class="locale" data-lang="cn">中文</a></li>
                        <li><a href="javascript:void(0)" class="locale" data-lang="my">Bahasa Melayu</a></li>
                    </ul>
                </li>
                <li class="country">
                    <a href="javascript:void(0)" class="country-toggle">{{$country_name}}</a>
                    <ul class="expandable-menu">
                         @foreach($locale_countries as $key => $co)
                            <li><a href="javascript:void(0)" class="countryloc" data-id="{{$co->co_id}}"><span class="flag-icon flag-icon-{{strtolower($co->co_code)}}"></span> {{$co->co_name}}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li>
                    @if (Auth::check())
                        <a href="javascript:void(0)" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out"></i> <span>{{ trans('localize.logout') }}</span>
                        </a>
                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display:none;">{{ csrf_field() }}</form>
                    @else
                        <a href="/login"><i class="fa fa-sign-in"></i> <span>{{ trans('localize.login') }} / {{ trans('localize.register') }}</span></a>
                    @endif
                    {{-- <a href="login.html">Login / Register</a> --}}
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="site-header">
    <div class="wrapper">
        <div id="main-header" class="grid--full grid--table">
            <div class="grid__item small--one-whole medium--one-whole two-eighths">
                <h1 class="site-header__logo large--left">
                    <a href="/" itemprop="url" class="site-header__logo-link">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Home Market Red" itemprop="logo">
                    </a>
                </h1>
            </div>
            <div class="grid__item small--one-whole medium--one-whole six-eighths mobile-bottom">
                <div class="large--hide medium-down--show navigation-icon">
                    <div class="grid">
                        <div class="grid__item one-half">
                            <div class="site-nav--mobile">
                                <button type="button" class="icon-fallback-text site-nav__link js-drawer-open-left" aria-controls="NavDrawer" aria-expanded="false">
                                    <span class="fa fa-bars" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="site-header__search">
                    <form action="{{ url('/search') }}" method="get" class="input-group search-bar">
                        <div class="collections-selector">
                            <select id="category" name="category" class="single-option-selector" data-option="collection-option">
                                <option value="all">{{trans('localize.allcategory')}}</option>
                                @foreach ($parents as $key => $menu)
                                    <option value="{{$menu->id}}" {{ (isset($category) && $category == $menu->id) ? 'selected' : ''}}>{{strtolower($menu->name)}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="text" id="search" name="search" value="{{ (isset($search)) ? $search : '' }}" placeholder="@lang('localize.search')" class="input-group-field st-default-search-input" aria-label="Search our store">
                        <span class="input-group-btn">
                        <button type="submit" class="btn icon-fallback-text">
                            <i class="fa fa-search"></i>
                            <span class="fallback-text"></span>
                        </button>
                        </span>
                    </form>
                </div>
                <div class="large--hide medium-down--show navigation-cart">
                    <div class="grid__item text-right">
                        <div class="site-nav--mobile">
                            {{-- <a href="cart.htm" class="js-drawer-open-right site-nav__link" aria-controls="CartDrawer" aria-expanded="false"> --}}
                            <a class="js-drawer-open-right site-nav__link">
                                <span class="icon-fallback-text">
									<span class="fa fa-shopping-basket" aria-hidden="true"></span>
									{{--  <span class="fa fa-shopping-basketx" aria-hidden="true"></span>  --}}
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid__item small--one-whole one-tenth medium-down--hide">
                <ul class="link-list">
                    <li class="header-cart">
                        <a href="#" class="site-header__cart-toggle js-drawer-open-right" aria-controls="CartDrawer" aria-expanded="false">
                            <i class="fa fa-shopping-basket"></i>
                            <span id="CartCount">{{count($carts)}}</span>
                            <span class="name">@lang('localize.shopping_cart')</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<!-- Navigation Bar -->

<div id="sticky-anchor"></div>
<nav class="nav-bar">
    <div class="wrapper">
        <!-- begin site-nav -->
        <ul class="site-nav" id="AccessibleNav">
            <li>
                <a href="/" class="site-nav__link">
                    <span>{{trans('front.home')}}</span>
                </a>
                {{--  <a class="site-nav__link"></a>  --}}
            </li>
            @foreach ($nav_featured as $featured)
                <li class="mega-menu site-nav--has-dropdown" aria-haspopup="true">
                    <a href="/products/{{base64_encode($featured['parent']->id)}}" class="site-nav__link"><span>{{$featured['parent']->name}}</span></a>
                    <ul class="site-nav__dropdown megamenu__dropdown megamenu_1" role="menu" style="width: 830px;">

                        @foreach ($featured['layer_one'] as $layer_one)
                        <li class="nav-sampletext grid__item large--one-quarter">
                            <ul >
                                <li class="list-title">
                                    <a href="/products/{{base64_encode($layer_one->id)}}">{{$layer_one->name}}</a>
                                </li>

                                @foreach ($layer_one['layer_two'] as $layer_two)
                                    <li class="list-unstyled nav-sub-mega"><a href="/products/{{base64_encode($layer_two->id)}}">{{ucwords(strtolower($layer_two->name))}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>

        <div class="fix-cart">
            <a href="javascript:void(0)" class="site-header__cart-toggle js-drawer-open-right" aria-controls="CartDrawer" aria-expanded="false">
                <i class="fa fa-shopping-basket"></i>
            </a>
        </div>

    </div>
</nav>