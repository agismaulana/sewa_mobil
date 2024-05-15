<?php

namespace App\Filament\Admin\Resources\RentResource\Pages;

use App\Filament\Admin\Resources\RentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRent extends EditRecord
{
    protected static string $resource = RentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
