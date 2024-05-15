<?php

namespace App\Filament\Admin\Resources\RegisterResource\Pages;

use App\Filament\Admin\Resources\RegisterResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Resources\Pages\Page;

class Register extends BaseRegister
{

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                Grid::make()
                    ->schema([
                        TextInput::make('phone_number')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->required(),
                        TextInput::make('driving_license_number')
                            ->label('Nomor SIM')
                            ->type('number')
                            ->required(),
                    ])
                    ->columns(2),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

}
