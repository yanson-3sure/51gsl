<?php
if ( ! function_exists('sms_check')) {
    /**
     * @param $value
     * @return bool
     */
    function sms_check($value)
    {
        return app('sms')->check($value);
    }
}
