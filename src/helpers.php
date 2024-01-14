<?php

namespace Vanadi\Framework;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Vanadi\Framework\Helpers\Access;
use Vanadi\Framework\Helpers\Currencies;
use Vanadi\Framework\Helpers\Framework;
use Vanadi\Framework\Models\Currency;
use Vanadi\Framework\Models\Team;
use Vanadi\Framework\Settings\WebserviceSettings;

if (! function_exists('Vanadi\Framework\team')) {
    function team(string $code): ?Team
    {
        return Team::whereCode($code)->first();
    }
}
if (! function_exists('Vanadi\Framework\current_team')) {
    function current_team(): ?Team
    {
        return Auth::check() ? Auth::user()->team : \Vanadi\Framework\team('DEFAULT');
    }
}

if (! function_exists('Vanadi\Framework\framework')) {
    function framework(): Framework
    {
        return app(Framework::class);
    }
}
if (! function_exists('Vanadi\Framework\vanadi')) {
    function vanadi(): \Vanadi\Framework\Framework
    {
        return app(\Vanadi\Framework\Framework::class);
    }
}
if (! function_exists('Vanadi\Framework\currencies')) {
    function currencies(): Currencies
    {
        return app(Currencies::class);
    }
}

if (! function_exists('Vanadi\Framework\default_team')) {
    function default_team(): Team
    {
        return Team::whereCode('DEFAULT')->firstOrFail();
    }
}

if (! function_exists('Vanadi\Framework\currency')) {
    function currency(string $code): ?Currency
    {
        return Currency::whereCode($code)->first();
    }
}

if (! function_exists('Vanadi\Framework\system_user')) {
    function system_user(): \App\Models\User | Authenticatable
    {
        return Access::system_user();
    }
}
if (! function_exists('Vanadi\Framework\webservice_settings')) {
    function webservice_settings(): WebserviceSettings
    {
        return new WebserviceSettings();
    }
}
