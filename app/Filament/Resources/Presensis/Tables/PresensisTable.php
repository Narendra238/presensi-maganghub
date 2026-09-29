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
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Date',
                        'ar' => 'التاريخ',
                        default => 'Tanggal',
                    })
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('kehadiran')
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Status',
                        'ar' => 'الحالة',
                        default => 'Status',
                    })
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hadir' => match (app()->getLocale()) {
                            'en' => 'Present',
                            'ar' => 'حاضر',
                            default => 'Hadir',
                        },
                        'tidak_hadir_dengan_keterangan' => match (app()->getLocale()) {
                            'en' => 'Excused / Sick',
                            'ar' => 'إذن / مرضي',
                            default => 'Tidak Hadir (Keterangan)',
                        },
                        'tidak_hadir_tanpa_keterangan' => match (app()->getLocale()) {
                            'en' => 'Unexcused',
                            'ar' => 'بدون عذر',
                            default => 'Tidak Hadir (Tanpa Keterangan)',
                        },
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
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Activity Description',
                        'ar' => 'وصف النشاط',
                        default => 'Aktivitas',
                    })
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('pembelajaran')
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Learnings Acquired',
                        'ar' => 'الدروس المستفادة',
                        default => 'Pembelajaran',
                    })
                    ->limit(30),

                TextColumn::make('kendala')
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Obstacles',
                        'ar' => 'المعوقات',
                        default => 'Kendala',
                    })
                    ->limit(30)
                    ->default('-'),

                TextColumn::make('keterangan')
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Reason / Notes',
                        'ar' => 'الملاحظات / السبب',
                        default => 'Keterangan',
                    })
                    ->limit(30)
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Created At',
                        'ar' => 'تاريخ الإنشاء',
                        default => 'Dibuat',
                    })
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
