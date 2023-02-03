<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::auth();

// Registration Routes...
$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
$this->post('register', 'Auth\RegisterController@register');
$this->post('password/phone', 'Auth\ForgotPasswordController@sendResetLinkPhone');
$this->get('password/phone/reset/{phone}/{token}', 'Auth\ResetPasswordController@showResetPhoneForm');
$this->post('password/phone/reset/{phone}', 'Auth\ResetPasswordController@resetPhone');

Route::get('/resend/activation/{cus_id}/{cus_email}', 'Auth\LoginController@resendActivation');
Route::any('/resend/activation/{cus_id}', ['uses' =>'Auth\LoginController@resendActivationForm', 'middleware' => 'memberThrottle.throttle:5,1']);

Route::group(array('middleware' => 'profile_update'), function()
{
    // Route::get('/', 'Front\ProfileController@accountInfo')->middleware('auth:web');
    Route::get('/', 'Front\HomeController@index');
    Route::get('/home', 'Front\HomeController@index');
    //Route::any('/contact-us', 'Front\HomeController@contactUs');
    Route::any('/info/{url_slug}', 'Front\HomeController@cms');
    Route::any('/search', 'Front\HomeController@search');
    Route::get('/about', 'Front\HomeController@about_us')->name('about-us');

    Route::get('email/verification/{token}', 'Front\ProfileController@verifyEmail')->name('email.verify');

/* Home Product Page */
Route::get('/products', 'Front\ProductController@all');
//Route::get('/products/{id}', 'Front\ProductController@category');
Route::get('/products/category/{parent}', 'Front\ProductController@category');
Route::get('/products/category/{parent}/{child}', 'Front\ProductController@category');
Route::get('/products/detail/{id}', 'Front\ProductController@detail');

    /* Cart */
    Route::get('/carts', 'Front\CartController@cart');
    Route::post('/carts/add', 'Front\CartController@add');
    Route::any('/carts/delete', 'Front\CartController@delete');
    Route::any('/carts/update', 'Front\CartController@update');
    Route::any('/carts/checkout', ['middleware'=>'auth', 'uses'=>'Front\CartController@checkout']);
    Route::any('/carts/update/attribute', 'Front\CartController@update_cart_attribute');
    Route::get('/carts/shippingaddress/list', 'Front\ProfileController@shippingAddress');
});

/* Set Lang|Country Locale */
Route::any('/home/setlocale', 'Front\HomeController@setlocale');
Route::any('/home/setcountry', 'Front\HomeController@setcountry');

Route::get('getDate',function() {
    $date = date('Y-m-d H:i:s');
    return json_encode(['dateData'=>$date]);
});

Route::get('/cron/dailycron', 'Cron\OrderProcessController@dailyCron');

Route::get('testsend', function (){
    Mail::raw('emails.reminder',  function ($m){
        $m->from(config('mail.from.address'), 'Your Application');

        $m->to('keansin@gmail.com')->subject('Your Reminder!');
    });
});

Route::any('/local-biz/detail/{id}', 'Front\LocalBizController@details');

Route::get('/email', function () {
    return view('front.emails.topup')->with('type', 'mei_point');
});