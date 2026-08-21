<?php

namespace App\Filament\Resources\AbsensiPiketResource;

use App\Filament\Resources\AbsensiPiketResource\Pages\ListAbsensiPikets;
use App\Filament\Resources\AbsensiPiketResource\Tables\AbsensiPiketTable;
use App\Models\AbsensiPiket;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AbsensiPiketResource extends Resource
{
    protected static ?string $model = AbsensiPiket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFingerPrint;

    protected static string|UnitEnum|null $navigationGroup = 'Absensi';

    protected static ?string $modelLabel = 'Absensi Piket';

    protected static ?int $navigationSort = 6;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return AbsensiPiketTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbsensiPikets::route('/'),
        ];
    }
}
