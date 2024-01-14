<?php

namespace Vanadi\Framework\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;
use Throwable;
use function Vanadi\Framework\default_team;

class AdminUserSeeder extends Seeder
{
    const ADMIN_EMAIL = 'admin@savannabits.com';

    /**
     * @throws Throwable
     */
    public function run(): void
    {
        $user = User::firstOrCreate([
            'username' => 'SYSADMIN',
        ], [
            'name' => 'System Admin',
            'email' => self::ADMIN_EMAIL,
            'code' => 'SYSADMIN',
            'email_verified_at' => now(),
            'user_type_code' => 'ADMIN',
            'password' => Hash::make('password'),
            'team_id' => default_team()?->getAttribute('id'),
        ]);
        $user->submit();
        // If schema does not have a table called roles, run shield:install --fresh
        if (!\Schema::hasTable('roles')) {
            $res = \Artisan::call('shield:install', ['--fresh' => true, '--no-interaction' => true]);
            abort_unless($res === 0, 500, 'shield:install failed. Check logs for details');
        }
        \Artisan::call('shield:super-admin', ['--user' => $user->id, '--no-interaction' => true]);
    }
}
