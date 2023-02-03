<?php

// Admin route
Route::get('/admin/login', 'Admin\Auth\AuthController@showLoginForm')->name('admin.login');
Route::post('/admin/login', 'Admin\Auth\AuthController@login');

// Route::post('/admin/password/reset','Admin\Auth\PasswordController@reset');
// Route::get('/admin/password/reset/{token?}', 'Admin\Auth\PasswordController@showResetForm');
// Route::post('/admin/password/email', 'Admin\Auth\PasswordController@sendResetLinkEmail');
//Route::post('/admin/password/reset', 'Admin\Auth\PasswordController@reset');

//Route::get('/admin/password/email', 'Admin\Auth\PasswordController@getEmail');
//Route::post('/admin/password/email', 'Admin\Auth\PasswordController@postEmail');
//Route::get('/admin/password/reset', 'Admin\Auth\PasswordController@getReset');
//Route::post('/admin/password/reset', 'Admin\Auth\PasswordController@postReset');

Route::get('admin/password/reset', 'Admin\Auth\ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
Route::post('admin/password/email', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('admin/password/reset/{token}', 'Admin\Auth\ResetPasswordController@showResetForm')->name('admin.password.reset');
Route::post('admin/password/reset', 'Admin\Auth\ResetPasswordController@reset');

Route::get('update_fund_withdraw_status/{wd_id}/{status}', 'Admin\TransactionController@update_fund_withdraw_status');
Route::get('update_product_status/{pro_id}/{status}', 'Admin\ProductController@update_product_status');
Route::get('update_merchant_status/{mer_id}/{status}', 'Admin\MerchantController@update_merchant_status');
Route::get('update_store_status/{store_id}/{status}', 'Admin\StoreController@update_store_status');

Route::group(['prefix' => 'admin', 'middleware' => ['permission:']], function() {
    Route::get('/logout', 'Admin\Auth\AuthController@logout');
    Route::get('/', 'Admin\HomeController@index');

    Route::group(['prefix' => 'administrator'], function() {
        // ROLES
        Route::group(['prefix' => 'role', 'middleware' => ['permission:adminmanagelist']], function() {
            Route::get('/', ['uses' => 'Admin\AdministratorController@manage_role']);
            Route::get('/edit/{role_id}', ['uses' => 'Admin\AdministratorController@edit_role']);

            Route::group([ 'middleware' => ['permission:adminmanagecreate']], function() {
                Route::get('/add', ['uses' => 'Admin\AdministratorController@add_role']);
                Route::post('/add', ['uses' => 'Admin\AdministratorController@add_role_submit']);
            });

            Route::group([ 'middleware' => ['permission:adminmanageedit']], function() {
                Route::post('/edit/{role_id}', ['uses' => 'Admin\AdministratorController@edit_role_submit']);
            });

            Route::get('/delete/{role_id}', ['uses' => 'Admin\AdministratorController@role_delete']);
        });

        // USER
        Route::group(['prefix' => 'user', 'middleware' => ['permission:adminmanageuserlist']], function() {
            Route::get('/', ['uses' => 'Admin\AdminUserController@index'] );
            Route::any('/edit/{id}', ['uses' => 'Admin\AdminUserController@edit']);

            Route::group(['middleware' => ['permission:adminmanageusercreate']], function() {
                Route::any('/add', ['uses' => 'Admin\AdminUserController@create']);
            });

            Route::group(['middleware' => ['permission:adminmanageuserresetpassword']], function() {
                Route::any('/reset_password/{id}', ['uses' => 'Admin\AdminUserController@reset_password']);
            });

            Route::group(['middleware' => ['permission:adminmanageuserlock']], function() {
                Route::any('/lock/{adm_id}/{type}', 'Admin\AdminUserController@change_admin_lock');
            });
        });
    });

    // SETTING - COURIER
    Route::group(['middleware' => ['permission:settingcourierlist']], function() {
        Route::get('/setting/courier', ['uses' => 'Admin\SettingController@courier']);

        Route::group(['middleware' => ['permission:settingcouriercreate']], function() {
            Route::any('/setting/courier/add', ['uses' => 'Admin\SettingController@courier_add']);
        });

        //Route::group(['middleware' => ['permission:settingcourieredit']], function() {
            Route::any('/setting/courier/edit/{id}', ['uses' => 'Admin\SettingController@courier_edit']);
        //});

        Route::group(['middleware' => ['permission:settingcourierdelete']], function() {
            Route::get('/setting/courier/delete/{id}', ['uses' => 'Admin\SettingController@courier_delete']);
        });
    });


    // SETTING - COUNTRIES
    Route::group(['middleware' => ['permission:settingcountrieslist']], function() {
        Route::get('/setting/country', ['uses' => 'Admin\SettingController@country']);
        Route::get('/setting/state', ['uses' => 'Admin\SettingController@state']);
        Route::get('/setting/state/{co_id}', ['uses' => 'Admin\SettingController@state_by_country']);

        Route::group(['middleware' => ['permission:settingcountriescreate']], function() {
            Route::any('/setting/country/add', ['uses' => 'Admin\SettingController@country_add']);
            Route::any('/setting/state/add/{co_id}', ['uses' => 'Admin\SettingController@state_add']);
        });

        Route::group(['middleware' => ['permission:settingcountriesedit']], function() {
            Route::any('/setting/country/edit/{id}', ['uses' => 'Admin\SettingController@country_edit']);
            Route::any('/setting/state/edit/{id}', ['uses' => 'Admin\SettingController@state_edit']);
        });

        Route::group(['middleware' => ['permission:settingcountriesdelete']], function() {
            Route::get('/setting/country/delete/{id}', ['uses' => 'Admin\SettingController@country_delete']);
            Route::get('/setting/state/delete/{id}/{co_id}', ['uses' => 'Admin\SettingController@state_delete']);
        });

        Route::get('/setting/generate/country_state', 'Admin\SettingController@buildCountryStateJs');
    });

    // SETTING - CATEGORIES ONLINE
    Route::group(['middleware' => ['permission:settingcategoriesonlinelist']], function() {
        Route::get('/setting/category/listing', ['uses' => 'Admin\SettingController@category_by_parent']);
        Route::get('/setting/category/listing/{parent_id}', ['uses' => 'Admin\SettingController@category_by_parent']);
        Route::any('/setting/category/edit/{id}', ['uses' => 'Admin\SettingController@edit_category']);
        Route::get('/setting/category/check', ['uses' => 'Admin\SettingController@check_category']);

        Route::group(['middleware' => ['permission:settingcategoriesonlinecreate']], function() {
            Route::get('/setting/category/add', ['uses' => 'Admin\SettingController@add_category']);
            Route::any('/setting/category/add/{parent_id}', ['uses' => 'Admin\SettingController@add_category']);
        });

        Route::group(['middleware' => ['permission:settingcategoriesonlineedit']], function() {
            Route::get('/setting/category/filter/{id}', ['uses' => 'Admin\SettingController@category_filter']);
            Route::get('/setting/category_filter/add', ['uses' => 'AjaxController@add_category_filter']);
            Route::get('/setting/category_filter/remove', ['uses' => 'AjaxController@remove_category_filter']);
        });

        Route::group(['middleware' => ['permission:settingcategoriesonlinedelete']], function() {
            Route::get('/setting/category/delete/{id}', ['uses' => 'Admin\SettingController@delete_category']);
        });
    });

    // SETTING - CATEGORIES OFFLINE
    Route::group(['middleware' => ['permission:settingcategoriesofflinelist']], function() {
        Route::get('/setting/offline_category/listing', ['uses' => 'Admin\SettingController@offline_category_by_parent']);
        Route::get('/setting/offline_category/listing/{parent_id}', ['uses' => 'Admin\SettingController@offline_category_by_parent']);
        Route::any('/setting/offline_category/edit/{id}', ['uses' => 'Admin\SettingController@edit_offline_category']);
        Route::get('/setting/offline_category/check', ['uses' => 'Admin\SettingController@check_offline_category']);

        Route::group(['middleware' => ['permission:settingcategoriesofflinecreate']], function() {
            Route::get('/setting/offline_category/add', ['uses' => 'Admin\SettingController@add_offline_category']);
            Route::any('/setting/offline_category/add/{parent_id}', ['uses' => 'Admin\SettingController@add_offline_category']);
        });

        Route::group(['middleware' => ['permission:settingcategoriesofflinedelete']], function() {
            Route::get('/setting/offline_category/delete/{id}', ['uses' => 'Admin\SettingController@delete_offline_category']);
        });
    });

    // SETTING - CMS
    Route::group(['middleware' => ['permission:settingcmslist']], function() {
        Route::get('/setting/cms', ['uses' => 'Admin\SettingController@cms_type']);
        Route::get('/setting/cms/manage/{type_id}', ['uses' => 'Admin\SettingController@cms']);
        Route::any('/setting/cms/edit/{id}', ['uses' => 'Admin\SettingController@cms_edit']);
        Route::get('/setting/cms/footer/sorting', ['uses' => 'Admin\SettingController@sorting_cms_footer']);
        Route::get('/setting/cms/footer/update_footer_sorting', ['uses' => 'Admin\SettingController@update_footer_sorting']);

        Route::group(['middleware' => ['permission:settingcmscreate']], function() {
            Route::any('/setting/cms/add/{type_id}', ['uses' => 'Admin\SettingController@cms_add']);
        });

        Route::group(['middleware' => ['permission:settingcmsdelete']], function() {
            Route::get('/setting/cms/delete/{id}', ['uses' => 'Admin\SettingController@cms_delete']);
        });
    });

    // SETTING - BANNER
    Route::group(['middleware' => ['permission:settingbannerlist']], function() {
        Route::get('/setting/banner', ['uses' => 'Admin\SettingController@bannertype']);
        Route::any('/setting/banner/type/edit/{id}', ['uses' => 'Admin\SettingController@bannertype_edit']);
        Route::get('/setting/banner/manage/{type_id}', ['uses' => 'Admin\SettingController@banner']);
        Route::any('/setting/banner/edit/{id}', ['uses' => 'Admin\SettingController@banner_edit']);
        Route::get('/setting/banner/save_order', ['uses' => 'Admin\SettingController@banner_saveorder']);

        Route::group(['middleware' => ['permission:settingbannercreate']], function() {
            Route::any('/setting/banner/type/add', ['uses' => 'Admin\SettingController@bannertype_add']);
            Route::any('/setting/banner/add/{id}', ['uses' => 'Admin\SettingController@banner_add']);
        });

        Route::group(['middleware' => ['permission:settingbannerdelete']], function() {
            Route::get('/setting/banner/delete/{id}', ['uses' => 'Admin\SettingController@banner_delete']);
        });
    });

    // SETTING - FILTER
    Route::group(['middleware' => ['permission:settingfilterlist']], function() {
        Route::get('/setting/filter', ['uses' => 'Admin\SettingController@filter']);
        Route::any('/setting/filter/edit/{id}', ['uses' => 'Admin\SettingController@filter_edit']);
        Route::get('/setting/filter/item/{id}', ['uses' => 'Admin\SettingController@filter_item']);
        Route::any('/setting/filter/item/edit/{id}', ['uses' => 'Admin\SettingController@filter_item_edit']);

        Route::group(['middleware' => ['permission:settingfiltercreate']], function() {
            Route::any('/setting/filter/add', ['uses' => 'Admin\SettingController@filter_add']);
            Route::any('/setting/filter/item/add/{id}', ['uses' => 'Admin\SettingController@filter_item_add']);
        });
    });

    // SETTING - COMMISSION
    Route::group(['middleware' => ['permission:settingcommissionlist']], function() {
        Route::get('/setting/commission', ['uses' => 'Admin\SettingController@commission']);

        Route::group(['middleware' => ['permission:settingcommissionedit']], function() {
            Route::post('/setting/commission', ['uses' => 'Admin\SettingController@commission_submit']);
        });
    });

    // PRODUCT - MANAGE & Add & SOLD OUT
    Route::group(['middleware' => ['permission:productmanagelist']], function() {
        Route::get('/product/manage', ['uses' => 'Admin\ProductController@manage_product']);
        Route::get('/product/detail/{id}', ['uses' => 'Admin\ProductController@detail']);
        Route::get('/product/sold', ['uses' => 'Admin\ProductController@sold_product']);
        Route::get('/product/shipping', ['uses' => 'Admin\ProductController@manage_product_shipping_details']);
        Route::get('/product/view/{mer_id}/{pro_id}', 'Admin\ProductController@view_product');
        Route::get('/product/edit/{mer_id}/{pro_id}', 'Admin\ProductController@edit_product');
        Route::get('/product/pricing/{mer_id}/{pro_id}', 'Admin\ProductController@product_pricing');
        Route::get('/product/filter/{mer_id}/{pro_id}', 'Admin\ProductController@manage_filter');
        Route::get('/product/attribute/{mer_id}/{pro_id}', 'Admin\ProductController@manage_attribute');
        Route::get('/product/quantity/{mer_id}/{pro_id}', 'Admin\ProductController@view_product_quantity_log');
        Route::get('/product/image/view/{mer_id}/{pro_id}', 'Admin\ProductImageController@view_product_image');
        Route::get('/product/image_script', 'Admin\ProductImageController@image_script');
        Route::get('/product/description/{mer_id}/{pro_id}', 'Admin\ProductController@product_description');
        Route::post('/product/description', 'Admin\ProductController@product_description_submit');

        /* CODE */
        Route::get('/product/code/listing/{mer_id}/{pro_id}', 'Admin\ProductController@ecard_listing');
        Route::post('/product/code/upload/{mer_id}/{pro_id}', 'Admin\ProductController@ecard_upload');
        Route::get('/product/code/redeem/{id}/{pro_id}/{mer_id}', 'Admin\ProductController@ecard_redeem');
        Route::get('/product/code/delete/{id}/{pro_id}/{mer_id}', 'Admin\ProductController@ecard_delete');

        Route::group(['middleware' => ['permission:productmanagecreate']], function() {
            Route::get('/product/add', 'Admin\ProductController@add_product');
            Route::post('/product/add', 'Admin\ProductController@add_product_submit');
            Route::post('/product/image/add', 'Admin\ProductImageController@upload_product_image');
        });

        Route::group(['middleware' => ['permission:productmanageedit']], function() {
            Route::post('/product/edit/{mer_id}/{pro_id}', 'Admin\ProductController@edit_product_submit');
            Route::post('/product/pricing/add/{mer_id}/{pro_id}', 'Admin\ProductController@product_pricing_submit');
            Route::get('/product/toggle_pricing_status/{id}/{mer_id}', 'Admin\ProductController@update_pricing_status');
            Route::get('/product/delete_product_pricing/{id}', 'Admin\ProductController@delete_product_pricing');
            Route::post('/product/pricing_status/batch_update/{pro_id}', 'Admin\ProductController@update_pricing_status_batch');
            Route::post('/product/filter/{mer_id}/{pro_id}', 'Admin\ProductController@update_filter');
            Route::post('/product/attribute/{mer_id}/{pro_id}/add', 'Admin\ProductController@add_attribute_submit');
            Route::post('/product/attribute/{mer_id}/{pro_id}', 'Admin\ProductController@update_attribute');
            Route::get('/product/attribute/{mer_id}/{pro_id}/delete/{attribute_id}/{option}', 'Admin\ProductController@delete_product_attribute');
            Route::get('/product/image/set_main_image/{id}/{mer_id}', 'Admin\ProductImageController@set_main_image');
            Route::get('/product/image/toggle_image_status/{id}', 'Admin\ProductImageController@toggle_image_status');
            Route::get('/product/image/delete/{id}/{mer_id}', 'Admin\ProductImageController@delete_product_image');
            Route::get('/product/image/save_order', 'Admin\ProductImageController@product_image_saveorder');
            Route::post('/product/image/add', 'Admin\ProductImageController@upload_product_image');
        });
    });

    // CUSTOMER - MANAGE & CREATE
    Route::group(['middleware' => ['permission:customermanagelist']], function() {
        Route::get('/customer/manage', ['uses' => 'Admin\CustomerController@manage']);
        Route::get('/customer/view/{cus_id}', 'Admin\CustomerController@view');
        Route::any('/customer/delete/{id}', ['uses' => 'Admin\CustomerController@delete']);
        Route::get('/customer/resend_activation/{id}', 'Admin\CustomerController@resend_verification_email');

        Route::group(['middleware' => ['permission:customermanagecreate']], function() {
            Route::get('/customer/add', ['uses' => 'Admin\CustomerController@add']);
            Route::post('/customer/add', ['uses' => 'Admin\CustomerController@add_submit']);
        });
        Route::group(['middleware' => ['permission:customermanageedit']], function() {
            Route::get('/customer/edit/{id}', ['uses' => 'Admin\CustomerController@edit']);
            Route::post('/customer/edit', ['uses' => 'Admin\CustomerController@edit_submit']);
        });
        Route::group(['middleware' => ['permission:customermanagesetstatus']], function() {
            // SET CUSTOMER STATUS - LOCK / UNLOCK / ACIVE / BLOCK
            Route::get('/customer/block/{cus_id}/{type}', 'Admin\CustomerController@change_customer_status');
        });

        Route::group(['middleware' => ['permission:customermanagemicreditreport']], function() {
            Route::get('/customer/credit/{cus_id}', 'Admin\CustomerController@credit_log');
            Route::group(['middleware' => ['permission:customermanagemicreditreportexport']], function() {
                Route::get('/export/credit_customer', 'Admin\ExportController@credit_customer');
            });
        });

        Route::group(['middleware' => ['permission:customermanagehardresetpassword']], function() {
            Route::post('/customer/reset_password', 'Admin\CustomerController@reset_password');
        });
        Route::get('/customer/soft_reset_password/{cus_id}', 'Admin\CustomerController@soft_reset_password');

        Route::group(['middleware' => ['permission:customermanagehardresetsecurecode']], function() {
            Route::get('/customer/soft_reset_secure_code/{cus_id}', 'Admin\CustomerController@soft_reset_secure_code');
        });
        Route::post('/customer/reset_secure_code', 'Admin\CustomerController@reset_secure_code');

        Route::group(['middleware' => ['permission:customermanagemmicreditedit']], function() {
            Route::post('/customer/manage_credit/{cus_id}', 'Admin\CustomerController@manage_credit');
        });

        // Route::get('/customer/gamepoint/{cus_id}', 'Admin\CustomerController@gamepoint_log');
        // Route::get('/export/gamepoint_customer', 'Admin\ExportController@gamepoint_customer');
    });

    // CUSTOMER - INQUIRIES
    Route::group(['middleware' => ['permission:customerinquirieslist']], function() {
        Route::get('/customer/inquiries', ['uses' => 'Admin\CustomerController@inquiries']);
        Route::group(['middleware' => ['permission:customerinquiriesdelete']], function() {
            Route::any('/customer/inquiries/delete/{id}', ['uses' => 'Admin\CustomerController@deleteinquiries']);
        });
    });

    // TRANSACTION - ONLINE ORDERS
    Route::group(['middleware' => ['permission:transactiononlineorderslist']], function() {
        Route::get('transaction/product/{operation}', 'Admin\TransactionController@product_orders');
        Route::get('/transaction/online/complete/{order_id}','Admin\TransactionController@completing_merchant_order');
        Route::post('/transaction/online/complete/{order_id}','Admin\TransactionController@completing_merchant_order');

        Route::group(['middleware' => ['permission:transactiononlineorderslistexport']], function() {
            Route::get('/export/product_orders/{operation}', 'Admin\ExportController@product_orders');
        });

        // Route::group(['middleware' => ['permission:transactiononlineorderrefund']], function() {
            Route::get('/transaction/refund_online_order/{order_id}/{operation}', 'Admin\TransactionController@refund_online_order');
        // });

        // Route::group(['middleware' => ['permission:permission:transactioncancelcoupons']], function() {
            Route::get('/transaction/online/cancel/{type}/{order_id}/{serial_number}','Admin\TransactionController@cancel_code');
        // });

        Route::post('/transaction/product/batch/update/{operation}','Admin\TransactionController@update_batch_transaction');
    });

    Route::group(['middleware' => ['permission:transactionofflineorderslist']], function() {
        Route::get('/transaction/offline', 'Admin\TransactionController@order_offline');

        Route::group(['middleware' => ['permission:transactionofflineorderslistexport']], function() {
            Route::get('/export/order_offline', 'Admin\ExportController@order_offline');
        });

        // Route::group(['middleware' => ['permission:transactionofflineorderrefund']], function() {
            Route::get('/transaction/refund_offline_order/{order_id}', 'Admin\TransactionController@refund_offline_order');
        // });
    });

    // TRANSACTION - FUND REQUEST
    Route::group(['middleware' => ['permission:transactionfundrequestlist']], function() {
        Route::get('/transaction/fund-request', 'Admin\TransactionController@fund_request');

        Route::group(['middleware' => ['permission:transactionfundrequestlistexport']], function() {
            Route::get('/export/fund_request', 'Admin\ExportController@fund_request');
        });
    });

    // MERCHANT - ONLINE & OFFLINE
    Route::group(['middleware' => ['permission:merchantonlinelist']], function() {
        Route::get('/merchant/view/{mer_id}', 'Admin\MerchantController@view_merchant');
        Route::get('/merchant/manage/', ['uses' => 'Admin\MerchantController@manage_merchant']);
        Route::get('/merchant/manage/{type}', ['uses' => 'Admin\MerchantController@manage_merchant']);

        //export
        Route::group(['middleware' => ['permission:merchantlistingexport']], function() {
            Route::get('/merchant/export/{type}', ['uses' => 'Admin\ExportController@merchant_listing'])->name('admin.export.merchant');
        });

        /* TAX INVOICE */
        Route::any('/merchant/tax/{id}', 'Admin\MerchantController@tax_listing');
        Route::any('/merchant/tax/generate/{id}', 'Admin\MerchantController@tax_create');
        Route::any('/merchant/tax/save/{id}', 'Admin\MerchantController@tax_save');
        Route::any('/merchant/tax/view/{mer_id}/{id}', 'Admin\MerchantController@tax_view');

        Route::group(['middleware' => ['permission:merchantonlinecreate']], function() {
            Route::get('/merchant/add', ['uses' => 'Admin\MerchantController@add_merchant']);
            Route::post('/merchant/add', ['uses' => 'Admin\MerchantController@add_merchant_submit']);
        });
        Route::group(['middleware' => ['permission:merchantonlineedit']], function() {
            Route::get('/merchant/edit/{mer_id}', ['uses'=> 'Admin\MerchantController@edit_merchant']);
            Route::post('/merchant/edit', 'Admin\MerchantController@edit_merchant_submit');

            Route::group(['middleware' => ['permission:merchantonlinelockmerchant']], function() {
                Route::any('/merchant/lock/{mer_id}/{type}', 'Admin\MerchantController@change_merchant_lock');
            });

            Route::group(['middleware' => ['permission:merchantonlinesoftresetpassword']], function() {
                Route::get('/merchant/soft_reset_password/{mer_id}', 'Admin\MerchantController@soft_reset_password');
            });

            Route::group(['middleware' => ['permission:merchantonlinehardresetpassword']], function() {
                Route::post('/merchant/reset_password', 'Admin\MerchantController@reset_password');
            });

            Route::group(['middleware' => ['permission:merchantonlinemanagemicredit']], function() {
                Route::post('/merchant/manage_credit/{mer_id}', ['uses' => 'Admin\MerchantController@manage_credit']);
            });
        });

        Route::group(['middleware' => ['permission:merchantonlinemicreditreport']], function() {
            Route::get('/merchant/credit/{mer_id}', 'Admin\MerchantController@merchant_credit_log');

            Route::group(['middleware' => ['permission:merchantonlinemicreditreportexport']], function() {
                Route::get('/export/credit_merchant', 'Admin\ExportController@credit_merchant');
            });
        });
    });

    //STORE
    Route::group(['middleware' => ['permission:storelist']], function() {
        Route::get('/store/manage/{merid_type}', 'Admin\StoreController@manage');
        Route::get('/store/add/{mer_id}', 'Admin\StoreController@add');
        Route::post('/store/add/{mer_id}', 'Admin\StoreController@add_submit');
        Route::get('/store/edit/{mer_id}/{store_id}', 'Admin\StoreController@edit');
        Route::get('/store/merchant/{merid_type}', 'Admin\StoreController@manage');

        Route::group(['middleware' => ['permission:storeedit']], function() {
            Route::post('/store/edit/{mer_id}/{store_id}', 'Admin\StoreController@edit_submit');
            Route::post('/store/batch_status_update/{operation}', 'Admin\StoreController@batch_status_update');
        });

        Route::group(['middleware' => ['permission:storetogglelisted']], function() {
            Route::get('/store/update_store_listed_status/{store_id}', 'Admin\StoreController@update_store_listed_status');
        });

        /* Merchant stores limit */
        Route::get('/store/limit/{mer_id}/{store_id}', 'Admin\StoreController@limit');
        Route::post('/store/limit-action/create/{mer_id}/{store_id}/{limit_id}', 'Admin\StoreController@limit_create');
        Route::get('/store/limit-action/edit/{mer_id}/{store_id}/{action_id}', 'Admin\StoreController@limit_edit');
        Route::post('/store/limit-action/edit/{mer_id}/{store_id}/{action_id}', 'Admin\StoreController@limit_edit_submit');
        Route::get('/store/limit-action/delete/{mer_id}/{store_id}/{action_id}', 'Admin\StoreController@limit_action_delete');
    });

    //STORE USER
    Route::group(['middleware' => ['permission:storeuserlist']], function() {
        Route::get('/store/user/{mer_id}', 'Admin\StoreUserController@manage');

        Route::group(['middleware' => ['permission:storeusercreate']], function() {
            Route::get('/store/user/add/{mer_id}', 'Admin\StoreUserController@add');
            Route::post('/store/user/add/{mer_id}', 'Admin\StoreUserController@add_submit');
        });

        Route::group(['middleware' => ['permission:storeuseredit']], function() {
            Route::get('/store/user/edit/{mer_id}/{user_id}', 'Admin\StoreUserController@edit');
            Route::post('/store/user/edit', 'Admin\StoreUserController@edit_submit');
            Route::get('/store/user/permission/{mer_id}/{user_id}', 'Admin\StoreUserController@store_permission');

            Route::group(['middleware' => ['permission:storeuserresetpassword']], function() {
                Route::post('/store/user/reset_password/{mer_id}/{user_id}', 'Admin\StoreUserController@reset_password');
            });
        });

        Route::group(['middleware' => ['permission:storeusersetuserstatus']], function() {
            Route::get('/store/user/status/{user_id}/{status}', 'Admin\StoreUserController@toggle_user_status');
        });
    });

    /* Report */
    Route::group(['middleware' => ['permission:viewsalesreport']], function() {
        Route::any('/report/sales', 'Admin\ReportController@sales');

        Route::group(['middleware' => ['permission:exportsalesreport']], function() {
            Route::get('/export/sale_by_transaction_date', 'Admin\ExportController@sale_by_transaction_date_report');
        });
    });

    Route::group(['middleware' => ['permission:viewmicreditsummary']], function() {
        Route::any('/report/credit-summary', 'Admin\ReportController@credit_summary');

        Route::group(['middleware' => ['permission:exportmicreditsummary']], function() {
            Route::get('/export/credit_summary', 'Admin\ExportController@credit_summary_report');
        });
    });

    Route::group(['middleware' => ['permission:viewmicreditlog']], function() {
        Route::any('/report/credit-log', 'Admin\ReportController@credit_log');

        Route::group(['middleware' => ['permission:exportmicreditlog']], function() {
            Route::get('/export/credit_log', 'Admin\ExportController@credit_log_report');
        });
    });

    Route::group(['middleware' => ['permission:credittransfer']], function() {
        Route::any('/report/credit-transfer', 'Admin\ReportController@credit_transfer');

        Route::group(['middleware' => ['permission:exportcredittransfer']], function() {
            Route::get('/export/credit_transfer', 'Admin\ExportController@credit_transfer_report');
        });
    });

    /* lucky Draws */
    Route::get('/lucky_draw/manage', 'Admin\LuckyDrawController@manage');
    Route::get('/lucky_draw/print', 'Admin\LuckyDrawController@print_all');
    Route::get('/lucky_draw/dummy', 'Admin\LuckyDrawController@print_dummy');
    Route::get('/lucky_draw/redeem/{id}', 'Admin\LuckyDrawController@redeemed');

    // customer limit
    Route::group(['prefix' => 'customer'], function()
    {
        Route::get('/limit/{customer_id}', 'Admin\CustomerController@limit');
        Route::post('/limit/action/create/{customer_id}/{limit_id}', 'Admin\CustomerController@limit_create');
        Route::get('/limit/action/edit/{customer_id}/{action_id}', 'Admin\CustomerController@limit_edit');
        Route::post('/limit/action/edit/{customer_id}/{action_id}', 'Admin\CustomerController@limit_edit_submit');
        Route::get('/limit/action/delete/{customer_id}/{action_id}', 'Admin\CustomerController@limit_action_delete');
    });
    /* KIV */
    // Route::get('/deals/add',['uses' => 'Admin\DealController@add_deal']);
    // Route::get('/transaction/deal/cod', 'Admin\TransactionController@deal_cod');
    // Route::get('/transaction/deal/orders', 'Admin\TransactionController@deal_orders');

    // Migration
    // Route::get('/migration/product_image/{limit}', 'Admin\MigrationController@product_image');
    // Route::get('/migration/categories', 'Admin\MigrationController@categories');
    // Route::get('/migration/product_categories/{limit}', 'Admin\MigrationController@product_categories');
    // Route::get('/migration/product_pricing/{limit}', 'Admin\MigrationController@migrate_pricing');
    // Route::get('/migration/product_quantity/{limit}', 'Admin\MigrationController@product_quantity');
    // Route::get('/migration/merchant_charges/{limit}', 'Admin\MigrationController@merchant_charges');
});

/*** OLD ***/
// Route::get('update_order_cod', 'Admin\TransactionController@update_order_cod');

/* Attribute Color Setting */
// Route::get('/setting/color', ['uses' => 'Admin\SettingController@color']);
// Route::any('/setting/color/add', ['uses' => 'Admin\SettingController@color_add']);
// Route::any('/setting/color/edit/{id}', ['uses' => 'Admin\SettingController@color_edit']);
// Route::get('/setting/color/delete/{id}', ['uses' => 'Admin\SettingController@color_delete']);

/* Attribute Size Setting */
// Route::get('/setting/size', ['uses' => 'Admin\SettingController@size']);
// Route::any('/setting/size/add', ['uses' => 'Admin\SettingController@size_add']);
// Route::any('/setting/size/edit/{id}', ['uses' => 'Admin\SettingController@size_edit']);
// Route::get('/setting/size/delete/{id}', ['uses' => 'Admin\SettingController@size_delete']);

/* Auction */
// Route::get('/auction/manage', ['uses' => 'Admin\AuctionController@manage']);
// Route::get('/auction/add', 'Admin\AuctionController@add');
// Route::post('/auction/add', 'Admin\AuctionController@add_submit');
// Route::get('/auction/archive', 'Admin\AuctionController@archive');
// Route::get('/auction/view/{auc_id}', 'Admin\AuctionController@view');
// Route::get('/auction/edit/{auc_id}', 'Admin\AuctionController@edit');
// Route::post('/auction/edit', 'Admin\AuctionController@edit_submit');
// Route::get('/auction/winner', 'Admin\AuctionController@auction_winner_list');
// Route::get('/auction/cod', 'Admin\AuctionController@auction_cod');
// Route::get('/transaction/auction/view', 'Admin\TransactionController@auction_view_bidders');
// Route::get('/transaction/auction/manage', 'Admin\TransactionController@auction_manage');
// Route::get('/transaction/auction/bidder/{id}', 'Admin\TransactionController@list_auction_bidder');
// Route::get('update_auction_winner/{id}/{pageid}', 'Admin\TransactionController@update_auction_winner');
// Route::get('update_auction_status/{auc_id}/{type}', 'Admin\AuctionController@change_auction_status');