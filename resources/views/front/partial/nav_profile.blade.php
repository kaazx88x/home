<div class="column col-xs-12 col-sm-3" id="left_column">
    <!-- block category -->
    <div class="block left-module">
        <p class="title_block btn-filter">{{trans('localize.accountInformation')}}</p>
        <div class="block_content">
            <!-- layered -->
            <div class="layered layered-category">
                <div class="layered-content">
                    <ul class="tree-menu">
                        <li {{Request::is('profile/account')?'class=active':''}}><span></span><a href="/profile/account">{{trans('localize.myaccount')}}</a></li>
                        <li {{Request::is('profile/password')?'class=active':''}}><span></span><a href="/profile/password">{{trans('localize.mypassword')}}</a></li>
                        @if (!empty($cu_code))
                            <li {{Request::is('profile/updatesecurecode')?'class=active':''}}><span></span><a href="/profile/updatesecurecode">{{trans('localize.mysecurecode')}}</a></li>
                        @else
                            <li {{Request::is('profile/newsecurecode')?'class=active':''}}><span></span><a href="/profile/newsecurecode"><font color="red">{{trans('localize.mysecurecode')}}</font></a></li>
                        @endif
                        <li {{Request::is('profile/shippingaddress')?'class=active':''}}><span></span><a href="/profile/shippingaddress">{{trans('localize.myaddress')}}</a></li>
                        <li {{Request::is('profile/order')?'class=active':''}}><span></span><a href="/profile/order">{{trans('localize.mybuys')}}</a></li>
                        <li {{Request::is('profile/auction')?'class=active':''}}><span></span><a href="/profile/auction">{{trans('localize.mybids')}}</a></li>
                        <li {{Request::is('profile/vcoin')?'class=active':''}}><span></span><a href="/profile/vcoin">{{trans('localize.vcoinLog')}}</a></li>
                        <li {{Request::is('profile/gamepoint')?'class=active':''}}><span></span><a href="/profile/gamepoint">{{trans('localize.gamepointLog')}}</a></li>

                    </ul>
                </div>
            </div>
            <!-- ./layered -->
        </div>
    </div>
    <!-- ./block category  -->
</div>
