<?php

namespace Vanadi\Framework\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Vanadi\Framework\AccessPlugin;
use Vanadi\Framework\Custom\Filament\Columns\ActiveStatusColumn;
use Vanadi\Framework\Custom\Filament\Fields\AuditFieldset;
use Vanadi\Framework\Custom\Filament\Layouts\Sidebar;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    public static function getNavigationGroup(): ?string
    {
        return AccessPlugin::getNavGroupLabel();
    }

    public static function form(Form $form): Form
    {
        return Sidebar::make($form)
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('username')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->maxLength(255),
                ])->columns(),
                Forms\Components\Section::make([
                    Forms\Components\Fieldset::make('Roles')->schema([
                        Forms\Components\CheckboxList::make('roles')->hiddenLabel()->relationship('roles', 'name'),
                    ]),
                ]),
                Forms\Components\Section::make([
                    Forms\Components\Fieldset::make('Teams')->schema([
                        Forms\Components\CheckboxList::make('teams')->hiddenLabel()->relationship('teams', 'name'),
                    ]),
                ]),

            ], [
                AuditFieldset::make()->columns(1),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make([
                Infolists\Components\Fieldset::make('Basic Details')->schema([
                    Infolists\Components\TextEntry::make('username'),
                    Infolists\Components\TextEntry::make('user_number'),
                    Infolists\Components\TextEntry::make('email'),
                    Infolists\Components\TextEntry::make('first_name'),
                    Infolists\Components\TextEntry::make('other_names'),
                    Infolists\Components\TextEntry::make('surname'),
                    Infolists\Components\TextEntry::make('department.short_name')->label('Department'),
                    Infolists\Components\TextEntry::make('dob'),
                    Infolists\Components\TextEntry::make('gender'),
                    Infolists\Components\IconEntry::make('is_active')->boolean(),
                    Infolists\Components\IconEntry::make('meals_active')->boolean(),
                    Infolists\Components\TextEntry::make('meals_allowance')->money('kes'),
                ])->columns(['lg' => 3, 'md' => 2]),
            ]),
            Infolists\Components\Section::make([
                Infolists\Components\Fieldset::make('Assigned Roles')->schema([
                    Infolists\Components\RepeatableEntry::make('roles')->schema([
                        Infolists\Components\TextEntry::make('name')->formatStateUsing(fn ($state) => Str::of($state)->snake()->title()->replace('_', ' '))->badge()->hiddenLabel(),
                    ])->grid(['md' => 2, 'lg' => 4])->hiddenLabel(),
                ])->columns(1),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('department.short_name')->label('Department')
                    ->searchable()->sortable(),
                Tables\Columns\IconColumn::make('meals_active')->boolean(),
                Tables\Columns\TextColumn::make('meals_allowance')->money('kes')->alignRight(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ActiveStatusColumn::make(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Vanadi\Framework\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \Vanadi\Framework\Filament\Resources\UserResource\Pages\CreateUser::route('/create'),
            'view' => \Vanadi\Framework\Filament\Resources\UserResource\Pages\ViewUser::route('/{record}'),
            'edit' => \Vanadi\Framework\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
