<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class KehadiranChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Progress Kehadiran Magang';

    protected ?string $description = 'Proporsi kehadiran terhadap total hari kerja';

    protected ?string $maxHeight = '300px';

    /**
     * Filter aktif saat ini: 'all' atau key periode ('1', '2', dst.)
     */
    public ?string $filter = 'all';

    /**
     * Cache data internal agar tidak query ulang saat render.
     */
    protected ?array $cachedData = null;

    /**
     * Dropdown filter: Semua Periode + masing-masing Periode 1-6.
     */
    protected function getFilters(): ?array
    {
        $filters = ['all' => 'Semua Periode (Keseluruhan)'];

        foreach (config('magang.periods', []) as $key => $p) {
            $filters[$key] = "{$p['name']} ({$p['label']})";
        }

        return $filters;
    }

    /**
     * Hook Livewire: saat user mengganti dropdown filter,
     * reset cache dan refresh chart canvas.
     */
    public function updatedFilter(): void
    {
        $this->cachedData = null;
        $this->updateChartData();
    }

    /**
     * Tipe chart: Doughnut (donat).
     */
    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * Menghasilkan data chart berformat Chart.js.
     *
     * Logika kunci:
     *  - 100% lingkaran = Total Hari Kerja (bukan total record DB)
     *  - Segmen: Hadir, Izin/Sakit, Tanpa Keterangan, Sisa Hari Kerja
     */
    protected function getData(): array
    {
        $userId = Auth::id();
        $holidays = config('holidays', []);

        // Tentukan rentang tanggal berdasarkan filter
        if ($this->filter === 'all' || $this->filter === null) {
            $startDate = config('magang.tanggal_mulai');
            $endDate = config('magang.tanggal_selesai');
        } else {
            $period = config("magang.periods.{$this->filter}");
            if (! $period) {
                // Fallback ke keseluruhan jika key tidak valid
                $startDate = config('magang.tanggal_mulai');
                $endDate = config('magang.tanggal_selesai');
            } else {
                $startDate = $period['start'];
                $endDate = $period['end'];
            }
        }

        // Hitung total hari kerja dalam rentang (100% lingkaran)
        $totalWorkdays = $this->countWorkdays($startDate, $endDate);

        // Query presensi user dalam rentang
        $query = Presensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        $hadir = (clone $query)->where('kehadiran', 'hadir')->count();
        $izin = (clone $query)->where('kehadiran', 'tidak_hadir_dengan_keterangan')->count();
        $tanpaKeterangan = (clone $query)->where('kehadiran', 'tidak_hadir_tanpa_keterangan')->count();

        // Sisa hari kerja = Total - (Hadir + Izin + Tanpa Keterangan)
        $totalTercatat = $hadir + $izin + $tanpaKeterangan;
        $sisaHariKerja = max(0, $totalWorkdays - $totalTercatat);

        // Bangun label dan data (hanya tampilkan segmen yang nilainya > 0)
        $labels = [];
        $data = [];
        $colors = [];

        if ($hadir > 0) {
            $labels[] = "Hadir ({$hadir})";
            $data[] = $hadir;
            $colors[] = '#10B981'; // Emerald-500
        }

        if ($izin > 0) {
            $labels[] = "Izin/Sakit ({$izin})";
            $data[] = $izin;
            $colors[] = '#F59E0B'; // Amber-500
        }

        if ($tanpaKeterangan > 0) {
            $labels[] = "Tanpa Keterangan ({$tanpaKeterangan})";
            $data[] = $tanpaKeterangan;
            $colors[] = '#EF4444'; // Red-500
        }

        if ($sisaHariKerja > 0) {
            $labels[] = "Sisa Hari Kerja ({$sisaHariKerja})";
            $data[] = $sisaHariKerja;
            $colors[] = '#94A3B8'; // Slate-400
        }

        // Edge case: belum ada data sama sekali
        if (empty($data)) {
            $labels[] = "Belum Ada Data";
            $data[] = 1;
            $colors[] = '#CBD5E1'; // Slate-300
        }

        return [
            'datasets' => [
                [
                    'label' => 'Kehadiran',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#1e293b',   // Slate-800 border
                    'borderWidth' => 2,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Menghitung jumlah hari kerja (Senin-Jumat) di antara dua tanggal,
     * dengan mengecualikan weekend dan hari libur nasional dari config('holidays').
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
     * Opsi tambahan Chart.js untuk cutout (ketebalan donat) dan legend.
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
            'cutout' => '60%',
        ];
    }
}
