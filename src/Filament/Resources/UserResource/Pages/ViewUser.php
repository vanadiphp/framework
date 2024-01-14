<?php

namespace Vanadi\Framework\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Vanadi\Framework\Filament\Resources\UserResource;

class ViewUser extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
