<?php

namespace Vanadi\Framework\Helpers;

use Vanadi\Framework\Settings\WebserviceSettings;

class Ws
{
    public static function settings(): WebserviceSettings
    {
        return new WebserviceSettings();
    }

    /**
     * @deprecated use Ws::settings() instead.
     */
    public static function webservice_settings(): WebserviceSettings
    {
        return static::settings();
    }
}
