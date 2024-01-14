<?php

namespace Vanadi\Framework;

use Filament\Facades\Filament;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vanadi\Framework\Commands\CurrencyExchangeCommand;
use Vanadi\Framework\Commands\FrameworkCommand;
use Vanadi\Framework\Concerns\Model\HasState;
use Vanadi\Framework\Livewire\SwitchTeam;
use Vanadi\Framework\Providers\VanadiServiceProvider;
use Vanadi\Framework\Seeders\AccessDatabaseSeeder;
use Vanadi\Framework\Seeders\FrameworkSeeder;
use Vanadi\Framework\Testing\TestsFramework;

class FrameworkServiceProvider extends PackageServiceProvider
{
    public static string $name = 'vanadi-framework';

    public static string $viewNamespace = 'vanadi-framework';

    public function configurePackage(Package $package): void
    {
        $this->app->register(VanadiServiceProvider::class);
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasRoutes($this->getRoutes())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('vanadiphp/framework')
                    ->endWith(fn (InstallCommand $command) => $this->extendInstallation($command));
            });

        $package->hasConfigFile([
            'vanadi',
            'ldap',
            'permission',
            'vanadi-auth',
            'vanadi-ldap',
            'vanadi-shield',
            'vanadi-permission',
        ]);

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        Livewire::component(static::$viewNamespace . '::switch-team', SwitchTeam::class);
        $this->registerConfigs();
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/vanadi/{$file->getFilename()}"),
                ], 'vanadi-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFramework());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'vanadi/framework';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FrameworkCommand::class,
            CurrencyExchangeCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [
            'web',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'recreate_users_table',
            'create_teams_table',
            'create_departments_table',
            'create_websockets_statistics_entries_table',
            'create_settings_table',
            'create_media_table',
            'add_ldap_columns_to_users_table',
            'create_permission_tables',
            'create_document_cancellations_table',
            'add_webservice_settings',
            'add_framework_settings',
            'add_framework_fields_to_users_table',
            'add_webservice_fields_to_users_table',
            'create_countries_table',
            'create_currencies_table',
            'add_framework_settings',
            'create_team_user_pivot_table',
        ];
    }

    protected function registerConfigs(): void
    {
        $initialPrividers = config('auth.providers');
        $providers = array_merge($initialPrividers, \Config::get('vanadi-auth.providers'));
        \Config::set('auth.providers', $providers);
        // Override spatie laravel permission
        \Config::set('permission', \Config::get('vanadi-permission'));

        // Override Filament Shield config
        \Config::set('filament-shield', \Config::get('vanadi-shield'));

        // Override LDAP settings
        \Config::set('ldap', \Config::get('vanadi-ldap'));
    }

    private function extendInstallation(InstallCommand $command): void
    {
        // Check if there is a current filament panel. If not, instlal Filament with --panels
        if (! Filament::getCurrentPanel()) {
            // Installing Filament with Panels
            $command->info('Installing Filament with Panels');
            $command->call('filament:install', ['--panels' => true]);
            $panel = Filament::getPanels()[0];
            if ($panel) {
                $command->info('Setting Filament Panel to: ' . $panel);
                Filament::setCurrentPanel($panel);
            }
        }
        // If the App\User\Model does not have the FrameworkTrait, add it.
        $userModel = config('auth.providers.users.model');

        // If the model does not have HasState trait recursively, rewrite the
        $command->info('Adding HasState trait to User model');
        // open app/Models/User.php in edit mode and add a trait to it
        $userModelFile = app_path('Models/User.php');
        $trait = 'use \Vanadi\Framework\Concerns\Model\HasState;';
        framework()->append_after_line(
            $userModelFile,
            'use Illuminate\Foundation\Auth\User as Authenticatable;',
            "$trait",
            skip_if_exists: true
        );
        // add line use HasState to the beginning of the class body
        $command->info('Adding `use HasState` trait to User model');
        $line = 'use HasState;';
        framework()->append_after_line(
            $userModelFile,
            'use HasApiTokens, HasFactory, Notifiable;',
            "    $line",
            skip_if_exists: true
        );

        // Add HasRoles trait to User model
        $command->info('Adding HasRoles trait to User model');

        $trait = 'use Spatie\Permission\Traits\HasRoles;';
        framework()->append_after_line(
            $userModelFile,
            'use Illuminate\Foundation\Auth\User as Authenticatable;',
            $trait,
            skip_if_exists: true
        );

        $line = 'use HasRoles;';
        framework()->append_after_line(
            $userModelFile,
            'use HasApiTokens, HasFactory, Notifiable;',
            "    $line",
            skip_if_exists: true
        );

        $trait = 'use Vanadi\Framework\Concerns\Model\HasTeam;';
        framework()->append_after_line(
            $userModelFile,
            'use Illuminate\Foundation\Auth\User as Authenticatable;',
            $trait,
            skip_if_exists: true
        );

        $line = 'use HasTeam;';
        framework()->append_after_line(
            $userModelFile,
            'use HasApiTokens, HasFactory, Notifiable;',
            "    $line",
            skip_if_exists: true
        );

        $trait = 'use Vanadi\Framework\Concerns\Model\HasAuditColumns;';
        framework()->append_after_line(
            $userModelFile,
            'use Illuminate\Foundation\Auth\User as Authenticatable;',
            $trait,
            skip_if_exists: true
        );

        $line = 'use HasAuditColumns;';
        framework()->append_after_line(
            $userModelFile,
            'use HasApiTokens, HasFactory, Notifiable;',
            "    $line",
            skip_if_exists: true
        );

        $trait = 'use Vanadi\Framework\Concerns\Model\HasCode;';
        framework()->append_after_line(
            $userModelFile,
            'use Illuminate\Foundation\Auth\User as Authenticatable;',
            $trait,
            skip_if_exists: true
        );

        $line = 'use HasCode;';
        framework()->append_after_line(
            $userModelFile,
            'use HasApiTokens, HasFactory, Notifiable;',
            "    $line",
            skip_if_exists: true
        );

        if ($command->confirm('Seed Initial Setup Data? (recommended)', true)) {
            $command->call('db:seed', ['--class' => AccessDatabaseSeeder::class]);
            $command->call('db:seed', ['--class' => FrameworkSeeder::class]);
        }

        if ($command->confirm('Would you like to generate All Permissions now?', true)) {
            $command->call('shield:generate', ['--all' => true]);
            $command->comment('Attempting to clear the permission cache: If this fails run it manually as the www-data user.');
            $command->call('permission:cache-reset');
        }
        $command->alert('DONE!');
    }
}
