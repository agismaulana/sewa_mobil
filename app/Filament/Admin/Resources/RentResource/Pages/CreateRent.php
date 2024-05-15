<?php

namespace App\Filament\Admin\Resources\RentResource\Pages;

use App\Filament\Admin\Resources\RentResource;
use App\Models\Rent;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CreateRent extends CreateRecord
{
    protected static string $resource = RentResource::class;

    protected function beforeCreate(): void
    {
        $getDataCurrentBorrowedCar = static::getModel()::query()
            ->whereBetween('start_date', [
                $this->data['start_date'],
                $this->data['end_date']
            ])
            ->orWhereBetween('end_date', [
                $this->data['start_date'],
                $this->data['end_date']
            ])
            ->where('status', 'is being borrowed')
            ->first();

        if ($getDataCurrentBorrowedCar) {
            $this->notify('danger', 'Car is currently being borrowed');
            $this->redirect($this->getRedirectUrl());
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        DB::beginTransaction();

        $rent = static::getModel()::create([
            'user_id' => auth()->user()->id,
            'car_id' => $data['car_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'start_price' => $data['start_price'],
            'duration' => $data['duration'],
        ]);

        DB::commit();

        return $rent;
    }
}
