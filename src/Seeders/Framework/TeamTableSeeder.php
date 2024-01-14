<?php

namespace Vanadi\Framework\Seeders\Framework;

use Vanadi\Framework\Models\Team;
use Illuminate\Database\Seeder;

class TeamTableSeeder extends Seeder
{
    public function run(): void
    {
        $team = Team::firstOrCreate(['code' =>'DEFAULT'],[
            'name' => 'DEFAULT TEAM'
        ]);
        $team->submit();
    }
}
