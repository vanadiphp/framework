<?php

namespace Vanadi\Framework\Seeders\Framework;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PragmaRX\Countries\Package\Countries;
use Vanadi\Framework\Helpers\Access;
use Vanadi\Framework\Models\Country;

use function Vanadi\Framework\default_team;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->comment('Seeding Countries');
        Auth::login(Access::system_user());
        DB::transaction(function () {
            $countries = new Countries();
            $countries->all()->each(function ($country) {
                $code = $country->get('cca3');
                $currency = $country->get('currencies.0');
                if (strlen($currency) > 3) {
                    $currency = null;
                }
                $this->command->comment("Seeding $code");
                $flagPath = $country->get('flag.svg_path');
                $flag = Str::substr($flagPath, Str::position($country->get('flag.svg_path'), '/vendor/'));
                Country::query()->firstOrCreate(['code' => $code, 'team_id' => default_team()->id], [
                    'cca2' => $country->get('cca2'),
                    'cca3' => $country->get('cca3'),
                    'name' => $country->get('name.common'),
                    'capital' => $country->get('capital.0'),
                    'flag_emoji' => $country->get('flag.emoji'),
                    // extract only the path from vendor/ onwards
                    'flag_svg_path' => $flag,
                    'currency_code' => $currency,
                ]);
            });
        });
        Auth::logout();
    }
}
