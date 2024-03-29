<?php

namespace Vanadi\Framework\Custom\Filament\Columns;

use Filament\Tables\Columns\TextColumn;

class ActiveStatusColumn
{
    public static function make()
    {
        return TextColumn::make('is_active')
            ->badge()
            ->label('Status')
            ->formatStateUsing(fn ($state) => match (boolval($state)) {
                true => 'Active',
                false => 'Inactive',
            })
            ->colors([
                'success' => true,
                'danger' => false,
            ]);
    }
}
