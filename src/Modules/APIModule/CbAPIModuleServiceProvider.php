<?php namespace Crocodic\CrudBooster\Modules\APIModule;

use Illuminate\Support\ServiceProvider;

class CbAPIModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * Call when after all packages has been loaded
     *
     * @return void
     */

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'CbApiModule');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/api_module_routes.php');

        app("CbModuleRegistry")->registerModule(CbApiModuleRegistry::class);
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
