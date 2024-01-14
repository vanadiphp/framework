<?php

namespace Vanadi\Framework\Filament\Resources;

use Vanadi\Framework\AccessPlugin;

class RoleResource extends \BezhanSalleh\FilamentShield\Resources\RoleResource
{
    public static function getNavigationGroup(): ?string
    {
        return AccessPlugin::getNavGroupLabel();
    }
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
