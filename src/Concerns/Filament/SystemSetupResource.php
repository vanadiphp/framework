<?php

namespace Vanadi\Framework\Concerns\Filament;

use Filament\Resources\Resource;
use Vanadi\Framework\FrameworkPlugin;

/**
 * Trait SystemSetupResource
 *
 * @mixin Resource
 */
trait SystemSetupResource
{
    public static function getNavigationGroup(): ?string
    {
        return FrameworkPlugin::getNavigationGroupLabel();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
