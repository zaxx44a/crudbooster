<?php namespace Crocodic\CrudBooster\Modules\LogModule;

use Illuminate\Support\ServiceProvider;

class CbLogModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * Call when after all packages has been loaded
     *
     * @return void
     */

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'CbLogModule');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/log_module_routes.php');
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
