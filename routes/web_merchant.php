<?php

// Merchant route
Route::get('merchant/login', 'Merchant\Auth\AuthController@showLoginForm')->name('merchant.login');
Route::post('merchant/login', 'Merchant\Auth\AuthController@login');

Route::get('merchant/register', 'Merchant\Auth\RegisterController@showRegistrationForm');
Route::post('merchant/register', 'Merchant\Auth\RegisterController@register');

Route::get('merchant/usernamecheck', 'Merchant\Auth\RegisterController@ajaxMerchantUsernameCheck');
Route::get('merchant/emailcheck', 'Merchant\Auth\RegisterController@ajaxMerchantEmailCheck');

Route::get('merchant/password/reset', 'Merchant\Auth\ForgotPasswordController@showLinkRequestForm')->name('merchant.password.request');
Route::post('merchant/password/email', 'Merchant\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('merchant/password/reset/{token}', 'Merchant\Auth\ResetPasswordController@showResetForm')->name('merchant.password.reset');
Route::post('merchant/password/reset', 'Merchant\Auth\ResetPasswordController@reset');

Route::get('merchant/activation/{token}', 'Merchant\Auth\AuthController@activateUser')->name('merchant.activate');
Route::get('merchant/activation-success', 'Merchant\Auth\AuthController@activationSuccess');
Route::get('/merchant/resend/activation/{mer_id}/{mer_email}', 'Merchant\Auth\AuthController@resendActivation');
Route::any('/merchant/resend/activation/{mer_id}', ['uses' =>'Merchant\Auth\AuthController@resendActivationForm', 'middleware' => 'merchantThrottle.throttle:5,1']);

Route::group(['prefix' => 'merchant', 'middleware' => 'merchant'], function() {
    Route::get('/logout', 'Merchant\Auth\AuthController@logout');
    Route::get('/', ['uses' => 'Merchant\HomeController@index']);
    Route::get('/merchant-user', ['uses' => 'Merchant\MerchantUserController@index']);
    Route::any('/merchant-user/create', ['uses' => 'Merchant\MerchantUserController@create']);
    Route::any('/merchant-user/edit/{id}', ['uses' => 'Merchant\MerchantUserController@edit']);

    Route::get('/product/add', 'Merchant\ProductController@add_product');
    Route::post('/product/add', 'Merchant\ProductController@add_product_submit');
    Route::get('/product/edit/{pro_id}', 'Merchant\ProductController@edit_product');
    Route::post('/product/edit/{pro_id}', 'Merchant\ProductController@edit_product_submit');
    Route::get('/product/description/{pro_id}', 'Merchant\ProductController@product_description');
    Route::post('/product/description/{pro_id}', 'Merchant\ProductController@product_description_submit');

    Route::any('/product/view/{pro_id}', 'Merchant\ProductController@view_product');
    Route::get('/product/manage', 'Merchant\ProductController@manage_product');
    Route::get('/product/block/{pro_id}/{type}', 'Merchant\ProductController@change_product_status');

    Route::get('/product/sold', 'Merchant\ProductController@sold_product');
    Route::get('/product/shipping', 'Merchant\ProductController@manage_product_shipping_details');

    Route::get('/profile','Merchant\MerchantUserController@get_merchant_profile');
    // Route::any('/profile/edit','Merchant\MerchantUserController@edit_merchant_profile');
    Route::any('/profile/password','Merchant\MerchantUserController@update_password');

    Route::get('/transaction/product/{operation}','Merchant\TransactionController@product_transaction');
    Route::get('/transaction/offline','Merchant\TransactionController@order_offline');
    Route::post('/transaction/product/batch/update/{mer_id}/{operation}','Merchant\TransactionController@update_batch_transaction');
    Route::get('/transaction/online/complete/{order_id}','Merchant\TransactionController@completing_merchant_order');

    Route::get('/fund/report', 'Merchant\FundController@fund_report');
    Route::get('/fund/withdraw', 'Merchant\FundController@fund_withdraw');
    Route::post('/fund/withdraw', 'Merchant\FundController@fund_withdraw_submit');

    Route::get('/store/manage', 'Merchant\StoreController@manage');
    // Route::get('/store/add', 'Merchant\StoreController@add');
    // Route::post('/store/add', 'Merchant\StoreController@add_submit');
    Route::get('/store/edit/{stor_id}/', 'Merchant\StoreController@edit');
    Route::post('/store/edit/{stor_id}/', 'Merchant\StoreController@edit_submit');
    Route::get('/store/limit/{stor_id}', 'Merchant\StoreController@limit');

    Route::post('/setlocale', 'Merchant\HomeController@setlocale');

    Route::get('/credit/log', 'Merchant\MerchantUserController@merchant_credit_log');

    //Product Image
    Route::get('/product/image/save_order', 'Merchant\ProductImageController@product_image_saveorder');
    Route::get('/product/image/set_main_image/{id}', 'Merchant\ProductImageController@set_main_image');
    Route::get('/product/image/toggle_image_status/{id}', 'Merchant\ProductImageController@toggle_image_status');
    Route::get('/product/image/view/{pro_id}', 'Merchant\ProductImageController@view_product_image');
    Route::post('/product/image/add', 'Merchant\ProductImageController@upload_product_image');
    Route::get('/product/image/delete/{id}', 'Merchant\ProductImageController@delete_product_image');

    //product e-card
    Route::get('/product/code/listing/{pro_id}', 'Merchant\ProductController@ecard_listing');
    Route::post('/product/code/upload/{pro_id}', 'Merchant\ProductController@ecard_upload');
    // Route::get('/product/code/redeem/{id}/{pro_id}', 'Merchant\ProductController@ecard_redeem');

    //product Pricing
    Route::get('/product/pricing/{pro_id}', 'Merchant\ProductController@product_pricing');
    Route::post('/product/pricing/add/{pro_id}', 'Merchant\ProductController@product_pricing_submit');
    Route::get('/product/toggle_pricing_status/{id}', 'Merchant\ProductController@update_pricing_status');
    Route::get('/product/delete_product_pricing/{id}', 'Merchant\ProductController@delete_product_pricing');
    Route::post('/product/pricing/status/batch_update/{pro_id}', 'Merchant\ProductController@update_pricing_status_batch');

    //product quantity log
    Route::get('/product/quantity/{id}', 'Merchant\ProductController@view_product_quantity_log');

    //product attribute
    Route::get('/product/attribute/{pro_id}', 'Merchant\ProductController@manage_attribute');
    Route::post('/product/attribute/{pro_id}', 'Merchant\ProductController@update_attribute');
    Route::post('/product/attribute/{pro_id}/add', 'Merchant\ProductController@add_attribute_submit');
    Route::get('/product/attribute/{pro_id}/delete/{attribute_id}/{option}', 'Merchant\ProductController@delete_product_attribute');

    // product category filter
    Route::get('/product/filter/{pro_id}', 'Merchant\ProductController@manage_filter');
    Route::post('/product/filter/{pro_id}', 'Merchant\ProductController@update_filter');

    //store user
    Route::get('/store/user/manage', 'Merchant\StoreUserController@manage');
    Route::get('/store/user/add', 'Merchant\StoreUserController@add');
    Route::post('/store/user/add', 'Merchant\StoreUserController@add_submit');
    Route::get('/store/user/permission/{user_id}', 'Merchant\StoreUserController@store_permission');
    Route::get('/store/user/status/{user_id}/{status}', 'Merchant\StoreUserController@toggle_user_status');
    Route::get('/store/user/edit/{user_id}', 'Merchant\StoreUserController@edit');
    Route::post('/store/user/edit', 'Merchant\StoreUserController@edit_submit');
    Route::post('/store/user/reset_password', 'Merchant\StoreUserController@reset_password');

    //export
    Route::get('/export/product_orders/{operation}', 'Merchant\ExportController@product_orders');
    Route::get('/export/order_offline', 'Merchant\ExportController@order_offline');
});
