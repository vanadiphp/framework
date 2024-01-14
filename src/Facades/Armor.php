<?php

namespace Vanadi\Framework\Facades;

use Illuminate\Support\Facades\Facade;

class Armor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Vanadi\Framework\Support\Armor::class;
    }
}
