<div align="center">

# 📋 Monev Presensi & Jurnal MagangHub
# TIDAK AKAN LUPA LAGI, UANG SAKU AMAN !
### Balai Diklat Industri Yogyakarta (BDI Yogyakarta)

![PHP](https://img.shields.io/badge/PHP-%5E8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-v5-FFA000?style=for-the-badge&logo=filament&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

<p align="center">
  Aplikasi Monitoring & Evaluasi (Monev), Presensi Harian, dan Jurnal / Logbook Kegiatan MagangHub berbasis web untuk peserta magang di Balai Diklat Industri Yogyakarta.
</p>

[Fitur Utama](#-fitur-utama) •
[Tech Stack](#-tech-stack) •
[Prasyarat](#-prasyarat-sistem) •
[Instalasi](#-panduan-instalasi) •
[Konfigurasi](#-konfigurasi) •
[Keamanan](#-keamanan--best-practices) •
[Lisensi](#-lisensi)

---

</div>

## 📌 Tentang Proyek

**Presensi MagangHub BDI** adalah sistem presensi digital dan logbook terpadu yang dirancang khusus untuk mempermudah peserta magang dalam mencatat kehadiran harian, mendokumentasikan hasil belajar, serta memantau progres pelaksanaan magang selama 6 periode evaluasi secara transparan dan akuntabel.

Sistem ini dilengkapi dengan dashboard analitik modern, kalkulator otomatis estimasi tunjangan/uang saku magang berbasis UMR, penghitung hari kerja efektif (mempertimbangkan kalender libur nasional), dan dukungan multi-bahasa.

---

## ✨ Fitur Utama

- 📝 **Pencatatan Presensi & Logbook Harian**:
  - Pilihan status kehadiran: *Hadir*, *WFH*, *Izin*, *Sakit*, *Alpa*, dan *Libur*.
  - Formulir jurnal aktivitas harian: Uraian kegiatan, ringkasan materi/pembelajaran yang didapat, serta kendala dan solusi yang dihadapi.
- 📊 **Dashboard & Statistik Interaktif**:
  - **Status Presensi Hari Ini**: Notifikasi langsung apakah pengguna sudah mengisi presensi hari ini.
  - **Total Kehadiran Kumulatif**: Menampilkan total hari hadir beserta persentase terhadap total hari kerja program magang.
  - **Estimasi Uang Saku / Tunjangan Magang**: Kalkulasi otomatis estimasi tunjangan per periode monev yang dihitung proporsional terhadap kehadiran (mengacu pada standar UMR yang dapat dikonfigurasi).
  - **Countdown Sisa Hari Magang**: Menampilkan sisa hari kalender program magang yang masih berjalan.
- 📈 **Grafik & Visualisasi Data**:
  - *Kehadiran Chart*: Tren riwayat kehadiran per bulan/periode.
  - *Periode Comparison Chart*: Analisis komparatif performa kehadiran peserta sepanjang 6 periode monev magang.
  - *Latest Presensi Widget*: Daftar entri jurnal dan presensi terbaru.
- 🌐 **Dukungan Multi-Bahasa (Trilingual)**:
  - Tersedia dalam **Bahasa Indonesia (ID)**, **English (EN)**, dan **العربية (AR)** dengan penyesuaian tata letak teks LTR dan RTL secara otomatis.
- 🌓 **Tema Gelap & Terang (Dark / Light Mode)**:
  - Tampilan modern, bersih, dan nyaman di mata dengan tombol beralih tema instan.
- 🗓️ **Kalender Libur & Hari Kerja Efektif**:
  - Terintegrasi dengan daftar hari libur nasional dan cuti bersama resmi agar perhitungan hari kerja presisi.
- 🔐 **Autentikasi & Manajemen Profil**:
  - Dibangun di atas Filament PHP v5 yang tangguh, aman, dan mudah disesuaikan.

---

## 🛠️ Tech Stack

- **Backend Framework**: [Laravel 13](https://laravel.com)
- **Admin Panel & Dashboard**: [Filament v5](https://filamentphp.com)
- **Bahasa Pemrograman**: PHP >= 8.3
- **Database**: SQLite (default untuk development) / MySQL / PostgreSQL
- **Frontend & Styling**: Blade, Tailwind CSS, Heroicons
- **Multi-Bahasa**: `prunacatalin/filament-locale-switcher`

---

## ⚙️ Prasyarat Sistem

Sebelum memulai instalasi, pastikan sistem Anda telah terpasang:

- **PHP** >= 8.3
  - Ekstensi yang diperlukan: `bcmath`, `ctype`, `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo`, `pdo_sqlite` (atau `pdo_mysql`), `tokenizer`, `xml`.
- **Composer** >= 2.x ([composer.org](https://getcomposer.org))
- **Node.js** >= 18.x & **NPM** ([nodejs.org](https://nodejs.org))
- **Git** ([git-scm.com](https://git-scm.com))

---

## 🚀 Panduan Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek ini di komputer lokal Anda:

### 1. Clone Repository
```bash
git clone https://github.com/Narendra238/presensi-maganghub.git
cd presensi-maganghub
```

### 2. Install Dependensi PHP
```bash
composer install
```

### 3. Setup File Konfigurasi Environment
Salin file `.env.example` menjadi `.env`:
```bash
# Di Linux / macOS / Git Bash:
cp .env.example .env

# Di Windows PowerShell:
copy .env.example .env
```

### 4. Generate Kunci Aplikasi (App Key)
```bash
php artisan key:generate
```

### 5. Setup Database & Jalankan Migrasi
Secara default, aplikasi ini menggunakan SQLite untuk kemudahan pengembangan.

Buat file database SQLite jika belum ada:
```bash
# Di Windows PowerShell:
if (!(Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite }

# Di Linux / macOS:
touch database/database.sqlite
```

Jalankan migrasi database:
```bash
php artisan migrate
```

> **Catatan**: Jika ingin menggunakan MySQL atau MariaDB, sesuaikan nilai `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di file `.env` Anda sebelum menjalankan `php artisan migrate`.

### 6. Buat Akun Pengguna / Admin Filament
Jalankan perintah interaktif berikut untuk membuat pengguna pertama yang dapat mengakses panel dashboard:
```bash
php artisan make:filament-user
```
Masukkan nama, alamat email, dan password sesuai instruksi di terminal.

### 7. Install & Build Asset Frontend
```bash
npm install
npm run build
```
*(Untuk mode pengembangan aktif dengan auto-reload, jalankan `npm run dev`)*.

### 8. Jalankan Server Aplikasi
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```

Aplikasi sekarang dapat diakses melalui browser:
- **Landing Page Publik**: [http://127.0.0.1:8000](http://127.0.0.1:8000)
- **Panel Dashboard / Admin**: [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

---

## 🔧 Konfigurasi

### 1. Konfigurasi Magang & Uang Saku
Pengaturan parameter magang tersimpan di [config/magang.php](config/magang.php). Anda dapat mengubah nominal uang saku per periode melalui file `.env`:

```env
# Nilai default: Rp 2.827.000 (mengacu UMR DIY)
MAGANG_UANG_SAKU_PER_PERIODE=2827000
```

Periode monev 1 sampai 6 beserta rentang tanggalnya didefinisikan di:
```php
// config/magang.php
'tanggal_mulai' => '2026-08-10',
'tanggal_selesai' => '2027-02-09',
'periods' => [
    '1' => ['name' => 'Periode 1', 'start' => '2026-08-10', 'end' => '2026-09-09', 'label' => '10 Agu 2026 - 09 Sep 2026'],
    // ... periode 2 hingga 6
],
```

### 2. Konfigurasi Hari Libur Nasional
Daftar hari libur nasional dan cuti bersama yang dikecualikan dari hari kerja tersimpan di [config/holidays.php](config/holidays.php):
```php
return [
    '2026-08-17' => 'HUT Kemerdekaan RI ke-81',
    '2026-08-25' => 'Maulid Nabi Muhammad SAW',
    '2026-12-24' => 'Cuti Bersama Hari Raya Natal',
    '2026-12-25' => 'Hari Raya Natal',
];
```

---

## 🛡️ Keamanan & Best Practices (Sebelum Publish / Deploy)

Jika Anda berencana mempublikasikan repositori ini atau melakukan deployment:

1. **Jaga Kerahasiaan File `.env`**:
   - Pastikan file `.env` **tidak pernah di-commit ke Git**. Repositori ini sudah dilengkapi aturan `.gitignore` yang memblokir `.env` dan variasinya.
   - Jangan menyertakan password database, secret key, atau API token asli ke dalam `.env.example`.
2. **Kunci Aplikasi (`APP_KEY`)**:
   - Jangan pernah menyalin `APP_KEY` milik orang lain atau lingkungan produksi ke repositori publik. Selalu gunakan `php artisan key:generate` di setiap lingkungan instalasi.
3. **Database Lokal**:
   - File database lokal SQLite (`database/database.sqlite`) diabaikan oleh `.gitignore` sehingga data absensi pribadi Anda aman dan tidak terunggah ke GitHub.
4. **Environment Produksi**:
   - Saat deploy ke server live / produksi, selalu atur:
     ```env
     APP_ENV=production
     APP_DEBUG=false
     ```

---

## 📂 Struktur Direktori Utama

```text
presensi-maganghub/
├── app/
│   ├── Filament/
│   │   ├── Pages/             # Halaman khusus Filament (Edit Profile, dll)
│   │   ├── Resources/
│   │   │   └── Presensis/     # Resource CRUD Presensi (Form & Tabel)
│   │   └── Widgets/           # Widget statistik, chart perbandingan & kehadiran
│   ├── Models/
│   │   ├── Presensi.php       # Model data kehadiran dan logbook
│   │   └── User.php           # Model pengguna
│   └── Providers/
│       └── Filament/
│           └── AdminPanelProvider.php  # Konfigurasi panel Filament, tema & locale
├── config/
│   ├── holidays.php           # Daftar master hari libur nasional
│   └── magang.php             # Konfigurasi tanggal & periode monev magang
├── database/
│   ├── migrations/            # Skema tabel database
│   └── seeders/               # Data awal database
├── resources/
│   └── views/
│       └── welcome.blade.php  # Halaman landing page publik
└── routes/
    ├── web.php                # Rute web aplikasi
    └── console.php            # Rute artisan command
```

---

## 🤝 Kontribusi

Kontribusi, kritik, dan saran selalu diterima dengan baik!
1. Fork repositori ini
2. Buat branch fitur baru (`git checkout -b fitur/fitur-keren`)
3. Commit perubahan Anda (`git commit -m 'feat: menambahkan fitur keren'`)
4. Push ke branch (`git push origin fitur/fitur-keren`)
5. Ajukan **Pull Request**

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [Lisensi MIT](LICENSE). Anda bebas menggunakannya untuk keperluan pembelajaran, magang, ataupun pengembangan lebih lanjut.

---

<div align="center">
  Dari Muhammad Narendra Hawari Dibuat dengan ❤️ untuk monitoring kegiatan magang di <b>Balai Diklat Industri Yogyakarta</b>.
</div>