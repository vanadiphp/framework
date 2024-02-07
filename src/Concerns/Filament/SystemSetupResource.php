<?php

namespace Vanadi\Framework\Concerns\Filament;

use Filament\Resources\Resource;
use Vanadi\Framework\FrameworkPlugin;
use function Vanadi\Framework\framework;

/**
 * Trait SystemSetupResource
 * @package Vanadi\Framework\Concerns\Filament
 * @mixin Resource
 */
trait SystemSetupResource
{
    /**
     * @return string|null
     */
    public static function getNavigationGroup(): ?string
    {
        return FrameworkPlugin::getNavigationGroupLabel();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
