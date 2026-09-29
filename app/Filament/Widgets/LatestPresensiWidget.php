<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Presensis\PresensiResource;
use App\Models\Presensi;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class LatestPresensiWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Presensi::query()
                    ->where('user_id', Auth::id())
                    ->latest('tanggal')
            )
            ->heading('Logbook & Aktivitas Presensi Terbaru')
            ->paginated([5, 10])
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('kehadiran')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hadir' => 'Hadir',
                        'tidak_hadir_dengan_keterangan' => 'Izin/Sakit',
                        'tidak_hadir_tanpa_keterangan' => 'Tanpa Keterangan',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'tidak_hadir_dengan_keterangan' => 'warning',
                        'tidak_hadir_tanpa_keterangan' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('uraian_aktivitas')
                    ->label('Uraian Aktivitas')
                    ->limit(45)
                    ->placeholder('—')
                    ->tooltip(fn ($record) => $record->uraian_aktivitas),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Presensi $record): string => route('filament.admin.resources.presensis.edit', $record)),
            ]);
    }
}
