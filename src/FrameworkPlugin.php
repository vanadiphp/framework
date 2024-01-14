<?php

namespace Vanadi\Framework;

use Vanadi\Framework\Filament\Resources\DepartmentResource;
use Vanadi\Framework\Models\Team;
use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Vanadi\Framework\Filament\Pages\ManageWebserviceSettings;
use Vanadi\Framework\Filament\Resources\CountryResource;
use Vanadi\Framework\Filament\Resources\CurrencyResource;
use Vanadi\Framework\Filament\Resources\TeamResource;
use Vanadi\Framework\Helpers\Framework;
use Vanadi\Framework\Http\Middleware\RedirectIfInertiaMiddleware;

class FrameworkPlugin implements Plugin
{
    private bool $registerResources = true;
    private bool $registerPages = true;
    public function getId(): string
    {
        return 'vanadi-framework';
    }

    public static function getNavigationGroupLabel(): string
    {
        return 'System Setup';
    }

    public function register(Panel $panel): void
    {
        app()->singleton($this->getId(), Framework::class);
        $panel->navigationGroups([
            NavigationGroup::make(static::getNavigationGroupLabel()),
            NavigationGroup::make('Settings')->collapsible()->collapsed(),
        ])
            ->middleware([
                RedirectIfInertiaMiddleware::class,
            ])
            ->discoverPages(in: __DIR__ . '/../Filament/Pages', for: 'Vanadi\\Framework\\Filament\\Pages')
//            ->tenant(Team::class)
//            ->tenantRegistration(RegisterTeam::class)
        ;

        if ($this->shouldRegisterPages()) {
            $panel->pages([
                ManageWebserviceSettings::class,
            ]);
        }
        if ($this->shouldRegisterResources()) {
            $panel->resources([
                TeamResource::class,
                CurrencyResource::class,
                CountryResource::class
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        FilamentView::registerRenderHook(
            'panels::user-menu.before',
            fn() => view('vanadi-framework::team-switcher')
        );
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

    public function registerResources(bool $registerResources = true): FrameworkPlugin
    {
        $this->registerResources = $registerResources;
        return $this;
    }

    public function shouldRegisterResources(): bool
    {
        return $this->registerResources;
    }

    public function registerPages(bool $registerPages = true): FrameworkPlugin
    {
        $this->registerPages = $registerPages;
        return $this;
    }

    public function shouldRegisterPages(): bool
    {
        return $this->registerPages;
    }
}
