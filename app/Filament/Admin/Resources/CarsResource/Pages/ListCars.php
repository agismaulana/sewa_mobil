<?php

namespace App\Filament\Admin\Resources\CarsResource\Pages;

use App\Filament\Admin\Resources\CarsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCars extends ListRecords
{
    protected static string $resource = CarsResource::class;

    protected ?string $heading = 'Cars';

    protected ?string $subheading = 'List of cars';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
