<?php

namespace App\Filament\Resources\JadwalPiket\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class JadwalPiketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('hari')
                    ->label('Tanggal Piket')
                    ->required()
                    ->columnSpan(1),
                TimePicker::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->seconds(false)
                    ->required()
                    ->afterOrEqual('jam_mulai'),
                Select::make('anggota')
                    ->relationship('anggota', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->helperText('Anggota yang wajib piket pada slot ini. Baris absensi otomatis dibuat.')
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
