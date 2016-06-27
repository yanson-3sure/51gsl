<?php

namespace Gensee\Foundation\ServiceProviders;


use Gensee\Webcast\WebcastJsonAPI;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WebcastJsonAPIServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['WebcastJsonAPI'] = function ($pimple) {
            return new WebcastJsonAPI($pimple['user']);
        };
    }
}
