<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages;
use App\Models\Category;
use App\Models\File;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use function Laravel\Prompts\select;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';


    public static function getNavigationLabel(): string
    {
        return __('attributes.files');
    }

    public static function getModelLabel(): string
    {
        return __('attributes.file');
    }

    public static function getPluralModelLabel(): string
    {
        return __('attributes.files');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(request()->user()->id),
                TextInput::make('registeration_number')
                    ->label(__('attributes.registeration_number'))
                    ->numeric(),
                Textarea::make('description')
                    ->label(__('attributes.description'))
                    ->maxLength(10000),
                TextInput::make('debtor_amount')
                    ->label(__('attributes.debtor_amount'))
                    ->numeric(),
                TextInput::make('creditor_amount')
                    ->label(__('attributes.creditor_amount'))
                    ->numeric(),
                DatePicker::make('date')
                    ->label(__('attributes.date'))
                    ->required(),
                Select::make('category_id')
                    ->label(__('attributes.category'))
                    ->relationship('category', 'id')
                    ->exists('categories', 'id')
                    ->live()
                    ->preload()
                    ->options(
                        function () {
                            return Category::all()->pluck('name', 'id');
                        }
                    )
                    ->nullable()
                    ->default(null),
                FileUpload::make('path')
                    ->label(__('attributes.file'))
                    ->moveFiles()
                    ->acceptedFileTypes(['application/pdf'])
                    ->preserveFilenames()
                    ->disk('private')
                    ->directory('pdf_files'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.username')
                    ->label(__('attributes.username'))
                    ->searchable(isIndividual: true),
                TextColumn::make('category.name')
                    ->label(__('attributes.category_name'))
                    ->searchable(isIndividual: true),
                TextColumn::make('description')
                    ->label(__('attributes.description'))
                    ->words(20)
                    ->wrap()
                    ->searchable(isIndividual: true),
                TextColumn::make('registeration_number')
                    ->label(__('attributes.registeration_number'))
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('debtor_amount')
                    ->label(__('attributes.debtor_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('creditor_amount')
                    ->label(__('attributes.creditor_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->label(__('attributes.date'))
                    ->date('d/m/Y')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('path')
                    ->url(fn ($record) => route('file.download', ['fileId' => $record->id])) // Assuming 'path' stores the file path
                    ->label(__('attributes.file'))
                    ->formatStateUsing(fn () => __('attributes.download_file'))
                    ->visible(request()->user()->hasPermission('file.download'))
                    ->color('success'),
                TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('date')
                    ->label(__('attributes.year'))
                    ->options(function () {
                        return File::selectRaw('YEAR(date) as year')
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year')
                            ->filter(function ($year) {
                                return !is_null($year) && $year !== '';
                            })
                            ->mapWithKeys(function ($item) {
                                return [$item => (string)$item];
                            })
                            ->toArray();
                    })
                    ->query(function (Builder $query, $data) {
                        if ($data['value']) {
                            return $query->whereYear('date', $data['value']);
                        }
                    }),
                SelectFilter::make('category_id')
                    ->label(__('attributes.category_name'))
                    ->relationship('category', 'name')
            ], layout: FiltersLayout::AboveContent)
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
            'index' => Pages\ListFiles::route('/'),
            'create' => Pages\CreateFile::route('/create'),
            'edit' => Pages\EditFile::route('/{record}/edit'),
        ];
    }
}
