<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CarsResource\Pages;
use App\Filament\Admin\Resources\CarsResource\RelationManagers;
use App\Models\Cars;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarsResource extends Resource
{
    protected static ?string $model = Cars::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Cars';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('brand_name')
                            ->required(),
                        TextInput::make('model_name')
                            ->required(),
                    ]),
                    TextInput::make('price')
                        ->label('price per day')
                        ->type('number')
                        ->required(),
                    TextInput::make('color')
                        ->nullable(),
                    TextInput::make('year')
                        ->type('number')
                        ->nullable(),
                    TextInput::make('plate_number')
                        ->maxLength(10)
                        ->nullable(),
                    FileUpload::make('image')
                        ->image()
                        ->maxSize(1024 * 5)
                        ->directory('cars')
                        ->nullable(),
                    TextArea::make('description')
                        ->nullable(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->height(50),
                TextColumn::make('brand_name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('model_name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->formatStateUsing(fn (string $state): string => Carbon::parse($state)->diffForHumans()),
            ])
            ->filters([
                SelectFilter::make('model_name')
                    ->options(Cars::all()->pluck('model_name', 'model_name'))
                    ->label('Model Name')
                    ->placeholder('Model Name')
                    ->multiple(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCars::route('/'),
            // 'create' => Pages\CreateCars::route('/create'),
            // 'edit' => Pages\EditCars::route('/{record}/edit'),
        ];
    }
}
