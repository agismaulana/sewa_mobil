<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UsersResource\Pages;
use App\Filament\Admin\Resources\UsersResource\RelationManagers;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->maxLength(120)
                                    ->required(),
                                TextInput::make('email')
                                    ->maxLength(120)
                                    ->required(),
                            ])
                            ->columns(2),
                        TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->type('number')
                            ->required(
                                fn () => $form->getOperation() === 'create'
                            ),
                        TextInput::make('driver_license_number')
                            ->label('Driver License Number')
                            ->maxLength(20)
                            ->required(
                                fn () => $form->getOperation() === 'create'
                            ),
                        Grid::make()
                            ->schema([
                                TextInput::make('password')
                                    ->password()
                                    ->maxLength(120)
                                    ->required(
                                        fn () => $form->getOperation() === 'create'
                                    ),
                                TextInput::make('password_confirmation')
                                    ->password()
                                    ->maxLength(120)
                                    ->required(
                                        fn () => $form->getOperation() === 'create'
                                    )
                                    ->same('password'),
                            ])
                            ->columns(2),
                        TextArea::make('address')
                            ->nullable()
                            ->cols(3)
                            ->rows(3),
                        FileUpload::make('image')
                            ->image()
                            ->maxSize(1024 * 5)
                            ->directory('users')
                            ->nullable(),
                        Select::make('role')
                            ->label('Role')
                            ->options(function () {
                                if(auth()->user()->hasRole('user')) {
                                    return Role::where('name', '!=', 'super_admin')
                                        ->get()
                                        ->pluck('name', 'name');
                                }
                                return Role::all()->pluck('name', 'name');
                            }),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->sortable()
                    ->formatStateUsing(
                        fn (string $state): string => Carbon::parse($state)->diffForHumans()
                    ),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->options(Role::all()->pluck('name', 'name')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function (User $record, array $data) {

                        if($data['password']) {
                            $data['password'] = bcrypt($data['password']);
                        } else {
                            $data['password'] = $record->password;
                        }

                        $record->update($data);

                        $record->syncRoles($data['role']);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUsers::route('/create'),
            // 'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }
}
