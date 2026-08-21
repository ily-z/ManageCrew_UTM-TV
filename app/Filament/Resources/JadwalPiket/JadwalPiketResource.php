<?php

namespace App\Filament\Resources\JadwalPiket;

use App\Filament\Resources\JadwalPiket\Pages\CreateJadwalPiket;
use App\Filament\Resources\JadwalPiket\Pages\EditJadwalPiket;
use App\Filament\Resources\JadwalPiket\Pages\ListJadwalPikets;
use App\Filament\Resources\JadwalPiket\RelationManagers\AbsensiRelationManager;
use App\Filament\Resources\JadwalPiket\Schemas\JadwalPiketForm;
use App\Filament\Resources\JadwalPiket\Tables\JadwalPiketTable;
use App\Models\JadwalPiket;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JadwalPiketResource extends Resource
{
    protected static ?string $model = JadwalPiket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Penjadwalan';

    protected static ?string $modelLabel = 'Jadwal Piket';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return JadwalPiketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalPiketTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AbsensiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJadwalPikets::route('/'),
            'create' => CreateJadwalPiket::route('/create'),
            'edit' => EditJadwalPiket::route('/{record}/edit'),
        ];
    }
}
