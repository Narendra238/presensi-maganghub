<?php

namespace App\Filament\Resources\Presensis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PresensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('kehadiran')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hadir' => 'Hadir',
                        'tidak_hadir_dengan_keterangan' => 'Tidak Hadir (Keterangan)',
                        'tidak_hadir_tanpa_keterangan' => 'Tidak Hadir (Tanpa Keterangan)',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'tidak_hadir_dengan_keterangan' => 'warning',
                        'tidak_hadir_tanpa_keterangan' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('uraian_aktivitas')
                    ->label('Aktivitas')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('pembelajaran')
                    ->label('Pembelajaran')
                    ->limit(30),

                TextColumn::make('kendala')
                    ->label('Kendala')
                    ->limit(30)
                    ->default('-'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->recordUrl(null)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
