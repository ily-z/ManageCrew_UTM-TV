<?php

namespace App\Filament\Resources\AbsensiKegiatanResource;

use App\Filament\Resources\AbsensiKegiatanResource\Pages\ListAbsensiKegiatans;
use App\Filament\Resources\AbsensiKegiatanResource\Tables\AbsensiKegiatanTable;
use App\Models\AbsensiKegiatan;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AbsensiKegiatanResource extends Resource
{
    protected static ?string $model = AbsensiKegiatan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Absensi';

    protected static ?string $modelLabel = 'Absensi Kegiatan';

    protected static ?int $navigationSort = 7;

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
        return AbsensiKegiatanTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbsensiKegiatans::route('/'),
        ];
    }
}
