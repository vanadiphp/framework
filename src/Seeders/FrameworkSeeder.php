<?php

namespace Vanadi\Framework\Seeders;

use Illuminate\Database\Seeder;
use Vanadi\Framework\Seeders\Framework\CountriesSeeder;
use Vanadi\Framework\Seeders\Framework\CurrenciesSeeder;

class FrameworkSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CountriesSeeder::class);
        $this->call(CurrenciesSeeder::class);
    }
}
