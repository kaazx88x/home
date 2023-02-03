<?php

Route::get('load_maincategory', 'AjaxController@load_maincategory');
Route::get('load_subcategory', 'AjaxController@load_subcategory');
Route::get('load_secsubcategory', 'AjaxController@load_secsubcategory');

Route::get('load_size', 'AjaxController@load_size');
Route::get('load_color', 'AjaxController@load_color');

Route::get('view_order/{id}', 'AjaxController@order_detail');
Route::get('print_invoice/{id}', 'AjaxController@print_invoice');
Route::get('view_shipment/{id}', 'AjaxController@view_shipment');
Route::get('accept_order/{id}/{status}', 'AjaxController@accept_order');
Route::get('update_shipment/{id}','AjaxController@update_shipment');
Route::post('update_shipment/{order_id}','AjaxController@update_shipment_submit');

Route::get('send_auction_winner/{oa_id}', 'AjaxController@send_auction_winner');
Route::post('send_auction_winner', 'AjaxController@send_auction_winner_submit');

Route::get('load_merchant_store', 'AjaxController@load_merchant_store');
Route::get('load_city','AjaxController@load_city');
// Route::get('merchant_emailcheck', 'AjaxController@merchant_emailcheck');
// Route::get('merchant_usernamecheck', 'AjaxController@merchant_usernamecheck');
Route::get('view_order_offline/{id}/{type}', 'AjaxController@order_offline_detail');
Route::get('print_order_offline/{id}/{type}/{type_inv}/{add}', 'AjaxController@order_offline_detail_print');
Route::get('download_pdf_inv/{id}/{type}/{type_inv}/{add}', 'AjaxController@get_inv_pdf');
Route::any('view_batch_inv/{type_inv}/{id}/{type}/{add?}', 'AjaxController@get_batch_inv');
Route::get('print_order_trans_ref/{type_inv}/{id}/{type}/{add?}', 'AjaxController@order_offline_trans_print');
Route::get('pdf_order_trans_ref/{type_inv}/{id}/{type}/{add?}', 'AjaxController@order_offline_trans_pdf');



Route::get('get_merchant_bank_info/{mer_id}', 'AjaxController@get_merchant_bank_info');

Route::get('vcoinlog/{id}', 'AjaxController@vcoinlog');
Route::get('gplog/{id}', 'AjaxController@gplog');

Route::get('load_state','AjaxController@load_state');

Route::get('edit_product_image/{mer_id}/{id}', 'AjaxController@edit_product_image');
Route::post('/product_image/edit', 'AjaxController@edit_product_image_submit');

Route::get('/get_product_category', 'AjaxController@product_category');
Route::get('/get_product_category/{id}', 'AjaxController@product_category');
Route::get('/product_pricing/edit/{mer_id}/{id}', 'AjaxController@edit_product_pricing');
Route::post('/product_pricing/edit', 'AjaxController@edit_product_pricing_submit');
// Route::any('/pricing_attribute/edit/{mer_id}/{pricing_id}', 'AjaxController@edit_pricing_attribute');
Route::any('/pricing_attribute_quantity/edit/{mer_id}/{pro_id}/{pricing_id}', 'AjaxController@edit_pricing_attribute_quantity');
Route::get('/product_detail/get_attribute_selection/{pro_id}', 'AjaxController@get_attribute_selection');

Route::get('/attribute/check_attribute_exist/{mer_id}/{pro_id}/{attribute_id}','AjaxController@check_attribute_exist');
Route::get('/attribute/remove/{pro_id}','AjaxController@remove_product_attribute');
Route::get('/edit_product_attribute/{attribute_id}/{pro_id}/{mer_id}', 'AjaxController@edit_product_attribute');
Route::get('/attribute/parent_check_attribute_exist/{mer_id}/{pro_id}/{attribute_id}','AjaxController@parent_check_attribute_exist');
Route::post('/update_attribute_parent/{pro_id}', 'AjaxController@update_attribute_parent_submit');
Route::get('/product/attribute_parent/delete/{attribute_id}/{option}', 'AjaxController@parent_delete_product_attribute');
Route::get('/product/attribute/delete/{attribute_id}/{option}', 'AjaxController@delete_product_attribute');
Route::get('/add_attribute_item', 'AjaxController@add_attribute_item_submit');

Route::get('/get_offline_category', 'AjaxController@offline_category');
Route::get('/get_offline_category/{id}', 'AjaxController@offline_category');

Route::get('/store_emailcheck', 'AjaxController@store_emailcheck');
Route::get('/store_usernamecheck', 'AjaxController@store_usernamecheck');

Route::get('/member_emailcheck', 'AjaxController@member_emailcheck');
Route::get('/member_usernamecheck', 'AjaxController@member_usernamecheck');
Route::get('/member_phone_check', 'AjaxController@member_phone_check');
Route::get('/sms_verification', 'AjaxController@send_tac');
Route::get('/check_tac', 'AjaxController@check_tac');
Route::get('/check_member_verification/{operation}', 'AjaxController@check_member_verification');

Route::get('/get_code_number_listing/{order_id}/{by}/{type}', 'AjaxController@get_code_number_listing');
Route::post('/description','AjaxController@saveimage');
Route::get('/load_name', 'AjaxController@load_name');
Route::get('/load_store_name', 'AjaxController@load_store_name');
Route::get('/load_merchant_name', 'AjaxController@load_merchant_name');

Route::get('/get_fund_withdraw_statement', 'AjaxController@get_fund_withdraw_statement');
Route::get('/download/ecard', 'AjaxController@download_ecard_template');
