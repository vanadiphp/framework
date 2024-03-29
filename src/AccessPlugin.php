<?php

namespace Vanadi\Framework;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Vanadi\Framework\Filament\Resources\RoleResource;
use Vanadi\Framework\Filament\Resources\UserResource;
use Vanadi\Framework\Http\Middleware\RedirectIfInertiaMiddleware;

class AccessPlugin implements Plugin
{
    private bool $useLdap = false;

    private bool $registerResources = true;

    public function getId(): string
    {
        return 'vanadi-access';
    }

    public static function getNavGroupLabel(): string
    {
        return 'Access Control';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->middleware([
                RedirectIfInertiaMiddleware::class,
            ])
            ->discoverPages(in: __DIR__ . '/../Filament/Pages', for: 'Vanadi\\Framework\\Access\\Filament\\Pages')
            ->navigationGroups([
                NavigationGroup::make(static::getNavGroupLabel())->collapsible()->collapsed(),
            ])->plugin(FilamentShieldPlugin::make()
            ->resourceCheckboxListColumns(['sm' => 2, 'lg' => 3, 'xl' => 6]));
        if ($this->isRegisterResources()) {
            $panel->resources([
                UserResource::class,
                RoleResource::class,
            ]);
        }
        if ($this->isUseLdap()) {
            $panel->ldap();
        } else {
            $panel->login();
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function useLdap(bool $useLdap = true): AccessPlugin
    {
        $this->useLdap = $useLdap;

        return $this;
    }

    public function isUseLdap(): bool
    {
        return $this->useLdap;
    }

    public function registerResources(bool $registerResources = true): AccessPlugin
    {
        $this->registerResources = $registerResources;

        return $this;
    }

    public function isRegisterResources(): bool
    {
        return $this->registerResources;
    }
}
