<?php

namespace App\Filament\Admin\Resources\RegisterResource\Pages;

use App\Filament\Admin\Resources\RegisterResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as AuthEditProfile;
use Filament\Resources\Pages\Page;

class EditProfile extends AuthEditProfile
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
                        TextInput::make('driver_license_number')
                            ->label('Nomor SIM')
                            ->type('number')
                            ->required(),
                    ])
                    ->columns(2),
                $this->getPasswordFormComponent(),
                TextArea::make('address')
                    ->label('Alamat')
                    ->nullable(),
                FileUpload::make('image')
                    ->image()
                    ->label('Foto Profil')
                    ->maxSize(1024 * 5)
                    ->directory('users'),
            ]);
    }
}
