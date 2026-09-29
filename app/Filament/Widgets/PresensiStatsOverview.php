<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

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
                ->description('Berakhir pada ' . Carbon::parse(config('magang.tanggal_selesai'))->translatedFormat('d M Y'))
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color('warning'),
        ];
    }

    /**
     * Membangun kartu status presensi hari ini.
     * Mengecek apakah weekend, libur nasional, sudah presensi, atau belum.
     */
    protected function buildStatusHariIniStat(int $userId, Carbon $today, string $todayStr, array $holidays): Stat
    {
        // Cek apakah hari ini weekend
        if ($today->isSaturday() || $today->isSunday()) {
            return Stat::make('Presensi Hari Ini', '🎉 Hari Libur')
                ->description('Hari ini weekend — selamat beristirahat!')
                ->descriptionIcon(Heroicon::OutlinedFaceSmile)
                ->color('gray');
        }

        // Cek apakah hari ini libur nasional
        if (isset($holidays[$todayStr])) {
            return Stat::make('Presensi Hari Ini', '🎉 Hari Libur')
                ->description($holidays[$todayStr])
                ->descriptionIcon(Heroicon::OutlinedFaceSmile)
                ->color('gray');
        }

        // Cek apakah sudah mengisi presensi hari ini
        $presensiHariIni = Presensi::where('user_id', $userId)
            ->where('tanggal', $todayStr)
            ->first();

        if ($presensiHariIni) {
            $statusLabel = match ($presensiHariIni->kehadiran) {
                'hadir' => '✅ Sudah Hadir',
                'tidak_hadir_dengan_keterangan' => '📋 Izin/Sakit',
                'tidak_hadir_tanpa_keterangan' => '❌ Tanpa Keterangan',
                default => '✅ Sudah Diisi',
            };

            $color = match ($presensiHariIni->kehadiran) {
                'hadir' => 'success',
                'tidak_hadir_dengan_keterangan' => 'warning',
                'tidak_hadir_tanpa_keterangan' => 'danger',
                default => 'primary',
            };

            return Stat::make('Presensi Hari Ini', $statusLabel)
                ->description('Presensi tanggal ' . $today->translatedFormat('d M Y') . ' tercatat')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color($color);
        }

        // Belum mengisi presensi
        return Stat::make('Presensi Hari Ini', '⚠️ Belum Diisi')
            ->description('Segera isi presensi hari ini!')
            ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
            ->color('danger');
    }

    /**
     * Menghitung jumlah hari kerja (Senin-Jumat, kecuali libur nasional)
     * antara dua tanggal (inklusif).
     */
    protected function countTotalWorkdays(string $start, string $end, array $holidays): int
    {
        $current = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->startOfDay();
        $workdays = 0;

        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            if (! $current->isSaturday() && ! $current->isSunday() && ! isset($holidays[$dateStr])) {
                $workdays++;
            }
            $current->addDay();
        }

        return $workdays;
    }
}
