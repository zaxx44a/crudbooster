<?php namespace Crocodic\CrudBooster\Modules\LogModule;

use Illuminate\Support\ServiceProvider;

class CbAuthModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * Call when after all packages has been loaded
     *
     * @return void
     */

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'CbAuthModule');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/auth_module_routes.php');
    }

    /**
     * Register the application services.
     * Call when this package is first time loaded
     *
     * @return void
     */
    public function register()
    {

    }
}
