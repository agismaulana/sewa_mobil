<?php

namespace App\Filament\Admin\Resources\UsersResource\Pages;

use App\Filament\Admin\Resources\UsersResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListUsers extends ListRecords
{
    protected static string $resource = UsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data) {
                    DB::beginTransaction();
                        $users = new User();

                        $users->fill($data);

                        $users->save();

                        $users->assignRole($data['role']);
                    DB::commit();
                }),
        ];
    }
}
