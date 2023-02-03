<footer class="MainFooter">
    &copy; {{ date("Y") }} @lang('common.mall_name')
    <div class="language" data-toggle="modal" data-target="#language">@lang('localize.lang')</div>
    <span class="about-link"><a href="{{ route('about-us') }}">@lang('localize.about_mall')</a></span>
</footer>