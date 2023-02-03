<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use Hash;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('valid_hash', function ($attribute, $value, $parameters, $validator) {
            return Hash::check($value, current($parameters));
        });

        Validator::extend('password', function ($attribute, $value, $parameters, $validator) {
            // Contain at least one uppercase/lowercase letters and one number
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', (string)$value);
        });

        Validator::extend('username', function ($attribute, $value, $parameters, $validator) {
            // Contain at least one letters letters and number
            return preg_match('/^[a-zA-Z0-9]*$/', (string)$value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}