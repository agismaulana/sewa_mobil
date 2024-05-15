<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RentTurnBackResource\Pages;
use App\Filament\Admin\Resources\RentTurnBackResource\RelationManagers;
use App\Models\Cars;
use App\Models\Rent;
use App\Models\RentTurnBack;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentTurnBackResource extends Resource
{
    protected static ?string $model = RentTurnBack::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Rent Turn Back';

    protected static ?string $navigationGroup = 'Transaction';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code_return')
                    ->label('Code Return')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('rent.car.image')
                    ->label('Image')
                    ->square(),
                TextColumn::make('rent.car.brand_name')
                    ->label('Car Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rent.user.name')
                    ->label('Customer Name')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->money('IDR'),
                TextColumn::make('penalty')
                    ->label('Penalti')
                    ->sortable()
                    ->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('secondary')
                    ->modalContent(function (RentTurnBack $record): View {
                        return view('filament.resources.rent-turn-backs.detail', [
                            'record' => $record,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                if(!auth()->user()->hasRole('super_admin')) {
                    return $query->whereHas('rent', function($newQuery) {
                        $newQuery->where('user_id', auth()->user()->id);
                    });
                }

                return $query;
            });
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
            'index' => Pages\ListRentTurnBacks::route('/'),
            // 'create' => Pages\CreateRentTurnBack::route('/create'),
            // 'edit' => Pages\EditRentTurnBack::route('/{record}/edit'),
        ];
    }
}
