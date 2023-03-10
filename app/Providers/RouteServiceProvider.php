<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));

        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web_front_profile.php'));

        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web_admin.php'));

        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web_merchant.php'));

        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web_ajax.php'));

        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web_store.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));

        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api_v2.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    // protected function mapWebRoutes(Router $router)
    // {
    //     $router->group([
    //         'namespace' => $this->namespace, 'middleware' => 'web',
    //     ], function ($router) {
    //         require app_path('Http/routes.php');
    //     });
    // }
}
