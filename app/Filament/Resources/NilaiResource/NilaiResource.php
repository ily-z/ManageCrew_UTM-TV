<?php

namespace App\Filament\Resources\NilaiResource;

use App\Filament\Resources\NilaiResource\Pages\CreateNilai;
use App\Filament\Resources\NilaiResource\Pages\EditNilai;
use App\Filament\Resources\NilaiResource\Pages\ListNilais;
use App\Filament\Resources\NilaiResource\Schemas\NilaiForm;
use App\Filament\Resources\NilaiResource\Tables\NilaiTable;
use App\Models\Nilai;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NilaiResource extends Resource
{
    protected static ?string $model = Nilai::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'Evaluasi';

    protected static ?string $modelLabel = 'Nilai';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return NilaiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NilaiTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNilais::route('/'),
            'create' => CreateNilai::route('/create'),
            'edit' => EditNilai::route('/{record}/edit'),
        ];
    }
}
