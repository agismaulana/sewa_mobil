<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RentResource\Pages;
use App\Filament\Admin\Resources\RentResource\RelationManagers;
use App\Models\Cars;
use App\Models\Rent;
use App\Models\RentTurnBack;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Exists;

class RentResource extends Resource
{
    protected static ?string $model = Rent::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = "Rents";

    protected static ?string $navigationGroup = "Transaction";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('start_date')
                                ->type('date')
                                ->label('Start Date')
                                ->default(date('Y-m-d'))
                                ->afterStateUpdated(function (Set $set, string $state, Get $get) {
                                    $price = $get('start_price');
                                    $duration = (strtotime($get('end_date')) - strtotime($state)) / 86400;
                                    $set('duration', $duration);
                                    $set('start_price', $price * $duration);
                                })
                                ->required()
                                ->live(),
                            TextInput::make('end_date')
                                ->type('date')
                                ->label('End Date')
                                ->default(date('Y-m-d', strtotime('+1 day')))
                                ->afterStateUpdated(function (Set $set, string $state, Get $get) {
                                    $price = $get('start_price');
                                    $duration = (strtotime($state) - strtotime($get('start_date'))) / 86400;
                                    $set('duration', $duration);
                                    $set('start_price', $price * $duration);
                                })
                                ->required()
                                ->live(),
                        ]),
                        Select::make('car_id')
                            ->relationship('car', 'brand_name')
                            ->label('Car')
                            ->searchable(function(Get $get, string $search = '') {
                                $cars = Cars::inRandomOrder()
                                    ->with(['rents'])
                                    ->whereDoesntHave('rents', function (Builder $query) use ($get) {
                                        return $query
                                            ->whereBetween('end_date', [$get('start_date'), $get('end_date')])
                                            ->orWhereBetween('start_date', [$get('start_date'), $get('end_date')])
                                            ->where('status', Rent::ISBEINGBORROWED);
                                    })
                                    ->where('brand_name', 'like', '%' . $search . '%')
                                    ->take(5)
                                    ->get()
                                    ->pluck('brand_name', 'id');

                                return $cars;
                            })
                            ->options(function(Get $get) {
                                $cars = Cars::inRandomOrder()
                                    ->with(['rents'])
                                    ->whereDoesntHave('rents', function (Builder $query) use ($get) {
                                        return $query
                                            ->whereBetween('end_date', [$get('start_date'), $get('end_date')])
                                            ->orWhereBetween('start_date', [$get('start_date'), $get('end_date')])
                                            ->where('status', Rent::ISBEINGBORROWED);
                                    })
                                    ->take(5)
                                    ->get()
                                    ->pluck('brand_name', 'id');

                                return $cars;
                            })
                            ->afterStateUpdated(function(Get $get, $state, Set $set) {
                                $car = Cars::find($state);

                                $price = $car->price * $get('duration');

                                $set('start_price', $price);
                            })
                            ->required()
                            ->live(),
                        TextInput::make('duration')
                            ->label('Duration')
                            ->readOnly()
                            ->type('number')
                            ->default(function (Get $get) {
                                return (strtotime($get('end_date')) - strtotime($get('start_date'))) / 86400;
                            })
                            ->nullable()
                            ->live(),
                        TextInput::make('start_price')
                            ->label('Price')
                            ->readOnly()
                            ->type('number')
                            ->default(0)
                            ->nullable()
                            ->live(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('car.image')
                    ->label('Foto Mobil'),
                TextColumn::make('car.brand_name')
                    ->label('Mobil')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        Rent::ISBEINGBORROWED => 'primary',
                        Rent::RETURNED => 'success',
                        Rent::CANCELLED => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Rent::ISBEINGBORROWED => 'Dipinjam',
                        Rent::RETURNED => 'Dikembalikan',
                        Rent::CANCELLED => 'Dibatalkan',
                    }),
                TextColumn::make('end_date')
                    ->label('Berakhir Tanggal')
                    ->formatStateUsing(fn (string $state): string => Carbon::parse($state)->isoFormat('D MMMM Y')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('return_car')
                    ->icon('heroicon-o-receipt-refund')
                    ->label(__('Return Car'))
                    ->action(function(Rent $record) {
                        $rent_turn_back = new RentTurnBack();
                        $rent_turn_back->rent_id = $record->id;
                        $rent_turn_back->return_date = now();
                        $rent_turn_back->price = 0;
                        $rent_turn_back->penalty = 0;
                        $rent_turn_back->save();

                        Notification::make()
                            ->title(__('Return Car'))
                            ->body(__('Return Car Success'))
                            ->success()
                            ->send();

                        return redirect()->route('filament.admin.resources.rents.index');
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('Return Car'))
                    ->modalDescription(__('Are you sure you want to return this car?'))
                    ->modalSubmitActionLabel(__('Return Car'))
                    ->modalCancelActionLabel(__('Cancel'))
                    ->color('success')
                    ->visible(function(Rent $record) {
                        if($record->user_id != auth()->user()->id) {
                            return false;
                        }

                        return $record->status == Rent::ISBEINGBORROWED;
                    }),
                Tables\Actions\ViewAction::make()
                    ->form([
                        Grid::make(2)->schema([
                            TextInput::make('start_date')
                                ->type('date')
                                ->label('Start Date'),
                            TextInput::make('end_date')
                                ->type('date')
                                ->label('End Date'),
                        ]),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('User'),
                        Select::make('car_id')
                            ->relationship('car', 'brand_name')
                            ->label('Car')
                            ->searchable(function(Get $get, string $search = '') {
                                $cars = Cars::inRandomOrder()
                                    ->with(['rents'])
                                    ->whereDoesntHave('rents', function (Builder $query) use ($get) {
                                        return $query->where('end_date', '<=', $get('end_date'))
                                            ->where('start_date', '>=', $get('start_date'));
                                    })
                                    ->where('brand_name', 'like', '%' . $search . '%')
                                    ->take(5)
                                    ->get()
                                    ->pluck('brand_name', 'id');

                                return $cars;
                            })
                            ->options(function(Get $get) {
                                $cars = Cars::inRandomOrder()
                                    ->with(['rents'])
                                    ->whereDoesntHave('rents', function (Builder $query) use ($get) {
                                        return $query->where('end_date', '<=', $get('end_date'))
                                            ->where('start_date', '>=', $get('start_date'));
                                    })
                                    ->take(5)
                                    ->get()
                                    ->pluck('brand_name', 'id');

                                return $cars;
                            }),
                        TextInput::make('duration')
                            ->label('Duration')
                            ->type('number')
                            ->default(function (Get $get) {
                                return (strtotime($get('end_date')) - strtotime($get('start_date'))) / 86400;
                            }),
                        TextInput::make('start_price')
                            ->label('Price')
                            ->type('number')
                            ->default(0),
                    ]),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                if(!auth()->user()->hasRole('super_admin')) {
                    return $query->where('user_id', auth()->user()->id);
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
            'index' => Pages\ListRents::route('/'),
            'create' => Pages\CreateRent::route('/create'),
            'edit' => Pages\EditRent::route('/{record}/edit'),
        ];
    }
}
