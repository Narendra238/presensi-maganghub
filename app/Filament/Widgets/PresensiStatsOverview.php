<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PresensiStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $userId = Auth::id();
        $today = Carbon::today();
        $todayStr = $today->format('Y-m-d');
        $holidays = config('holidays', []);

        // 1. Status Presensi Hari Ini
        $statHariIni = $this->buildStatusHariIniStat($userId, $today, $todayStr, $holidays);

        // 2. Total Hadir Kumulatif
        $totalHariMagang = $this->countTotalWorkdays(
            config('magang.tanggal_mulai'),
            config('magang.tanggal_selesai'),
            $holidays
        );
        $totalHadir = Presensi::where('user_id', $userId)->where('kehadiran', 'hadir')->count();
        $persenHadir = $totalHariMagang > 0 ? round(($totalHadir / $totalHariMagang) * 100, 1) : 0;

        // 3. Estimasi Uang Saku (UMR DIY per periode)
        $umrPerPeriode = (int) config('magang.uang_saku_per_periode', 2827000);
        $totalUangMagang = 0;
        foreach (config('magang.periods', []) as $p) {
            $workdays = $this->countTotalWorkdays($p['start'], $p['end'], $holidays);
            $hadir = Presensi::where('user_id', $userId)
                ->whereBetween('tanggal', [$p['start'], $p['end']])
                ->where('kehadiran', 'hadir')
                ->count();
            if ($workdays > 0 && $hadir > 0) {
                $totalUangMagang += round(min(1, $hadir / $workdays) * $umrPerPeriode);
            }
        }
        $totalPotensi = count(config('magang.periods', [])) * $umrPerPeriode;

        // 4. Sisa Waktu Magang (Integer Bulat)
        $endDate = Carbon::parse(config('magang.tanggal_selesai'))->startOfDay();
        $sisaHariKalender = (int) max(0, $today->diffInDays($endDate, false));

        return [
            $statHariIni,
            Stat::make('Total Kehadiran', "{$totalHadir} Hari")
                ->description("{$persenHadir}% dari total {$totalHariMagang} hari kerja")
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('primary'),
            Stat::make('Estimasi Uang Magang', 'Rp ' . number_format($totalUangMagang, 0, ',', '.'))
                ->description('UMR DIY: Rp ' . number_format($umrPerPeriode, 0, ',', '.') . ' / periode')
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->color('success'),
            Stat::make('Sisa Waktu Magang', "{$sisaHariKalender} Hari Lagi")
                ->description('Berakhir pada 09 Feb 2027')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color('warning'),
        ];
    }
}
