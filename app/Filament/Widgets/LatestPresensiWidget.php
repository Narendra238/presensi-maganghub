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
        $locale = app()->getLocale();

        $heading = match ($locale) {
            'en' => 'Recent Attendance & Activity Logbook',
            'ar' => 'سجل الحضور والأنشطة الأخير',
            default => 'Logbook & Aktivitas Presensi Terbaru',
        };

        $lblDate = match ($locale) {
            'en' => 'Date',
            'ar' => 'التاريخ',
            default => 'Tanggal',
        };

        $lblStatus = match ($locale) {
            'en' => 'Status',
            'ar' => 'الحالة',
            default => 'Status',
        };

        $lblActivity = match ($locale) {
            'en' => 'Activity Description',
            'ar' => 'وصف النشاط',
            default => 'Uraian Aktivitas',
        };

        return $table
            ->query(
                Presensi::query()
                    ->where('user_id', Auth::id())
                    ->latest('tanggal')
            )
            ->heading($heading)
            ->paginated([5, 10])
            ->columns([
                TextColumn::make('tanggal')
                    ->label($lblDate)
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('kehadiran')
                    ->label($lblStatus)
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hadir' => match ($locale) {
                            'en' => 'Present',
                            'ar' => 'حاضر',
                            default => 'Hadir',
                        },
                        'tidak_hadir_dengan_keterangan' => match ($locale) {
                            'en' => 'Excused / Sick',
                            'ar' => 'إذن / مرضي',
                            default => 'Izin/Sakit',
                        },
                        'tidak_hadir_tanpa_keterangan' => match ($locale) {
                            'en' => 'Unexcused',
                            'ar' => 'بدون عذر',
                            default => 'Tanpa Keterangan',
                        },
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'tidak_hadir_dengan_keterangan' => 'warning',
                        'tidak_hadir_tanpa_keterangan' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('uraian_aktivitas')
                    ->label($lblActivity)
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
