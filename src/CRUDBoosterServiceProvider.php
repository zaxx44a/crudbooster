<?php namespace Crocodic\CrudBooster;

use Crocodic\CrudBooster\Core\RuntimeCache;
use Crocodic\CrudBooster\Modules\LogModule\CbRoleModuleServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class CRUDBoosterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * Call when after all packages has been loaded
     *
     * @return void
     */

    public function boot()
    {        
                                
        $this->loadViewsFrom(__DIR__ . '/views', 'crudbooster');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadTranslationsFrom(__DIR__ . '/Lang','crudbooster');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        if($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/Configs/crudbooster.php' => config_path('crudbooster.php')],'cb_config');
            $this->publishes([__DIR__ . '/Assets' =>public_path('vendor/crudbooster')],'cb_asset');
        }

        $this->customValidation();
    }

    /**
     * Register the application services.
     * Call when this package is first time loaded
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Configs/crudbooster.php','crudbooster');

        $this->registerSingleton();

        $this->commands('CbInstall');

        App::register(CbRoleModuleServiceProvider::class);
    }
   
    private function registerSingleton()
    {
        $this->app->singleton('crudbooster', function ()
        {
            return true;
        });

        $this->app->singleton("RuntimeCache", function () {
            return new RuntimeCache;
        });

        $this->app->singleton('CbInstall',function() {
            return new \Crocodic\CrudBooster\Commands\CbInstall;
        });
    }

    private function customValidation() {
        Validator::extend('alpha_spaces', function ($attribute, $value) {
            // This will only accept alpha and spaces.
            // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            return preg_match('/^[\pL\s]+$/u', $value);
        },'The :attribute should be letters and spaces only');

        Validator::extend('alpha_num_spaces', function ($attribute, $value) {
            // This will only accept alphanumeric and spaces.
            return preg_match('/^[a-zA-Z0-9\s]+$/', $value);
        },'The :attribute should be alphanumeric characters and spaces only');
    }
}
