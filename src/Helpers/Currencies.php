<?php

namespace Vanadi\Framework\Helpers;

use Auth;
use Vanadi\Framework\Models\Currency;
use function Vanadi\Framework\default_team;
use function Vanadi\Framework\framework;
use function Vanadi\Framework\system_user;

class Currencies
{
    public function exchangeRates(string $base = 'USD')
    {
        // Call exchange rates api to get exchange rates
        // https://api.exchangeratesapi.io/latest?base=USD
        try {
            $response = \Http::get(config('vanadi-framework.currency.exchange_rate_endpoint'), [
                'source' => $base,
                'access_key' => config('vanadi-framework.currency.exchange_rates_api_key')
            ])->throw()->collect();
            if (!$response->get('success')) {
                throw new \Exception(json_encode($response->get('error')));
            }
            $res = $response->get('quotes') ?: collect([]);
            \Log::info($res);
            return $res;
        } catch (\Exception $e) {
            \Log::error("Error: ". $e->getMessage());
            return [];
        }

    }

    public function updateExchangeRates($base = 'KES'): array
    {
        $success = 0;
        $failed = 0;
        $rates = collect($this->exchangeRates($base));
        if (!$rates->count()) {
            return ['success' => $success, 'failed' => $failed];
        }
        if (!Auth::check()) {
            // Login System user
            Auth::login(system_user());
        }
        // Update the base currency: KES
        $currency = Currency::query()->where('code', '=', $base)->first();
        if (!$currency) {
            Currency::query()->create([
                'code' => $base,
                'symbol' => $base,
                'name' => $base,
                'team_id' => default_team()?->id,
                'last_forex_update' => now(),
                'exchange_rate' => 1.0,
                'exchange_base_currency' => $base
            ]);
        } else {
            $currency->update([
                'exchange_rate' => 1.0,
                'exchange_base_currency' => $base,
                'last_forex_update' => now(),
            ]);
        }
        $rates->each(function ($rate, $code) use (&$success, &$failed, $base) {
            try {
                // Remove prefix KES from the $code variable
                $code = \Str::replaceFirst($base, '', $code);
                $currency = Currency::query()->where('code', '=', $code)->first();
                if (!$currency) {
                    Currency::query()->create([
                        'code' => $code,
                        'symbol' => $code,
                        'name' => $code,
                        'team_id' => default_team()?->id,
                        'last_forex_update' => now(),
                        'exchange_rate' => $rate,
                        'exchange_base_currency' => $base,
                    ]);
                } else {
                    $currency->update([
                        'exchange_rate' => $rate,
                        'exchange_base_currency' => $base,
                        'last_forex_update' => now(),
                    ]);
                }
                $success++;
            } catch (\Exception $e) {
                $failed++;
                \Log::error($e->getMessage());
            }
        });
        if (Auth::user()?->user_type_code === 'SYSTEM') {
            Auth::logout();
        }
        return ['success' => $success, 'failed' => $failed];
    }
    public function convert(string $to, $from='KES'): float
    {
        $c1 = Currency::query()->where('code','=', $from)->first();
        $c2 = Currency::query()->where('code','=', $to)->first();
        if (!$c1 || !$c2) {
            return 1.0;
        }
        return floatval($c2->exchange_rate / ($c1->exchange_rate ?: 1.0));
    }
}
