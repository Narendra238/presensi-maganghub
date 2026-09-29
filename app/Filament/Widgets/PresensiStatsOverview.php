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

        $locale = app()->getLocale();

        $statAttendanceLabel = match ($locale) {
            'en' => 'Total Attendance',
            'ar' => 'إجمالي الحضور',
            default => 'Total Kehadiran',
        };
        $statAttendanceUnit = match ($locale) {
            'en' => 'Days',
            'ar' => 'أيام',
            default => 'Hari',
        };
        $statAttendanceDesc = match ($locale) {
            'en' => "{$persenHadir}% of total {$totalHariMagang} workdays",
            'ar' => "{$persenHadir}٪ من إجمالي {$totalHariMagang} يوم عمل",
            default => "{$persenHadir}% dari total {$totalHariMagang} hari kerja",
        };

        $statAllowanceLabel = match ($locale) {
            'en' => 'Estimated Allowance',
            'ar' => 'المكافأة التقديرية',
            default => 'Estimasi Uang Magang',
        };
        $statAllowanceDesc = match ($locale) {
            'en' => 'DIY UMR: Rp ' . number_format($umrPerPeriode, 0, ',', '.') . ' / period',
            'ar' => 'الحد الأدنى للأجور DIY: Rp ' . number_format($umrPerPeriode, 0, ',', '.') . ' / فترة',
            default => 'UMR DIY: Rp ' . number_format($umrPerPeriode, 0, ',', '.') . ' / periode',
        };

        $statRemainingLabel = match ($locale) {
            'en' => 'Remaining Time',
            'ar' => 'الوقت المتبقي',
            default => 'Sisa Waktu Magang',
        };
        $statRemainingUnit = match ($locale) {
            'en' => 'Days Left',
            'ar' => 'يوم متبقٍ',
            default => 'Hari Lagi',
        };
        $statRemainingDesc = match ($locale) {
            'en' => 'Ends on ' . Carbon::parse(config('magang.tanggal_selesai'))->locale('en')->translatedFormat('d M Y'),
            'ar' => 'ينتهي في ' . Carbon::parse(config('magang.tanggal_selesai'))->locale('ar')->translatedFormat('d M Y'),
            default => 'Berakhir pada ' . Carbon::parse(config('magang.tanggal_selesai'))->locale('id')->translatedFormat('d M Y'),
        };

        return [
            $statHariIni,
            Stat::make($statAttendanceLabel, "{$totalHadir} {$statAttendanceUnit}")
                ->description($statAttendanceDesc)
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('primary'),

            Stat::make($statAllowanceLabel, 'Rp ' . number_format($totalUangMagang, 0, ',', '.'))
                ->description($statAllowanceDesc)
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->color('success'),

            Stat::make($statRemainingLabel, "{$sisaHariKalender} {$statRemainingUnit}")
                ->description($statRemainingDesc)
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
        $locale = app()->getLocale();
        $statTitle = match ($locale) {
            'en' => "Today's Attendance",
            'ar' => 'حضور اليوم',
            default => 'Presensi Hari Ini',
        };

        // Cek apakah hari ini weekend
        if ($today->isSaturday() || $today->isSunday()) {
            return Stat::make($statTitle, match ($locale) {
                'en' => '🎉 Day Off',
                'ar' => '🎉 عطلة',
                default => '🎉 Hari Libur',
            })
                ->description(match ($locale) {
                    'en' => "It's the weekend — enjoy your rest!",
                    'ar' => 'عطلة نهاية الأسبوع — نتمنى لك وقتاً ممتعاً!',
                    default => 'Hari ini weekend — selamat beristirahat!',
                })
                ->descriptionIcon(Heroicon::OutlinedFaceSmile)
                ->color('gray');
        }

        // Cek apakah hari ini libur nasional
        if (isset($holidays[$todayStr])) {
            return Stat::make($statTitle, match ($locale) {
                'en' => '🎉 Public Holiday',
                'ar' => '🎉 عطلة رسمية',
                default => '🎉 Hari Libur',
            })
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
                'hadir' => match ($locale) {
                    'en' => '✅ Present',
                    'ar' => '✅ حاضر',
                    default => '✅ Sudah Hadir',
                },
                'tidak_hadir_dengan_keterangan' => match ($locale) {
                    'en' => '📋 Excused / Sick',
                    'ar' => '📋 إذن / مرضي',
                    default => '📋 Izin/Sakit',
                },
                'tidak_hadir_tanpa_keterangan' => match ($locale) {
                    'en' => '❌ Unexcused',
                    'ar' => '❌ بدون عذر',
                    default => '❌ Tanpa Keterangan',
                },
                default => match ($locale) {
                    'en' => '✅ Submitted',
                    'ar' => '✅ تم التسجيل',
                    default => '✅ Sudah Diisi',
                },
            };

            $color = match ($presensiHariIni->kehadiran) {
                'hadir' => 'success',
                'tidak_hadir_dengan_keterangan' => 'warning',
                'tidak_hadir_tanpa_keterangan' => 'danger',
                default => 'primary',
            };

            $descRecorded = match ($locale) {
                'en' => 'Attendance on ' . $today->locale('en')->translatedFormat('d M Y') . ' recorded',
                'ar' => 'تم تسجيل حضور تاريخ ' . $today->locale('ar')->translatedFormat('d M Y'),
                default => 'Presensi tanggal ' . $today->locale('id')->translatedFormat('d M Y') . ' tercatat',
            };

            return Stat::make($statTitle, $statusLabel)
                ->description($descRecorded)
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color($color);
        }

        // Belum mengisi presensi
        return Stat::make($statTitle, match ($locale) {
            'en' => '⚠️ Not Yet Submitted',
            'ar' => '⚠️ لم يتم التسجيل بعد',
            default => '⚠️ Belum Diisi',
        })
            ->description(match ($locale) {
                'en' => 'Please submit your attendance today!',
                'ar' => 'يرجى تسجيل الحضور اليوم!',
                default => 'Segera isi presensi hari ini!',
            })
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
