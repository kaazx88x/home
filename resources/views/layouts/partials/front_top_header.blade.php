<div class="top-header">
    <div class="container">
        <ul>
            @if (auth('web')->check())
                <li>
                    <a href="javascript:void(0)" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="fa fa-power-off"></i> <span>{{ trans('localize.logout') }}</span>
                    </a>
                    <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display:none;">{{ csrf_field() }}</form>
                </li>
            @else
                <li><a href="{{ url('login') }}"><i class="fa fa-sign-in"></i> {{ trans('localize.login') }}</a></li>
                <li><a href="{{ url('register') }}"><i class="fa fa-arrow-circle-up"></i> {{ trans('localize.register') }}</a></li>
            @endif
        </ul>
    </div>
</div>