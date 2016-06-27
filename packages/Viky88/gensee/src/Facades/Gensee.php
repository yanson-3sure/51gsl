<?php

namespace Gensee\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mews\Captcha
 */
class Gensee extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor() { return 'gensee'; }

    static public function __callStatic($name, $args)
    {
        $app = static::getFacadeRoot();

        if (method_exists($app, $name)) {
            return call_user_func_array([$app, $name], $args);
        }

        return $app->$name;
    }

}