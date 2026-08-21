<?php

namespace App\Filament\Resources\LogbookResource;

use App\Enums\StatusLogbook;
use App\Models\Logbook;
use App\Filament\Resources\LogbookResource\Pages\ListLogbooks;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LogbookResource extends Resource
{
    protected static ?string $model = Logbook::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Evaluasi';

    protected static ?string $modelLabel = 'Logbook';

    protected static ?int $navigationSort = 4;

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
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                TextColumn::make('kegiatan.nama')
                    ->label('Kegiatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Anggota')
                    ->searchable(),
                TextColumn::make('isi')
                    ->limit(60),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('catatan_admin')
                    ->label('Catatan Admin')
                    ->limit(40)
                    ->placeholder('-'),
                TextColumn::make('submitted_at')
                    ->label('Disubmit')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusLogbook::class)
                    ->default(StatusLogbook::Pending->value),
                SelectFilter::make('kegiatan')
                    ->relationship('kegiatan', 'nama')
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== StatusLogbook::Approved)
                    ->action(function ($record) {
                        $record->update(['status' => StatusLogbook::Approved]);
                    }),
                Action::make('mintaRevisi')
                    ->label('Minta Revisi')
                    ->icon('heroicon-m-arrow-path')
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('catatan_admin')
                            ->label('Catatan Revisi')
                            ->required()
                            ->helperText('Akan terlihat oleh anggota di web app.'),
                    ])
                    ->visible(fn ($record) => $record->status !== StatusLogbook::Approved)
                    ->action(function (array $data, $record) {
                        $record->update([
                            'status' => StatusLogbook::Revisi,
                            'catatan_admin' => $data['catatan_admin'],
                        ]);
                    }),
                \Filament\Actions\ViewAction::make()
                    ->modalDescription(fn ($record) => $record->isi),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLogbooks::route('/'),
        ];
    }
}
