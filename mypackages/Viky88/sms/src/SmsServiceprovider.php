<?php namespace Viky88\Sms;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
class SmsServiceprovider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    public function boot()
    {
        $this->setupRoutes($this->app->router);
        // this for config
        $this->publishes([
            __DIR__.'/config/sms.php' => config_path('sms.php'),
        ]);
        $this->app['validator']->extend('sms', function($attribute, $value, $parameters)
        {
            return sms_check($value);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'Viky88\Sms\Http\Controllers'], function($router)
        {
            require __DIR__.'/Http/routes.php';
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/sms.php', 'sms'
        );
        $this->registerSms();

    }
    private function registerSms()
    {
        // Bind sms
        $this->app->bind('sms', function($app)
        {
            return new Sms(
                $app['Illuminate\Config\Repository'],
                $app['Illuminate\Session\Store'],
                $app['Illuminate\Hashing\BcryptHasher'],
                $app['Illuminate\Support\Str']
            );
        });
    }
}