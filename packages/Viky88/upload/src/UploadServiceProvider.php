<?php

namespace Viky88\Upload;

use Illuminate\Support\ServiceProvider;

class UploadServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
       // Define the route

        $types = $this->app['config']->get('upload.types');//获取所有types
        foreach($types as $k => $type){
            $routeConfig = [
                'namespace' => 'Viky88\Upload\Http\Controllers',
            ];
            if(isset($type['middleware'])){
                $routeConfig['middleware'] = $type['middleware'];
            }
            $this->app['router']->group($routeConfig, function($router) use($type){
                $router->post($type['route'],$type['action']);
            });
            $this->publishes([
                __DIR__.'/config/upload.php' => config_path('upload.php'),
            ]);
        }
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
