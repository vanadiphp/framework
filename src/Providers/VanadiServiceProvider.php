<?php

namespace Vanadi\Framework\Providers;

use Filament\Panel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;
use Vanadi\Framework\Filament\Pages\Login;
use Vanadi\Framework\Macros\FrameworkColumns;
use Vanadi\Framework\Macros\UuidNestedSet;

class VanadiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::macro('ldap', function () {
            $this
                ->authGuard('web')
                ->login(Login::class);

            return $this;
        });
        Blueprint::macro('statusColumns', function () {
            FrameworkColumns::statusColumns($this);
        });

        Blueprint::macro('dropStatusColumns', function () {
            FrameworkColumns::dropStatusColumns($this);
        });

        Blueprint::macro('auditColumns', function () {
            FrameworkColumns::auditColumns($this);
        });
        Blueprint::macro('dropAuditColumns', function () {
            FrameworkColumns::dropAuditColumns($this);
        });

        Blueprint::macro('reversalColumns', function () {
            FrameworkColumns::reversalColumns($this);
        });

        Blueprint::macro('dropReversalColumns', function () {
            FrameworkColumns::dropReversalColumns($this);
        });

        Blueprint::macro('uuidNestedSet', function () {
            UuidNestedSet::columns($this);
        });
        Blueprint::macro('dropUuidNestedSet', function () {
            UuidNestedSet::dropColumns($this);
        });

        Blueprint::macro('teamColumn', function () {
            FrameworkColumns::teamColumn($this);
        });

        Blueprint::macro('teamCodeColumn', function (bool $createCodeColumn = false, bool $createTeamColumn = false) {
            FrameworkColumns::teamCodeColumn($this, $createCodeColumn, $createTeamColumn);
        });

        Blueprint::macro('dropTeamColumn', function () {
            FrameworkColumns::dropTeamColumn($this);
        });

        Blueprint::macro('codeColumn', function () {
            $this->string('code', 48);
        });

        Blueprint::macro('dropCodeColumn', function () {
            $this->dropColumn('code');
        });
    }

    public function boot(): void
    {
        \DB::getDoctrineConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }
}
