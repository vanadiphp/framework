<?php

namespace Vanadi\Framework\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Vanadi\Framework\Custom\Filament\Columns\ActiveStatusColumn;
use Vanadi\Framework\FrameworkPlugin;
use Vanadi\Framework\Models\Team;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationGroup(): ?string
    {
        return FrameworkPlugin::getNavigationGroupLabel();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Fieldset::make('Accounting Setup')->schema([
                    Forms\Components\TextInput::make('chart_code')->required(),

                    Forms\Components\TextInput::make('revenue_account_number')->required(),
                    Forms\Components\TextInput::make('revenue_object_code')->required(),

                    Forms\Components\TextInput::make('payable_account_number'),
                    Forms\Components\TextInput::make('payable_object_code'),

                    Forms\Components\TextInput::make('deferred_income_account_number'),
                    Forms\Components\TextInput::make('deferred_income_object_code'),

                ]),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                ActiveStatusColumn::make(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Vanadi\Framework\Filament\Resources\TeamResource\Pages\ManageTeams::route('/'),
        ];
    }
}
