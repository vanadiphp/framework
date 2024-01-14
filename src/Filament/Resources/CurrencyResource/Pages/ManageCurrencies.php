<?php

namespace Vanadi\Framework\Filament\Resources\CurrencyResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRecords;
use Vanadi\Framework\Filament\Resources\CurrencyResource;
use Vanadi\Framework\Models\Currency;
use Vanadi\Framework\Vanadi;

class ManageCurrencies extends ManageRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('update-exchange-rates')->label('Update Exchange Rates')
                ->requiresConfirmation()
                ->form([
                    Select::make('base_currency')->label('Set the Base Currency')
                        ->options(Currency::get()
                            ->map(fn($currency) => ['code' => $currency->code,'label' => "$currency->code - $currency->name"])->pluck('label','code')
                        )
                        ->default('KES')
                        ->searchable()
                ])
                ->action(fn(array $data) => \Framework::updateExchangeRates($data['base_currency'] ?: 'KES'))
                ->color('success')->icon('heroicon-o-arrow-path'),
            Actions\CreateAction::make(),
        ];
    }
}
