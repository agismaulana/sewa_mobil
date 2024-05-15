<?php

namespace App\Filament\Admin\Resources\RentResource\Pages;

use App\Filament\Admin\Resources\RentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRents extends ListRecords
{
    protected static string $resource = RentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
