<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PeriodeComparisonChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    public function getHeading(): ?string
    {
        return match (app()->getLocale()) {
            'en' => 'Inter-Period Attendance Comparison',
            'ar' => 'مقارنة الحضور بين الفترات',
            default => 'Perbandingan Kehadiran Antar Periode',
        };
    }

    public function getDescription(): ?string
    {
        return match (app()->getLocale()) {
            'en' => 'Attendance consistency across internship monev periods',
            'ar' => 'مدى انتظام الحضور عبر فترات متابعة التدريب',
            default => 'Konsistensi kehadiran per periode monev magang',
        };
    }

    /**
     * Tipe chart: Bar (batang vertikal).
     */
    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Menghasilkan data chart dengan 2 dataset berdampingan:
     *  - Batang Hijau: Hari Hadir di setiap periode
     *  - Batang Biru Transparan: Target Hari Kerja per periode
     */
    protected function getData(): array
    {
        $userId = Auth::id();
        $holidays = config('holidays', []);
        $periods = config('magang.periods', []);
        $locale = app()->getLocale();

        $labels = [];
        $hadirData = [];
        $targetData = [];

        foreach ($periods as $key => $p) {
            $labels[] = match ($locale) {
                'en' => "Period {$key}",
                'ar' => "الفترة {$key}",
                default => $p['name'],
            };

            // Hitung target hari kerja untuk periode ini
            $workdays = $this->countWorkdays($p['start'], $p['end']);
            $targetData[] = $workdays;

            // Hitung hari hadir user dalam periode ini
            $hadir = Presensi::where('user_id', $userId)
                ->whereBetween('tanggal', [$p['start'], $p['end']])
                ->where('kehadiran', 'hadir')
                ->count();
            $hadirData[] = $hadir;
        }

        $lblHadir = match ($locale) {
            'en' => 'Days Attended',
            'ar' => 'أيام الحضور',
            default => 'Hari Hadir',
        };

        $lblTarget = match ($locale) {
            'en' => 'Target Workdays',
            'ar' => 'أيام العمل المستهدفة',
            default => 'Target Hari Kerja',
        };

        return [
            'datasets' => [
                [
                    'label' => $lblHadir,
                    'data' => $hadirData,
                    'backgroundColor' => '#10B981',    // Emerald-500
                    'borderColor' => '#059669',         // Emerald-600
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => $lblTarget,
                    'data' => $targetData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.25)', // Blue-500 transparan
                    'borderColor' => '#3B82F6',                       // Blue-500
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                    'borderDash' => [5, 5],
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Menghitung jumlah hari kerja (Senin-Jumat) di antara dua tanggal,
     * dengan mengecualikan weekend dan hari libur nasional.
     */
    protected function countWorkdays(string $start, string $end): int
    {
        $holidays = config('holidays', []);
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

    /**
     * Opsi Chart.js: sumbu Y mulai dari 0, legend di bawah.
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 16,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 5,
                    ],
                ],
            ],
        ];
    }
}
