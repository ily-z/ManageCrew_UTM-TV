<?php

namespace App\Filament\Resources\NilaiResource\Schemas;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NilaiForm
{
    public static function kriteriaFields(): array
    {
        return collect([
            'partisipasi' => 'Partisipasi',
            'ketepatan_waktu' => 'Ketepatan Waktu',
            'kualitas_tugas' => 'Kualitas Tugas',
            'kualitas_logbook' => 'Kualitas Logbook',
        ])
            ->map(fn ($label, $key) => TextInput::make("kriteria.{$key}")
                ->label($label)
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($get, $set, $state) use ($key) {
                    self::hitungTotal($get, $set);
                }))
            ->values()
            ->all();
    }

    public static function hitungTotal($get, $set): void
    {
        $nilai = array_filter([
            $get('kriteria.partisipasi'),
            $get('kriteria.ketepatan_waktu'),
            $get('kriteria.kualitas_tugas'),
            $get('kriteria.kualitas_logbook'),
        ], fn ($v) => $v !== null && $v !== '');

        if (count($nilai) === 4) {
            $set('nilai_total', round(array_sum($nilai) / 4, 2));
        }
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kegiatan_id')
                    ->relationship('kegiatan', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Sebaiknya hanya anggota yang terlibat kegiatan tersebut.'),
                Section::make('Kriteria Penilaian (0-100)')->schema(self::kriteriaFields())->columns(2)->columnSpanFull(),
                TextInput::make('nilai_total')
                    ->numeric()
                    ->readOnly()
                    ->dehydrated(true)
                    ->helperText('Rata-rata sederhana dari 4 kriteria.'),
                Textarea::make('catatan')
                    ->columnSpanFull(),
            ]);
    }
}
