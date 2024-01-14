<?php

namespace Vanadi\Framework\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Vanadi\Framework\Filament\Resources\UserResource;

class EditUser extends EditRecord
{

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
