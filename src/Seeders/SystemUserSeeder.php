<?php

namespace Vanadi\Framework\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Vanadi\Framework\Seeders\Framework\TeamTableSeeder;
use Throwable;
use function Vanadi\Framework\default_team;

class SystemUserSeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run(): void
    {
        // Ensure default team is seeded.
        $this->call(TeamTableSeeder::class);
        $user = User::firstOrCreate([
            'code' => 'SYSBOT',
        ], [
            'name' => 'System Bot',
            'email' => 'bot@system',
            'username' => 'SYSBOT',
            'user_number' => 'SYSBOT',
            'email_verified_at' => now(),
            'user_type_code' => 'SYSTEM',
            'team_id' => default_team()?->getAttribute('id'),
        ]);
        $user->saveQuietly();
        $user->submit();
    }
}
