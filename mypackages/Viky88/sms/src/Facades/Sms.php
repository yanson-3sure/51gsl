<?php

namespace Viky88\Sms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mews\Captcha
 */
class Sms extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor() { return 'sms'; }

}