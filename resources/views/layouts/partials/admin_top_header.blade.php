<div class="top-header">
    <div class="container">
        <ul>
            @if (auth('admins')->check())
                <li>
                    <a href="javascript:void(0)" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out"></i> <span>{{ trans('localize.logout') }}</span>
                    </a>
                    <form id="logout-form" action="{{ url('admin/logout') }}" method="POST" style="display:none;">{{ csrf_field() }}</form>
                </li>
            @else
                <li><a href="{{ url('admin/login') }}"><i class="fa fa-sign-in"></i> {{ trans('localize.login') }}</a></li>
            @endif
        </ul>
    </div>
</div>