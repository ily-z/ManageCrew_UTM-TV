<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->nullable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText('Kosongkan untuk anggota (login via Google). Diisi hanya untuk akun admin.'),
                Select::make('role')
                    ->options(UserRole::class)
                    ->default('anggota')
                    ->required(),
                TextInput::make('no_induk')
                    ->label('No. Induk / NIS')
                    ->maxLength(50),
                TextInput::make('divisi')
                    ->maxLength(100),
                TextInput::make('angkatan')
                    ->maxLength(20),
                TextInput::make('no_hp')
                    ->tel()
                    ->maxLength(20),
                FileUpload::make('foto')
                    ->image()
                    ->disk('public')
                    ->directory('profil')
                    ->imageEditor()
                    ->maxSize(2048),
                Toggle::make('status_aktif')
                    ->default(true)
                    ->helperText('Nonaktif = tidak bisa login/absen, tapi histori tetap tersimpan.'),
            ])
            ->columns(2);
    }
}
