<?php

namespace Gensee;

use Gensee\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class GenseeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/config/gensee.php' => config_path('gensee.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/gensee.php', 'gensee'
        );

        $this->app->singleton(['Gensee\\Foundation\\Application' => 'gensee'], function($app){
            $app = new Application(config('gensee'));
            return $app;
        });
    }


}
