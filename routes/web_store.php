<?php

// Store User Route
Route::get('/store/login', 'Merchant\Auth\StoreAuthController@showLoginForm')->name('store.login');
Route::post('/store/login', 'Merchant\Auth\StoreAuthController@login');

Route::get('store/password/reset', 'Merchant\Auth\StoreForgotPasswordController@showLinkRequestForm')->name('store.password.request');
Route::post('store/password/email', 'Merchant\Auth\StoreForgotPasswordController@sendResetLinkEmail');
Route::get('store/password/reset/{token}', 'Merchant\Auth\StoreResetPasswordController@showResetForm')->name('store.password.reset');
Route::post('store/password/reset', 'Merchant\Auth\StoreResetPasswordController@reset');

Route::get('store/activation/{token}', 'Merchant\Auth\StoreAuthController@activateUser')->name('storeuser.activate');
Route::post('store/activation/{token}', 'Merchant\Auth\StoreAuthController@activateUser');
Route::get('store/activation-success', 'Merchant\Auth\StoreAuthController@activationSuccess');

Route::group(['prefix' => 'store', 'middleware' => 'storeuser'], function() {
    Route::get('/logout', 'Merchant\Auth\StoreAuthController@logout');
    Route::get('/', ['uses' => 'Merchant\HomeController@index']);

    Route::get('/profile/edit','Merchant\StoreUserController@edit_profile');
    Route::post('/profile/edit','Merchant\StoreUserController@edit_profile_submit');
    Route::get('/password/edit', ['uses' => 'Merchant\StoreUserController@change_password']);
    Route::post('/password/edit', ['uses' => 'Merchant\StoreUserController@change_password_submit']);

    Route::get('/product/edit/{pro_id}', 'Merchant\ProductController@edit_product');
    Route::post('/product/edit/{pro_id}', 'Merchant\ProductController@edit_product_submit');

    Route::any('/product/view/{pro_id}', 'Merchant\ProductController@view_product');
    Route::get('/product/manage', 'Merchant\ProductController@manage_product');

    //Product Image
    Route::get('/product/image/save_order', 'Merchant\ProductImageController@product_image_saveorder');
    Route::get('/product/image/set_main_image/{id}', 'Merchant\ProductImageController@set_main_image');
    Route::get('/product/image/toggle_image_status/{id}', 'Merchant\ProductImageController@toggle_image_status');
    Route::get('/product/image/view/{pro_id}', 'Merchant\ProductImageController@view_product_image');
    Route::post('/product/image/add', 'Merchant\ProductImageController@upload_product_image');
    Route::get('/product/image/delete/{id}', 'Merchant\ProductImageController@delete_product_image');

    //product attribute
    Route::get('/product/attribute/{pro_id}', 'Merchant\ProductController@manage_attribute');
    Route::post('/product/attribute/{pro_id}', 'Merchant\ProductController@update_attribute');
    Route::post('/product/attribute/{pro_id}/add', 'Merchant\ProductController@add_attribute_submit');
    Route::get('/product/attribute/{pro_id}/delete/{attribute_id}/{option}', 'Merchant\ProductController@delete_product_attribute');

    // product category filter
    Route::get('/product/filter/{pro_id}', 'Merchant\ProductController@manage_filter');
    Route::post('/product/filter/{pro_id}', 'Merchant\ProductController@update_filter');

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

    //product transaction
    Route::get('/transaction/product/{operation}','Merchant\TransactionController@product_transaction');
    Route::get('/transaction/offline','Merchant\TransactionController@order_offline');
    Route::post('/transaction/product/batch/update/{mer_id}/{operation}','Merchant\TransactionController@update_batch_transaction');
});