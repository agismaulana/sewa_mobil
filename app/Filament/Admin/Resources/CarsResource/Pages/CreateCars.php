<?php

namespace App\Filament\Admin\Resources\CarsResource\Pages;

use App\Filament\Admin\Resources\CarsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCars extends CreateRecord
{
    protected static string $resource = CarsResource::class;
}
