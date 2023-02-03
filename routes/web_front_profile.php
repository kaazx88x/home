<?php

//profile routes
Route::group(array('prefix' => 'profile', 'middleware' => ['auth:web', 'profile_update']), function()
{
    Route::get('/', 'Front\ProfileController@account');
    Route::any('account', 'Front\ProfileController@accountInfo');
    Route::any('upload', 'Front\ProfileController@upload_avatar');
    Route::any('shippingaddress', 'Front\ProfileController@shippingList');
    Route::any('shippingaddress/edit/{id}', 'Front\ProfileController@shippingAddressUpdate');
    Route::any('shippingaddress/delete/{id}', 'Front\ProfileController@shippingAddressDelete');
    Route::any('shippingaddress/setdefault/{id}', 'Front\ProfileController@shippingAddressDefault');
    Route::any('shippingaddress/add', 'Front\ProfileController@shippingAddressAdd');
    Route::any('shippingaddress/update', 'Front\ProfileController@shippingAddressUpdate');
    Route::get('order', 'Front\ProfileController@orderHistory');
    Route::get('credit', 'Front\ProfileController@creditLog');
    Route::any('password', 'Front\ProfileController@passwordUpdate');
    Route::any('securecode', 'Front\ProfileController@securecode');
    // Route::get('securecode/reset', 'Front\ProfileController@resetsecurecode');
    Route::get('security/question', 'Front\ProfileController@security_question');
    Route::post('security/question', 'Front\ProfileController@security_question_submit');
    Route::get('logout', 'Auth\LoginController@logout');
    Route::any('phone', 'Front\ProfileController@phone');
    Route::post('phone/update', 'Front\ProfileController@phoneUpdate');
    Route::any('email', 'Front\ProfileController@emailUpdate');

    Route::get('accept_orders/{id}/{status}', 'Front\ProfileController@acceptorders');
    Route::get('send/verification/email', 'Front\ProfileController@sendVerificationMail')->name('email.verify.send');
    // Route::get('email/verification/{token}', 'Front\ProfileController@verifyEmail')->name('email.verify');

    Route::get('phone/verify', 'Front\ProfileController@verifyPhone')->name('phone.verify.send');
    Route::post('phone/verify', 'Front\ProfileController@verifyPhoneSubmit')->name('phone.verify.submit');

    Route::get('/limit', 'Front\ProfileController@limit');
});

Route::any('profile/update', ['uses' => 'Front\ProfileController@update_info', 'middleware' => 'auth:web']);
Route::any('profile/update/success', ['uses' => 'Front\ProfileController@update_success', 'middleware' => 'auth:web']);
