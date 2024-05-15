<?php

namespace App\Filament\Admin\Resources\RentTurnBackResource\Pages;

use App\Filament\Admin\Resources\RentTurnBackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentTurnBack extends EditRecord
{
    protected static string $resource = RentTurnBackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
