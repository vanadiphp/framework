<?php

namespace Vanadi\Framework\Custom\Filament\Columns;

use Filament\Tables\Columns\TextColumn;

class StateColumn
{
    public static function make()
    {
        return TextColumn::make('state')->badge()
            ->formatStateUsing(fn ($state) => match ($state) {
                0 => 'Draft',
                1 => 'Submitted',
                2 => 'Cancelled'
            })
            ->colors([
                'warning' => 0,
                'success' => 1,
                'danger' => 2,
            ]);
    }
}
