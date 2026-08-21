<?php

namespace App\Filament\Resources\Kegiatan;

use App\Filament\Resources\Kegiatan\Pages\CreateKegiatan;
use App\Filament\Resources\Kegiatan\Pages\EditKegiatan;
use App\Filament\Resources\Kegiatan\Pages\ListKegiatans;
use App\Filament\Resources\Kegiatan\Schemas\KegiatanForm;
use App\Filament\Resources\Kegiatan\Tables\KegiatanTable;
use App\Models\Kegiatan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KegiatanResource extends Resource
{
    protected static ?string $model = Kegiatan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|UnitEnum|null $navigationGroup = 'Penjadwalan';

    protected static ?string $modelLabel = 'Kegiatan';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return KegiatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KegiatanTable::configure($table);
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
            'index' => ListKegiatans::route('/'),
            'create' => CreateKegiatan::route('/create'),
            'edit' => EditKegiatan::route('/{record}/edit'),
        ];
    }
}
