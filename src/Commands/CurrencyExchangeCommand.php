<?php

namespace Vanadi\Framework\Commands;

use Illuminate\Console\Command;
use function Vanadi\Framework\currencies;

class CurrencyExchangeCommand extends Command
{
    protected $signature = 'currency:exchange {base? : Base currency code (default: KES)}';

    protected $description = 'Update Exchange Rates for all currencies';

    public function handle(): int
    {
        // Get base currency
        try {
            $base = $this->argument('base') ?: 'KES';
            $res = currencies()->updateExchangeRates($base);
            \Log::info("Exchange rates update results: " . json_encode($res));
            $this->comment("Exchange rates update results: ".json_encode($res));
            return self::SUCCESS;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
