<?php

namespace App\Filament\Resources\Kegiatan\Schemas;

use App\Enums\JenisKegiatan;
use App\Enums\StatusKegiatan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class KegiatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('jenis')
                    ->options(JenisKegiatan::class)
                    ->default('liputan')
                    ->required(),
                DatePicker::make('tanggal')
                    ->required(),
                Select::make('status')
                    ->options(StatusKegiatan::class)
                    ->default('draft')
                    ->required()
                    ->helperText('Absensi hanya aktif saat status bukan Draft.'),
                TextInput::make('lokasi')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                TimePicker::make('waktu_mulai')
                    ->label('Waktu Mulai')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('waktu_selesai')
                    ->label('Waktu Selesai')
                    ->seconds(false)
                    ->required(),
                Textarea::make('deskripsi')
                    ->columnSpanFull(),
                Repeater::make('peserta')
                    ->relationship('peserta')
                    ->label('Peserta & Tugas')
                    ->helperText('Assign anggota beserta tugasnya (reporter, fotografer, editor, dsb). Baris absensi otomatis dibuat.')
                    ->schema([
                        Select::make('user_id')
                            ->label('Anggota')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        TextInput::make('tugas')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('reporter / fotografer / editor'),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
