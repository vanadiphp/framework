<?php

namespace Vanadi\Framework\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Vanadi\Framework\Custom\Filament\Columns\ActiveStatusColumn;
use Vanadi\Framework\Filament\Resources\DepartmentResource\Pages;
use Vanadi\Framework\FrameworkPlugin;
use Vanadi\Framework\Models\Department;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

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
                Forms\Components\TextInput::make('short_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('chart_code')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('object_code')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('account_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('revenue_object_code')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('revenue_account_number')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('sync_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('parent_sync_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('short_name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('chart_code')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('object_code')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable()->sortable(),
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
            'index' => Pages\ManageDepartments::route('/'),
        ];
    }
}
