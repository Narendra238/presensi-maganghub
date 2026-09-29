<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PeriodeComparisonChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Perbandingan Kehadiran Antar Periode';

    protected ?string $description = 'Konsistensi kehadiran per periode monev magang';

    protected ?string $maxHeight = '300px';

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

        $labels = [];
        $hadirData = [];
        $targetData = [];

        foreach ($periods as $p) {
            $labels[] = $p['name']; // "Periode 1", "Periode 2", dst.

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

        return [
            'datasets' => [
                [
                    'label' => 'Hari Hadir',
                    'data' => $hadirData,
                    'backgroundColor' => '#10B981',    // Emerald-500
                    'borderColor' => '#059669',         // Emerald-600
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Target Hari Kerja',
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
