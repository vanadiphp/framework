<?php

namespace Vanadi\Framework\Seeders\Framework;

use Illuminate\Database\Seeder;
use Vanadi\Framework\Models\Team;

class TeamTableSeeder extends Seeder
{
    public function run(): void
    {
        $team = Team::firstOrCreate(['code' => 'DEFAULT'], [
            'name' => 'DEFAULT TEAM',
        ]);
        $team->submit();
    }
}
