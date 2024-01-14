<?php

namespace Vanadi\Framework\Filament\Resources\CountryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Vanadi\Framework\Filament\Resources\CountryResource;

class ManageCountries extends ManageRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
