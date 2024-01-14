<?php

namespace Vanadi\Framework\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vanadi\Framework\Framework
 */
class Saas extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Vanadi\Framework\Framework::class;
    }
}
