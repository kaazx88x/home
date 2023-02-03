<?php

Route::group(['prefix' => 'v2'], function () {

    // Route::get('goBeta', 'Api\V2\GeneralController@goBeta');

    Route::post('member/register-step1', 'Api\V2\member\AuthController@validate_tac');
    Route::post('member/register-step2', 'Api\V2\member\AuthController@validate_email_password');
    Route::post('member/register', 'Api\V2\member\AuthController@register');
    Route::post('member/register-tac', 'Api\V2\member\AuthController@register_tac');
    Route::post('member/login', 'Api\V2\member\AuthController@login');
    Route::post('member/phone-login', 'Api\V2\member\AuthController@phone_login');
    Route::post('member/forget-password', 'Api\V2\member\AuthController@forget_password');

    Route::get('version', 'Api\V2\GeneralController@getVersion');
    Route::get('rates', 'Api\V2\GeneralController@getGeneralRates');
    Route::get('currency', 'Api\V2\GeneralController@getCurrency');
    Route::get('countries', 'Api\V2\GeneralController@getCountries');
    Route::get('categories', 'Api\V2\GeneralController@getCategories');
    Route::get('location', 'Api\V2\GeneralController@getLocation');
    Route::get('states', 'Api\V2\GeneralController@getStates');
    Route::get('faqs', 'Api\V2\GeneralController@getFaq');

    Route::group(['prefix' => 'member', 'middleware' => ['auth.api:api_members']], function () {
        Route::get('logout', 'Api\V2\member\AuthController@logout');

        Route::get('details', 'Api\V2\member\ProfileController@memberInfo');

        //member claim order
        Route::post('order/claim', 'Api\V2\member\PaymentController@claimOrder');
        Route::post('order/confirm', 'Api\V2\member\PaymentController@confirmOrder');
        Route::post('order/cancel', 'Api\V2\member\PaymentController@cancelOrder');

        // Member Create order
        Route::get('check/store', 'Api\V2\member\PaymentController@checkStore');
        Route::post('order/create', 'Api\V2\member\PaymentController@createPayment');

        // Member Profile
        Route::post('/history/offline-order', 'Api\V2\member\ProfileController@offline_order_history');
        Route::post('/update/name', 'Api\V2\member\ProfileController@update_name');
        Route::post('/update/address', 'Api\V2\member\ProfileController@update_address');
        Route::post('/update/avatar', 'Api\V2\member\ProfileController@update_avatar');
        Route::post('/history/online-order', 'Api\V2\member\ProfileController@online_order_history');
        Route::post('/update/password', 'Api\V2\member\ProfileController@update_password');
        Route::post('/update/securecode', 'Api\V2\member\ProfileController@update_securecode');
        Route::post('/update/securecode-autogenerate', 'Api\V2\member\ProfileController@autogenerate_securecode');

        // Offline Store
        Route::get('offline-store', 'Api\V2\member\StoreController@getStore');
        Route::get('offline-store/listing', 'Api\V2\member\StoreController@getStoreListing');
        Route::get('offline-store/gallery', 'Api\V2\member\StoreController@getImages');
        Route::get('offline-store/reviews', 'Api\V2\member\StoreController@getReviews');
        Route::post('offline-store/review-add', 'Api\V2\member\StoreController@addReview');
        Route::get('offline-store/map', 'Api\V2\member\StoreController@getStoreMap');
        Route::get('offline-store/home', 'Api\V2\member\StoreController@getHome');

        Route::get('/events/ticket', 'Api\V2\member\EventController@ticket_listing');

        Route::get('/lucky-draw/redeem', 'Api\V2\member\LuckyDrawController@redeem');
    });

    Route::post('merchant/login', 'Api\V2\merchant\AuthController@login');

    Route::group(['prefix' => 'merchant', 'middleware' => ['auth.api:api_storeusers,api_merchants']], function () {
        Route::get('logout', 'Api\V2\merchant\AuthController@logout');

        // merchant info
        Route::get('details', 'Api\V2\merchant\ProfileController@merchantInfo');

        // merchant order
        Route::post('order/create', 'Api\V2\merchant\PaymentController@createOrder');
        Route::get('order/status', 'Api\V2\merchant\PaymentController@checkOrderStatusByMerchant');
        Route::post('order/cancel', 'Api\V2\merchant\PaymentController@merchantCancelOrder');

        Route::get('order/coupon/status', 'Api\V2\merchant\PaymentController@couponStatus');
        Route::post('order/coupon/redeem', 'Api\V2\merchant\PaymentController@redeemCoupon');

        Route::get('order/ticket/status', 'Api\V2\merchant\PaymentController@ticketStatus');
        Route::post('order/ticket/redeem', 'Api\V2\merchant\PaymentController@redeemTicket');

        Route::post('/history/offline-order', 'Api\V2\merchant\ProfileController@offline_order_history');

        //merchant events
        Route::get('/events', 'Api\V2\merchant\EventController@events');
        Route::get('/events/ticket', 'Api\V2\merchant\EventController@ticket_listing');
    });
});
