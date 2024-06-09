<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    use Translatable;

    protected static ?string $model = User::class;

    public static function getNavigationLabel(): string
    {
        return __('attributes.users');
    }

    public static function getModelLabel(): string
    {
        return __('attributes.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('attributes.users');
    }

    public static function canEdit($record): bool
    {
        return $record->role->name !== 'owner';
    }

    public static function canDelete($record): bool
    {
        return $record->role->name !== 'owner';
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('attributes.name'))
                    ->minLength(2)->maxLength(15)->string()
                    ->required()
                    ->maxLength(255),
                TextInput::make('username')
                    ->label(__('attributes.username'))
                    ->required()
                    ->regex('/^[a-zA-Z0-9]{5,14}$/')
                    ->validationMessages([
                        'regex' => __('authpage.The username must be between 6 to 14 characters and contain only letters and numbers.'),
                    ])
                    ->unique(User::class, 'username', ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('attributes.email'))
                    ->email()
                    ->unique(User::class, 'email', ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone_num')
                    ->label(__('attributes.phone_num'))
                    ->tel()
                    ->maxLength(255)
                    ->default(null),
                Select::make('role_id')
                    ->label(__('attributes.role'))
                    ->relationship('role', 'id')
                    ->exists('roles', 'id')
                    ->live()
                    ->preload()
                    ->options(
                        function () {
                            return Role::whereNotIn('name', ['owner'])->pluck('name', 'id');
                        }
                    ),
                TextInput::make('password')
                    ->label(__('attributes.password'))
                    ->password()
                    ->hiddenOn('edit')
                    ->required()
                    ->maxLength(255),
                Hidden::make('email_verified_at')->default(now())
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('attributes.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('attributes.name'))
                    ->searchable(),
                TextColumn::make('username')
                    ->label(__('attributes.username'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('attributes.email'))
                    ->searchable(),
                TextColumn::make('phone_num')
                    ->label(__('attributes.phone_num'))
                    ->searchable(),
                TextColumn::make('role.name')
                    ->label(__('attributes.role'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
