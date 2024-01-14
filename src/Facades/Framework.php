<?php

namespace Vanadi\Framework\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vanadi\Framework\Framework
 */
class Framework extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Vanadi\Framework\Framework::class;
    }
}
