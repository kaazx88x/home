<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composers([
            // 'App\Http\ViewComposers\CategoryComposer' => ['layouts.nav', 'front.product.list', 'layouts.footer'],
            'App\Http\ViewComposers\CategoryComposer' => [
                'layouts.web.master',
                'front.home.index'
            ],
            'App\Http\ViewComposers\CartComposer' => [
                'front.cart.cart',
                'front.cart.checkout',
                'front.product.detail',
                'layouts.web.header.main'
            ],
            'App\Http\ViewComposers\CountryComposer' => [
                'layouts.web.master',
                'layouts.web.header.main',
                'merchant.layouts.master',
                'admin.partial.nav',
            ],
            'App\Http\ViewComposers\CustomerComposer' => [
                'layouts.web.header.main',
                'front.partial.nav_profile',
                'front.cart.checkout',
                'front.cart.cart',
            ],
            'App\Http\ViewComposers\MerchantComposer' => [
                'merchant.partial.nav_merchant',
                'merchant.layouts.master',
                'merchant.product.*',
                'merchant.transaction.*',
                'merchant.dashboard'
            ],
            'App\Http\ViewComposers\MobileAuthComposer' => [
                'layouts.master'
            ],
            'App\Http\ViewComposers\AdminComposer' => [
                'admin.partial.nav',
                'admin.merchant.edit',
                'admin.setting.country',
                'admin.setting.country_edit',
                'admin.customer.edit',
                'admin.merchant.manage'
            ],
            'App\Http\ViewComposers\BannerComposer' => [
                'front.home.index'
            ],
        ]);

        // view()->composer(
        //     ['layouts.nav', 'front.product.list'],
        //     'App\Http\ViewComposers\CategoryComposer'
        // );
        //
        // view()->composer(
        //     ['layouts.nav', 'front.cart.cart', 'front.cart.checkout'],
        //     'App\Http\ViewComposers\CartComposer'
        // );
        //
        // view()->composer(
        //     ['layouts.nav'],
        //     'App\Http\ViewComposers\CountryComposer'
        // );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
