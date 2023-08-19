<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected function loadHelpers()
    {
        $appPath = app_path();
        require_once($appPath .'/Helpers/General.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadHelpers();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        if($this->app->environment('production')) {
            $this->app['request']->server->set('HTTPS', true);
        }

    }
}
