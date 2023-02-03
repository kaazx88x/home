<div class="top-header">
    <div class="container">
        @if (!str_contains(url()->current(), '/admin'))
        <ul class="main-nav">
            <li class="dropdown">
                <a href="javascript:void(0)" class="expand-toggle" data-toggle="dropdown">@lang('front.language') <i class="fa fa-angle-down"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="javascript:void(0)" class="locale" data-lang="en">English</a></li>
                    <li><a href="javascript:void(0)" class="locale" data-lang="cn">中文</a></li>
                </ul>
            </li>
        </ul>
        @endif
        <ul class="pull-right">
            @if (auth('merchants')->check())
                <li>
                    <a href="javascript:void(0)" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out"></i> <span>{{ trans('localize.logout') }}</span>
                    </a>
                    <form id="logout-form" action="{{ url('merchant/logout') }}" method="POST" style="display:none;">{{ csrf_field() }}</form>
                </li>
            @else
                <li><a href="{{ url('merchant/login') }}"><i class="fa fa-sign-in"></i> {{ trans('localize.login') }}</a></li>
                <li><a href="{{ url('merchant/register') }}"><i class="fa fa-arrow-circle-up"></i> {{ trans('localize.register') }}</a></li>
            @endif
        </ul>
    </div>
</div>