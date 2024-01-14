<?php

namespace Vanadi\Framework\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Vanadi\Framework\Seeders\Framework\CountriesSeeder;
use Vanadi\Framework\Seeders\Framework\CurrenciesSeeder;
use Vanadi\Framework\Seeders\Framework\TeamTableSeeder;
use function Vanadi\Framework\system_user;

class FrameworkSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CountriesSeeder::class);
        $this->call(CurrenciesSeeder::class);
    }
}
