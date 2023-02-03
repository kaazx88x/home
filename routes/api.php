<?php

// use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1', 'middleware' => 'auth.token:api'], function () {
    Route::get('version', 'Api\V1\GeneralController@getVersion');

    // currency function to be obsolete
    Route::get('currency', 'Api\V1\GeneralController@getCurrency');
    Route::get('rates', 'Api\V1\GeneralController@getGeneralRates');
    Route::get('countries', 'Api\V1\GeneralController@getCountries');
    Route::get('categories', 'Api\V1\GeneralController@getCategories');
    Route::get('location', 'Api\V1\GeneralController@getLocation');

    Route::post('merchant/login', 'Api\V1\AuthController@merchantLogin');
    Route::post('merchant/order', 'Api\V1\OfflinePaymentController@createOrder');
    Route::get('merchant/order/status', 'Api\V1\OfflinePaymentController@checkOrderStatusByMerchant');
    Route::post('merchant/order/cancel', 'Api\V1\OfflinePaymentController@merchantCancelOrder');

    Route::post('member/login', 'Api\V1\AuthController@memberLogin');
    Route::get('member/order', 'Api\V1\OfflinePaymentController@getOrder');
    Route::post('member/order/claim', 'Api\V1\OfflinePaymentController@claimOrder');
    Route::post('member/order/confirm', 'Api\V1\OfflinePaymentController@confirmOrder');
    Route::post('member/order/cancel', 'Api\V1\OfflinePaymentController@memberCancelOrder');
    Route::get('member/order/history', 'Api\V1\OfflinePaymentController@memberOrderHistory');

    // OfflineStore
    Route::get('offline-store/listing', 'Api\V1\OfflineStoreController@getStoreListing');
    Route::get('offline-store/search', 'Api\V1\OfflineStoreController@searchStore');
    Route::post('offline-store/rating-add', 'Api\V1\OfflineStoreController@addRating');
    Route::post('offline-store/review-add', 'Api\V1\OfflineStoreController@addReview');
    Route::get('offline-store/reviews', 'Api\V1\OfflineStoreController@getReviews');
    Route::get('offline-store/gallery', 'Api\V1\OfflineStoreController@getImages');
    Route::get('offline-store', 'Api\V1\OfflineStoreController@getStore');
    Route::get('offline-store/map', 'Api\V1\OfflineStoreController@getStoreMap');
    Route::get('offline-store/range', 'Api\V1\OfflineStoreController@getStoreRange');

    Route::get('offline-store/home', 'Api\V1\OfflineStoreController@getHome');

    // Member Create order
    Route::get('member/check/merchant', 'Api\V1\OfflinePaymentController@checkMerchant');
    Route::get('member/check/store', 'Api\V1\OfflinePaymentController@checkStore');
    // Route::post('member/order/create', 'Api\V1\OfflinePaymentController@memberCreateOrder');
    Route::post('member/order/create', 'Api\V1\OfflinePaymentController@memberCreatePayment');

    // Order History Report
    Route::get('member/order/history/online', 'Api\V1\MemberController@online_order_history');
    Route::get('member/order/history/offline', 'Api\V1\MemberController@offline_order_history');
    Route::get('merchant/order/history/offline', 'Api\V1\MemberController@merchant_offline_order_history');

    Route::get('member/details', 'Api\V1\MemberController@memberInfo');
    Route::get('merchant/details', 'Api\V1\MemberController@merchantInfo');

    // Check Version 10 to beta
    Route::get('goBeta', 'Api\V1\GeneralController@getVersionBeta');
});

// API for Sponsor
Route::group(['prefix' => 'topup', 'middleware' => 'auth.token:api'], function () {
    Route::post('confirm', 'Api\SVI\ApiController@confirm');
    Route::post('{type}', 'Api\SVI\ApiController@topup'); //type:vt_topup;gp_topup
});