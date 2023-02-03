<footer class="site-footer">

    <div class="grid__item footer_information">
        <div class="wrapper">
            <div class="grid-uniform">
                <div class="fi-links grid__item one-quarter small--one-whole medium--one-whole">
                    <div class="fi-title">{{trans('footer.Profile')}}</div>
                    <div class="fi-content">
                        <ul class="grid__item footer-link">
                            <li><a href="{{ url('profile/account') }}">@lang('localize.myaccount')</a></li>
                            <li><a href="{{ url('profile/order') }}">@lang('localize.mybuys')</a></li>
                            <li><a href="{{ url('profile/credit') }}">@lang('localize.vcoinLog')</a></li>
                            <li><a href="{{ url('profile/password') }}">@lang('localize.mypassword')</a></li>
                            <li><a href="{{ url('profile/newsecurecode') }}">@lang('localize.mysecurecode')</a></li>
                            {{-- <li><a href="/profile/account">{{trans('footer.myAccount')}}</a></li>
                            <li><a href="/profile/shippingaddress">{{trans('footer.myAddress')}}</a></li>
                            <li><a href="/profile/order">{{trans('footer.myOrderHistory')}}</a></li> --}}
                        </ul>
                    </div>
                </div>
                <div class="fi-links grid__item one-quarter small--one-whole medium--one-whole">
                    <div class="fi-title">{{trans('footer.company')}}</div>
                    <div class="fi-content">
                        <ul class="grid__item footer-link">
                            <li><a href="/info/aboutus">{{trans('footer.aboutUs')}}</a></li>
                            <li><a href="/info/returns">{{trans('footer.returns')}}</a></li>
                            <li><a href="/info/faq">{{trans('footer.faq')}}</a></li>
                            <li><a href="/info/prohibited_products">{{trans('footer.prohibitedProducts')}}</a></li>
                            <li><a href="/contact-us">{{trans('footer.contactUs')}}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="fi-about-block grid__item one-half small--one-whole medium--one-whole">
                    <div class="fi-title">
                        @lang('footer.locate_us')
                    </div>
                    <div class="fi-content">
                        {{-- <div class="grid__item one-third small--one-whole medium--one-whole">
                            <img src="{{ asset('assets/images/logo-b.png') }}" style="filter:grayscale(100%);">
                        </div> --}}
                        <div class="grid__item two-thirds small--one-whole medium--one-whole">
                            <ul class="group_information">
                                <li><i class="fa fa-map-marker"></i> No. 12A, Jalan Setia Dagang, U13/AK Seksyen U13, Setia Alam, 40170 Shah Alam, Selangor, Malaysia</li>
                                <li><i class="fa fa-phone"></i> (+60)3 3885 7018</li>
                                <li><i class="fa fa-envelope"></i> {{ env('MAIL_FROM_ADDRESS') }}</li>
                            </ul>
                            <div class="inline-list social-icons">
                                <a href="#" title="Twitter" class="icon-social twitter" data-toggle="tooltip" data-placement="top"><i class="fa fa-twitter-square"></i></a>
                                <a href="#" title="Facebook" class="icon-social facebook" data-toggle="tooltip" data-placement="top"><i class="fa fa-facebook-square"></i></a>
                                <a href="#" title="Google+" class="icon-social google" data-toggle="tooltip" data-placement="top"><i class="fa fa-google-plus-square"></i></a>
                                <a href="#" title="Pinterest" class="icon-social pinterest" data-toggle="tooltip" data-placement="top"><i class="fa fa-pinterest-square"></i></a>
                                <a href="#" title="Youtube" class="icon-social youtube" data-toggle="tooltip" data-placement="top"><i class="fa fa-youtube-square"></i></a>
                                <a href="#" title="Vimeo" class="icon-social vimeo" data-toggle="tooltip" data-placement="top"><i class="fa fa-vimeo-square"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="grid__item footer-bottom">
        <div class="wrapper">
            <div class="footer-copyright">
                <p>
                    &copy; {{ date("Y") }} @lang('common.mall_name')
                    {{-- &nbsp;|&nbsp; {{ trans('common.mall_name') }} {{ trans('localize.member_app') }} :
                    <a href="#" data-toggle="modal" data-target="#android_member"><img src="{{ asset('assets/images/google-play-badge.png') }}" height="30px"></a>
                    &nbsp; &nbsp;
                    <a href="#" data-toggle="modal" data-target="#ios_member"><img src="{{ asset('assets/images/app-store-badge.png') }}" height="30px"></a> --}}
                </p>
            </div>
        </div>
    </div>

    <!-- Modal Android Member-->
    {{--  <div class="modal fade" id="android_member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <i class="fa fa-android" aria-hidden="true"></i>&nbsp; Android Application Download
                </div>
                <div class="modal-body" style="text-align:center;">
                    <img src="{{ asset('assets/images/qr/android_member.jpg') }}" height="450px"/>
                    <a class="btn btn-xs" href="{{env('ANDROID_MEMBER')}}" target="_blank">CLICK TO DOWNLOAD ANDROID VERSION</a>
                </div>
            </div>
        </div>
    </div>  --}}

    <!-- Modal IOS Member-->
    {{--  <div class="modal fade" id="ios_member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <i class="fa fa-apple" aria-hidden="true"></i>&nbsp; iOS Application Download
                </div>
                <div class="modal-body" style="text-align:center;">
                    <img src="{{ asset('assets/images/qr/ios_member.jpg') }}" height="450px"/>
                    <a class="btn btn-xs" href="{{env('IOS_MEMBER')}}" target="_blank">CLICK TO DOWNLOAD IOS VERSION</a>
                </div>
            </div>
        </div>
    </div>  --}}

    <script type="text/javascript">
        $(function () {
            $(".fi-title").click(function () {
                $(this).toggleClass('opentab');
            });
        });
    </script>
</footer>