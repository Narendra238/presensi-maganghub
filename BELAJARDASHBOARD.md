# 🎓 Panduan Belajar Mandiri: Membangun Dashboard Interaktif Filament PHP dari Nol

Dokumen ini adalah panduan lengkap *step-by-step* untuk memahami arsitektur, alur logika, dan cara membuat dashboard monitoring seperti pada proyek **Presensi Maganghub BDI** secara mandiri tanpa harus bergantung pada AI.

---

## 📌 Daftar Isi
1. [Konsep Dasar & Arsitektur Dashboard Filament](#1-konsep-dasar--arsitektur-dashboard-filament)
2. [Langkah 1: Menyiapkan Single Source of Truth (`config/magang.php`)](#2-langkah-1-menyiapkan-single-source-of-truth-configmagangphp)
3. [Langkah 2: Membuat Widget Kartu Metrik KPI (`PresensiStatsOverview.php`)](#3-langkah-2-membuat-widget-kartu-metrik-kpi-presensistatsoverviewphp)
4. [Langkah 3: Membuat Donut Chart Progress dengan Filter Reaktif (`KehadiranChart.php`)](#4-langkah-3-membuat-donut-chart-progress-dengan-filter-reaktif-kehadiranchartphp)
5. [Langkah 4: Membuat Bar Chart Perbandingan Periode (`PeriodeComparisonChart.php`)](#5-langkah-4-membuat-bar-chart-perbandingan-periode-periodecomparisonchartphp)
6. [Langkah 5: Membuat Tabel Ringkasan Logbook Terbaru (`LatestPresensiWidget.php`)](#6-langkah-5-membuat-tabel-ringkasan-logbook-terbaru-latestpresensiwidgetphp)
7. [Langkah 6: Mengatur Layout di Panel Provider (`AdminPanelProvider.php`)](#7-langkah-6-mengatur-layout-di-panel-provider-adminpanelproviderphp)
8. [Langkah 7: Keamanan & Kustomisasi Profil Pengguna (`User.php`, `EditProfile.php`, `LogoutResponse.php`)](#8-langkah-7-keamanan--kustomisasi-profil-pengguna)
9. [Cheatsheet Perintah Artisan Filament](#9-cheatsheet-perintah-artisan-filament)

---

## 1. Konsep Dasar & Arsitektur Dashboard Filament

Filament menggunakan sistem **Widget** berbasis Livewire untuk membangun dashboard. Setiap komponen di layar adalah sebuah kelas PHP mandiri di dalam folder `app/Filament/Widgets/`.

### Jenis-Jenis Widget Utama:
1. **`StatsOverviewWidget`**: Menampilkan barisan kartu angka/metrik ringkasan (KPI).
2. **`ChartWidget`**: Menampilkan grafik visual (Donut, Pie, Bar, Line, dll) bertenaga *Chart.js*.
3. **`TableWidget`**: Menampilkan tabel data ringkas langsung di dashboard.

### Cara Filament Mengatur Posisi & Grid:
* **`$sort` (Urutan Tampil)**: Angka lebih kecil tampil lebih atas (`$sort = 1` di atas `$sort = 2`).
* **`$columnSpan` (Lebar Kolom)**: 
  * Nilai `'full'` = Membentang penuh sepanjang layar.
  * Default = Mengisi 1 kolom grid (misal pada layar laptop yang terdiri dari 2 kolom, 2 widget akan otomatis berdampingan).

---

## 2. Langkah 1: Menyiapkan Single Source of Truth (`config/magang.php`)

### 🧠 Mengapa langkah ini penting?
Jangan pernah menaruh angka atau rentang tanggal paten (*hardcode*) langsung di dalam widget. Jika tanggal periode atau nominal UMR berubah, kamu cukup mengubahnya di satu file config saja.

### 📝 File: `config/magang.php`
```php
<?php

return [
    // Nominal uang saku per 1 periode magang (mengacu UMR Yogyakarta)
    'uang_saku_per_periode' => (int) env('MAGANG_UANG_SAKU_PER_PERIODE', 2827000),

    // Rentang keseluruhan program
    'tanggal_mulai' => '2026-08-10',
    'tanggal_selesai' => '2027-02-09',

    // Master 6 periode monev magang
    'periods' => [
        '1' => ['name' => 'Periode 1', 'start' => '2026-08-10', 'end' => '2026-09-09', 'label' => '10 Agu 2026 - 09 Sep 2026'],
        '2' => ['name' => 'Periode 2', 'start' => '2026-09-10', 'end' => '2026-10-09', 'label' => '10 Sep 2026 - 09 Okt 2026'],
        '3' => ['name' => 'Periode 3', 'start' => '2026-10-10', 'end' => '2026-11-09', 'label' => '10 Okt 2026 - 09 Nov 2026'],
        '4' => ['name' => 'Periode 4', 'start' => '2026-11-10', 'end' => '2026-12-09', 'label' => '10 Nov 2026 - 09 Des 2026'],
        '5' => ['name' => 'Periode 5', 'start' => '2026-12-10', 'end' => '2027-01-09', 'label' => '10 Des 2026 - 09 Jan 2027'],
        '6' => ['name' => 'Periode 6', 'start' => '2027-01-10', 'end' => '2027-02-09', 'label' => '10 Jan 2027 - 09 Feb 2027'],
    ],
];
```

---

## 3. Langkah 2: Membuat Widget Kartu Metrik KPI (`PresensiStatsOverview.php`)

### 🧠 Logika Pembuatan:
Widget ini menempati posisi teratas (`$sort = 1`) dan membentang penuh dengan 4 kartu informasi cepat:
1. **Presensi Hari Ini**: Cek apakah hari ini weekend, libur nasional, sudah isi presensi, atau belum.
2. **Total Kehadiran**: Menghitung berapa hari sudah hadir dibandingkan target total hari kerja.
3. **Estimasi Uang Magang**: Mengakumulasikan perolehan UMR secara pro-rata berdasarkan hari hadir per periode.
4. **Sisa Waktu Magang**: Menghitung *countdown* sisa hari kalender (dibuat integer bulat).

### 🛠️ Struktur Kodenya:
```php
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
                ->description('Berakhir pada 09 Feb 2027')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color('warning'),
        ];
    }
}
```

---

## 4. Langkah 3: Membuat Donut Chart Progress dengan Filter Reaktif (`KehadiranChart.php`)

### 🧠 Logika Pembuatan:
* **Kesalahan Umum**: Donut chart biasanya hanya merekap database: `[hadir, izin, alpa]`. Jika baru hadir 5 hari dan izin 0, grafiknya langsung 100% penuh hijau (salah kaprah).
* **Solusi**: 100% lingkaran donat harus merepresentasikan **Total Hari Kerja Harus Masuk**.
  * Segmen 1: **Hadir** (Emerald)
  * Segmen 2: **Izin/Sakit** (Amber, jika ada)
  * Segmen 3: **Tanpa Keterangan** (Merah, jika ada)
  * Segmen 4: **Sisa Hari Kerja** (Abu-abu/Slate)
* **Reaktivitas Filter**: Saat user mengganti dropdown dari "Periode 1" ke "Periode 2", Livewire menjalankan hook `updatedFilter()` untuk me-reset data cache dan me-refresh canvas Chart.js.

### 🛠️ Poin Penting Implementasi:
1. **Dropdown Filter**:
   ```php
   public ?string $filter = 'all';

   protected function getFilters(): ?array
   {
       $filters = ['all' => 'Semua Periode (Keseluruhan)'];
       foreach (config('magang.periods', []) as $key => $p) {
           $filters[$key] = "{$p['name']} ({$p['label']})";
       }
       return $filters;
   }
   ```
2. **Hook Reset Livewire**:
   ```php
   public function updatedFilter(): void
   {
       $this->cachedData = null;
       $this->updateChartData();
   }
   ```
3. **Hitung Hari Kerja (Kecualikan Weekend & Libur Nasional)**:
   ```php
   protected function countWorkdays(string $start, string $end): int
   {
       $holidays = config('holidays', []);
       $current = Carbon::parse($start)->startOfDay();
       $end = Carbon::parse($end)->startOfDay();
       $workdays = 0;

       while ($current->lte($end)) {
           $dateStr = $current->format('Y-m-d');
           if (! $current->isSaturday() && ! $current->isSunday() && ! isset($holidays[$dateStr])) {
               $workdays++;
           }
           $current->addDay();
       }
       return $workdays;
   }
   ```

---

## 5. Langkah 4: Membuat Bar Chart Perbandingan Periode (`PeriodeComparisonChart.php`)

### 🧠 Logika Pembuatan:
* Bertujuan untuk melihat **konsistensi kehadiran** anak magang dari bulan ke bulan.
* Menggunakan `$this->getType() = 'bar'`.
* Menggabungkan 2 dataset berdampingan di sumbu Y:
  1. Batang Hijau: **Hari Hadir**
  2. Batang Biru Transparan: **Target Hari Kerja Periode**

```php
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PeriodeComparisonChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected ?string $heading = 'Perbandingan Kehadiran Antar Periode';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        // Loop Periode 1 s/d 6
        // dataset 1: Hari Hadir
        // dataset 2: Target Hari Kerja
    }
}
```

---

## 6. Langkah 5: Membuat Tabel Ringkasan Logbook Terbaru (`LatestPresensiWidget.php`)

### 🧠 Logika Pembuatan:
* Menggunakan turunan kelas `Filament\Widgets\TableWidget`.
* Mengambil 5 data presensi terakhir milik user yang sedang login (`latest('tanggal')->limit(5)`).
* Menampilkan badge warna status kehadiran dan uraian aktivitas yang dipotong ringkas (`limit(45)`).
* Menyediakan tombol aksi `EditAction` yang mengarah langsung ke form pengeditan jurnal.

```php
namespace App\Filament\Widgets;

use App\Models\Presensi;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class LatestPresensiWidget extends TableWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Presensi::query()->where('user_id', Auth::id())->latest('tanggal'))
            ->heading('Logbook & Aktivitas Presensi Terbaru')
            ->paginated([5, 10])
            ->columns([
                TextColumn::make('tanggal')->date('d M Y'),
                TextColumn::make('kehadiran')->badge(),
                TextColumn::make('uraian_aktivitas')->limit(45),
            ])
            ->recordActions([
                EditAction::make()->url(fn ($record) => route('filament.admin.resources.presensis.edit', $record)),
            ]);
    }
}
```

---

## 7. Langkah 6: Mengatur Layout di Panel Provider (`AdminPanelProvider.php`)

### 🧠 Logika Pembuatan:
File `app/Providers/Filament/AdminPanelProvider.php` adalah pusat pengendali antarmuka admin.

```php
// Otomatis mencari semua file widget di app/Filament/Widgets
->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')

// Widget bawaan yang ingin tetap diaktifkan
->widgets([
    AccountWidget::class, // Kotak ucapan selamat datang & profil
])
```

Karena kita sudah memberi `$sort` pada setiap widget:
* `$sort = 1` -> `PresensiStatsOverview` (Paling atas, full width)
* `$sort = 2` -> `KehadiranChart` (Tengah kiri, 1 kolom)
* `$sort = 3` -> `PeriodeComparisonChart` (Tengah kanan, 1 kolom)
* `$sort = 4` -> `LatestPresensiWidget` (Paling bawah, full width)

---

## 8. Langkah 7: Keamanan & Kustomisasi Profil Pengguna

1. **Hak Akses Panel ([User.php](file:///d:/NARENDRA/MaganghubBDI/PRESENSI/presensi-maganghub/app/Models/User.php))**:
   Hanya user dengan role tertentu yang boleh masuk:
   ```php
   public function canAccessPanel(Panel $panel): bool
   {
       return $this->role === 'super_admin';
   }
   ```
2. **Kustomisasi Halaman Profil ([EditProfile.php](file:///d:/NARENDRA/MaganghubBDI/PRESENSI/presensi-maganghub/app/Filament/Pages/Auth/EditProfile.php))**:
   Mengubah label default bahasa Inggris bawaan Filament menjadi bahasa Indonesia (*Nama Lengkap*, *Username / Email Login*, *Kata Sandi Baru*).
3. **Redirect Logout ([LogoutResponse.php](file:///d:/NARENDRA/MaganghubBDI/PRESENSI/presensi-maganghub/app/Http/Responses/LogoutResponse.php))**:
   Mengarahkan user kembali ke halaman utama publik (`/`) saat menekan tombol Logout, bukan ke halaman login admin.

---

## 9. Cheatsheet Perintah Artisan Filament

Untuk projek masa depan, kamu bisa membuat kerangka widget secara otomatis menggunakan terminal:

```bash
# 1. Membuat Widget Kartu Metrik (Stats Overview)
php artisan make:filament-widget NamaWidgetStats --stats-overview

# 2. Membuat Widget Grafik (Chart)
php artisan make:filament-widget NamaWidgetChart --chart

# 3. Membuat Widget Tabel
php artisan make:filament-widget NamaWidgetTabel --table
```

Selamat belajar dan mencoba! File ini bisa kamu jadikan contekan kapan pun ingin membangun dashboard serupa di Laravel Filament. 🚀
