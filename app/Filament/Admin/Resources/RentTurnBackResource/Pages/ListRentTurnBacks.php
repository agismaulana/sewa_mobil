<?php

namespace App\Filament\Admin\Resources\RentTurnBackResource\Pages;

use App\Filament\Admin\Resources\RentTurnBackResource;
use App\Models\Rent;
use App\Models\RentTurnBack;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ListRentTurnBacks extends ListRecords
{
    protected static string $resource = RentTurnBackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->form([
                    TextInput::make('plate_number')
                        ->label('Plate Number')
                        ->required()
                        ->maxLength(20)
                        ->afterStateUpdated(function(Get $get, Set $set) {
                            $rent = Rent::whereHas('car', function (Builder $query) use ($get) {
                                $query->where('plate_number', $get('plate_number'));
                            })
                            ->where('status', Rent::ISBEINGBORROWED);

                            if(!auth()->user()->hasRole('super_admin')) {
                                $rent = $rent->where('user_id', auth()->user()->id);
                            }


                            $rent = $rent->first();

                            if ($rent) {
                                Notification::make()
                                    ->success()
                                    ->title('Rent Found')
                                    ->body('Rent has been found')
                                    ->send();

                                $set('visibleRent', true);
                                $set('rent_id', $rent->id);
                                $set('car_id', $rent->car->id);
                                $set('start_price', $rent->start_price);
                            } else {
                                Notification::make()
                                    ->warning()
                                    ->title('Rent Not Found')
                                    ->body('Rent has not been found')
                                    ->send();
                            }
                        })
                        ->live(),
                    Section::make('Rent')
                        ->visible(fn (Get $get) => $get('visibleRent'))
                        ->schema([
                            Select::make('rent_id')
                                ->relationship('rent', 'code_rent')
                                ->label('Rent')
                                ->searchable()
                                ->required(),
                            Select::make('car_id')
                                ->relationship('rent.car', 'plate_number')
                                ->label('Car')
                                ->searchable()
                                ->required(),
                            TextInput::make('start_price')
                                ->label('Start Price')
                                ->required(),
                        ]),
                ])
                ->using(function (array $data) {
                        $rentTurnBack = new RentTurnBack();

                        $rentTurnBack->fill([
                            'rent_id' => $data['rent_id'],
                            'return_date' => now(),
                            'price' => 0,
                            'penalty' => 0
                        ]);

                        $rentTurnBack->save();
                }),
        ];
    }
}
