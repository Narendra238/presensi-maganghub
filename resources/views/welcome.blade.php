<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monev MagangHub - Balai Diklat Industri Yogyakarta</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Smooth transition untuk tema */
        * {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-[#0b0f19] text-slate-800 dark:text-slate-100 min-h-screen flex flex-col font-sans antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="w-full bg-white/80 dark:bg-[#111827]/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <!-- Brand / Logo Info -->
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-500 flex items-center justify-center text-white font-extrabold text-sm shadow-md shadow-blue-500/20">
                    BDI
                </div>
                <div>
                    <span class="font-bold text-sm tracking-tight text-slate-900 dark:text-white block leading-tight">Monev MagangHub</span>
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block leading-tight">Balai Diklat Industri Yogyakarta</span>
                </div>
            </div>

            <!-- Right Actions: Dark Mode & Login Button -->
            <div class="flex items-center space-x-3">
                <!-- Theme Toggle Button -->
                <button id="theme-toggle" type="button" aria-label="Toggle Theme" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    <!-- Sun Icon (Dark Mode Active) -->
                    <svg id="sun-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon Icon (Light Mode Active) -->
                    <svg id="moon-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Login Admin Action -->
                @auth
                    <a href="/admin" class="inline-flex items-center gap-2 px-4 py-2 text-xs md:text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition shadow-sm shadow-blue-500/20 active:scale-[0.98]">
                        <span>Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="/admin/login" class="inline-flex items-center gap-2 px-4 py-2 text-xs md:text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition shadow-sm shadow-blue-500/20 active:scale-[0.98]">
                        <span>Login Admin</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-8 md:py-12">

        <!-- STATUS ALERT PRESENSI HARI INI -->
        <div class="mb-8">
            @if(isset($sudahPresensi) && $sudahPresensi)
                <!-- Kondisi: Sudah Mengisi -->
                <div class="p-4 sm:p-5 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl flex items-start sm:items-center justify-between gap-4 backdrop-blur-sm">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-emerald-900 dark:text-emerald-300">
                                Laporan Presensi Hari Ini Sudah Tersimpan!
                            </h3>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400/90 mt-0.5">
                                Logbook aktivitas tanggal {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} sudah tersimpan rapi di database lokal.
                            </p>
                        </div>
                    </div>
                    <a href="/admin/presensis" class="hidden sm:inline-flex items-center text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-500/20 hover:bg-emerald-500/30 px-3 py-2 rounded-lg transition">
                        Lihat Riwayat &rarr;
                    </a>
                </div>
            @else
                <!-- Kondisi: Belum Mengisi -->
                <div class="p-4 sm:p-5 bg-amber-500/10 border border-amber-500/30 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 backdrop-blur-sm">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0 animate-pulse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-amber-900 dark:text-amber-300">
                                Sudahkah Anda presensi hari ini?
                            </h3>
                            <p class="text-xs text-amber-800 dark:text-amber-400 mt-0.5">
                                Belum ada logbook tercatat untuk hari ini ({{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}). Jangan lupa input backup sebelum pukul 23:59 WIB.
                            </p>
                        </div>
                    </div>
                    <a href="/admin/presensis/create" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition shadow-sm shadow-amber-600/20 whitespace-nowrap self-stretch sm:self-auto text-center">
                        <span>Isi Presensi Sekarang</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </a>
                </div>
            @endif
        </div>

        <!-- PROFILE HERO CARD -->
        <div class="bg-white dark:bg-[#111827] border border-slate-200/80 dark:border-slate-800/90 rounded-3xl p-6 sm:p-8 shadow-sm">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                
                <!-- Foto Profil (Local / Fallback Avatar) -->
                <div class="relative group flex-shrink-0">
                    <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border-2 border-blue-500/30 p-1 shadow-md">
                        <img 
                            src="{{ asset('images/PP.png') }}" 
                            alt="Muhammad Narendra Hawari" 
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Muhammad+Narendra+Hawari&background=2563eb&color=fff&size=256&bold=true';"
                            class="w-full h-full object-cover rounded-xl"
                        >
                    </div>
                    <span class="absolute -bottom-2 -right-2 px-2.5 py-1 bg-emerald-500 text-[10px] font-bold text-white uppercase tracking-wider rounded-full shadow border-2 border-white dark:border-[#111827]">
                        Active
                    </span>
                </div>

                <!-- Bio & Posisi Magang -->
                <div class="flex-1 text-center sm:text-left space-y-3">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-200/50 dark:border-blue-800/50">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        Internal Archive System
                    </div>
                    
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Muhammad Narendra Hawari
                        </h1>
                        <p class="text-sm font-semibold text-blue-600 dark:text-blue-400 mt-0.5">
                            Data Analyst Intern
                        </p>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed max-w-xl">
                        Sistem database lokal pencatatan presensi, uraian aktivitas harian, pembelajaran yang diperoleh, dan mitigasi kendala program <strong class="text-slate-800 dark:text-slate-200">Monev MagangHub</strong> di <strong class="text-slate-800 dark:text-slate-200">Balai Diklat Industri Yogyakarta</strong>.
                    </p>

                    <!-- Info Badges -->
                    <div class="pt-2 flex flex-wrap items-center justify-center sm:justify-start gap-2 text-xs">
                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg font-medium border border-slate-200/60 dark:border-slate-700/60">
                            🏢 BDI Yogyakarta
                        </span>
                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg font-medium border border-slate-200/60 dark:border-slate-700/60">
                            🐘 PostgreSQL
                        </span>
                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg font-medium border border-slate-200/60 dark:border-slate-700/60">
                            ⚡ Laravel & Filament
                        </span>
                    </div>
                </div>
            </div>

            <!-- Mini Summary Bar -->
            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800/80 grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block">Total Logbook Tersimpan</span>
                    <span class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mt-1 block">
                        {{ $totalLaporan ?? 0 }} <span class="text-xs font-normal text-slate-500">Hari</span>
                    </span>
                </div>

                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block">Entri Terakhir</span>
                    <span class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mt-1 block">
                        @if(isset($laporanTerbaru) && $laporanTerbaru)
                            {{ \Carbon\Carbon::parse($laporanTerbaru->tanggal)->translatedFormat('d M Y') }}
                        @else
                            <span class="text-sm font-normal text-slate-400">Belum ada</span>
                        @endif
                    </span>
                </div>

                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-800 col-span-2 sm:col-span-1">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block">Database Status</span>
                    <span class="inline-flex items-center gap-1.5 text-sm sm:text-base font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Connected Local
                    </span>
                </div>
            </div>
        </div>

        <!-- KALENDER PERIODE MONEV MAGANGHUB -->
        <div class="mt-8 bg-white dark:bg-[#111827] border border-slate-200/80 dark:border-slate-800/90 rounded-3xl p-5 sm:p-7 shadow-sm">
            
            <!-- Header Navigasi Periode -->
            <div class="flex items-center justify-between gap-3 mb-6">
                <button 
                    id="prev-period-btn" 
                    type="button" 
                    class="p-2 sm:px-3 sm:py-2 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed flex items-center gap-1.5 text-xs font-semibold active:scale-95"
                    title="Periode Sebelumnya"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    <span class="hidden sm:inline">Periode Lalu</span>
                </button>

                <div class="text-center">
                    <h2 id="period-title" class="text-base sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Periode 1
                    </h2>
                    <p id="period-range" class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                        10 Agu 2026 - 9 Sep 2026
                    </p>
                </div>

                <button 
                    id="next-period-btn" 
                    type="button" 
                    class="p-2 sm:px-3 sm:py-2 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed flex items-center gap-1.5 text-xs font-semibold active:scale-95"
                    title="Periode Selanjutnya"
                >
                    <span class="hidden sm:inline">Periode Berikutnya</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- Wrapper Tabel Kalender -->
            <div class="border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3 sm:p-5 bg-slate-50/60 dark:bg-slate-900/50">
                <!-- Header Hari: Sen, Sel, Rab, Kam, Jum, Sab, Min -->
                <div class="grid grid-cols-7 gap-1 sm:gap-2 text-center pb-3 mb-3 border-b border-slate-200/80 dark:border-slate-800">
                    <div class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">Sen</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">Sel</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">Rab</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">Kam</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">Jum</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-400 dark:text-slate-500">Sab</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-400 dark:text-slate-500">Min</div>
                </div>

                <!-- Grid Tanggal Dinamis -->
                <div id="calendar-grid" class="grid grid-cols-7 gap-1 sm:gap-2">
                    <!-- Tanggal digenerate otomatis oleh JavaScript -->
                </div>
            </div>

            <!-- Keterangan & Ringkasan -->
            <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <!-- Legenda Status -->
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 sm:gap-5 text-slate-600 dark:text-slate-400">
                    <div class="flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span>Terisi</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-slate-400 dark:bg-slate-600 rounded-[1px]"></span>
                        <span>Libur / Weekend</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-blue-500 rotate-45 rounded-[1px]"></span>
                        <span>Hari Ini</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 border border-slate-300 dark:border-slate-700 rounded-full"></span>
                        <span>Masa Depan</span>
                    </div>
                </div>

                <!-- Statistik Periode Aktif -->
                <div id="period-stats" class="text-xs font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/60 px-3 py-1.5 rounded-xl border border-slate-200/50 dark:border-slate-700/50 whitespace-nowrap">
                    Memuat data...
                </div>
            </div>
        </div>

        <!-- Shortcut & Rules Card -->
        <div class="mt-6 bg-slate-100/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-xs text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-center sm:text-left">
                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Validasi aktif: Tanggal unik (anti-duplikat) & Sabtu/Minggu libur presensi.</span>
            </div>
            <a href="/admin/presensis" class="text-blue-600 dark:text-blue-400 hover:underline font-medium whitespace-nowrap">
                Buka Tabel Lengkap &rarr;
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-200 dark:border-slate-800 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
        &copy; {{ date('Y') }} Muhammad Narendra Hawari &bull; Monev MagangHub BDI Yogyakarta Tracker
    </footer>

    <!-- Script Theme Toggle & Kalender Periode -->
    <script>
        // --- 1. Theme Toggle Dark Mode ---
        const themeToggleBtn = document.getElementById('theme-toggle');
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');

        function updateIcons() {
            if (document.documentElement.classList.contains('dark')) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                sunIcon.classList.hidden = true;
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
        }

        if (localStorage.getItem('color-theme') === 'light' || (!('color-theme' in localStorage) && !window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
        updateIcons();

        themeToggleBtn.addEventListener('click', function() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
            updateIcons();
        });

        // --- 2. Kalender Periode MagangHub ---
        const tanggalTerisiList = @json($tanggalTerisi ?? []);
        const hariLiburList = @json($hariLibur ?? []);

        // 6 Periode Magang
        const periods = [
            { id: 1, name: 'Periode 1', start: '2026-08-10', end: '2026-09-09', label: '10 Agu 2026 - 9 Sep 2026' },
            { id: 2, name: 'Periode 2', start: '2026-09-10', end: '2026-10-09', label: '10 Sep 2026 - 9 Okt 2026' },
            { id: 3, name: 'Periode 3', start: '2026-10-10', end: '2026-11-09', label: '10 Okt 2026 - 9 Nov 2026' },
            { id: 4, name: 'Periode 4', start: '2026-11-10', end: '2026-12-09', label: '10 Nov 2026 - 9 Des 2026' },
            { id: 5, name: 'Periode 5', start: '2026-12-10', end: '2027-01-09', label: '10 Des 2026 - 9 Jan 2027' },
            { id: 6, name: 'Periode 6', start: '2027-01-10', end: '2027-02-09', label: '10 Jan 2027 - 9 Feb 2027' },
        ];

        // Format tanggal hari ini (YYYY-MM-DD)
        const now = new Date();
        const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;

        // Tentukan periode aktif awal berdasarkan tanggal hari ini
        let currentPeriodIndex = periods.findIndex(p => todayStr >= p.start && todayStr <= p.end);
        if (currentPeriodIndex === -1) {
            currentPeriodIndex = 0; // Default ke Periode 1 jika belum atau sudah lewat
        }

        const periodTitleEl = document.getElementById('period-title');
        const periodRangeEl = document.getElementById('period-range');
        const prevBtn = document.getElementById('prev-period-btn');
        const nextBtn = document.getElementById('next-period-btn');
        const calendarGridEl = document.getElementById('calendar-grid');
        const periodStatsEl = document.getElementById('period-stats');

        function renderCalendar(index) {
            const period = periods[index];
            periodTitleEl.textContent = period.name;
            periodRangeEl.textContent = period.label;

            prevBtn.disabled = (index === 0);
            nextBtn.disabled = (index === periods.length - 1);

            calendarGridEl.innerHTML = '';

            const startDate = new Date(period.start + 'T00:00:00');
            const endDate = new Date(period.end + 'T00:00:00');

            // Offset hari awal: 0 = Senin, 6 = Minggu
            const startDay = (startDate.getDay() + 6) % 7;

            // Kotak kosong di awal baris pertama (padding)
            for (let i = 0; i < startDay; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'h-12 sm:h-14 rounded-xl opacity-0 pointer-events-none';
                calendarGridEl.appendChild(emptyCell);
            }

            let totalWorkdays = 0;
            let filledWorkdays = 0;
            let current = new Date(startDate);
            let totalCells = startDay;

            while (current <= endDate) {
                const dayNum = current.getDate();
                const yyyy = current.getFullYear();
                const mm = String(current.getMonth() + 1).padStart(2, '0');
                const dd = String(current.getDate()).padStart(2, '0');
                const dateStr = `${yyyy}-${mm}-${dd}`;

                const dayOfWeek = (current.getDay() + 6) % 7; // 0=Sen, ..., 5=Sab, 6=Min
                const isWeekend = (dayOfWeek === 5 || dayOfWeek === 6);
                const isHoliday = Boolean(hariLiburList[dateStr]);
                const holidayName = isHoliday ? hariLiburList[dateStr] : '';
                const isToday = (dateStr === todayStr);
                const isPast = (dateStr < todayStr);
                const isFuture = (dateStr > todayStr);
                const isFilled = tanggalTerisiList.includes(dateStr);

                if (!isWeekend && !isHoliday) {
                    totalWorkdays++;
                    if (isFilled) {
                        filledWorkdays++;
                    }
                }

                const cell = document.createElement('div');
                let cellClasses = 'h-12 sm:h-14 rounded-xl flex flex-col items-center justify-between py-1.5 sm:py-2 px-1 text-center transition border ';
                let indicatorHtml = '';

                if (isToday) {
                    cellClasses += 'border-blue-500 bg-blue-50/80 dark:bg-blue-950/40 shadow-sm ring-2 ring-blue-500/20 ';
                } else if (isHoliday) {
                    cellClasses += 'border-rose-200/60 dark:border-rose-900/40 bg-rose-50/40 dark:bg-rose-950/20 hover:bg-rose-100/60 dark:hover:bg-rose-950/40 ';
                } else {
                    cellClasses += 'border-slate-200/60 dark:border-slate-800/80 bg-white dark:bg-slate-800/40 hover:bg-slate-100/80 dark:hover:bg-slate-800/80 ';
                }

                let numClasses = 'text-xs sm:text-sm font-semibold leading-none ';
                if (isToday) {
                    numClasses += 'text-blue-600 dark:text-blue-400 font-extrabold ';
                } else if (isHoliday) {
                    numClasses += 'text-rose-500 dark:text-rose-400 font-bold ';
                } else if (isWeekend) {
                    numClasses += 'text-slate-400 dark:text-slate-500 ';
                } else {
                    numClasses += 'text-slate-800 dark:text-slate-200 ';
                }

                // Tentukan Ikon Status
                if (isFilled) {
                    indicatorHtml = `
                        <div class="flex items-center justify-center text-emerald-500 dark:text-emerald-400">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    `;
                } else if (isHoliday) {
                    indicatorHtml = `<span class="inline-block w-1.5 h-1.5 bg-rose-500 dark:bg-rose-400 rounded-[1px]" title="Libur: ${holidayName}"></span>`;
                } else if (isWeekend) {
                    indicatorHtml = `<span class="inline-block w-1.5 h-1.5 bg-slate-400 dark:bg-slate-600 rounded-[1px]" title="Libur Weekend"></span>`;
                } else if (isToday) {
                    indicatorHtml = `<span class="inline-block w-2 h-2 bg-blue-500 rotate-45 rounded-[1px] shadow-sm animate-pulse" title="Hari Ini"></span>`;
                } else if (isPast) {
                    indicatorHtml = `<span class="inline-block w-1.5 h-1.5 bg-slate-400 dark:bg-slate-600 rounded-[1px]" title="Tidak Ada Presensi"></span>`;
                } else {
                    indicatorHtml = `<span class="inline-block w-1.5 h-1.5 border border-slate-300 dark:border-slate-700 rounded-full" title="Masa Depan"></span>`;
                }

                cell.className = cellClasses;
                cell.title = `Tanggal: ${dateStr}${isHoliday ? ` (Libur: ${holidayName})` : (isWeekend ? ' (Weekend)' : (isFilled ? ' (Presensi Terisi)' : (isToday ? ' (Hari Ini)' : '')))}`;
                cell.innerHTML = `
                    <span class="${numClasses}">${dayNum}</span>
                    <div class="h-4 flex items-center justify-center">
                        ${indicatorHtml}
                    </div>
                `;

                calendarGridEl.appendChild(cell);
                totalCells++;

                // Lanjut ke hari berikutnya
                current.setDate(current.getDate() + 1);
            }

            // Kotak kosong penutup di baris terakhir jika tidak pas 7 kolom
            const remainder = totalCells % 7;
            if (remainder !== 0) {
                for (let i = 0; i < (7 - remainder); i++) {
                    const emptyCell = document.createElement('div');
                    emptyCell.className = 'h-12 sm:h-14 rounded-xl opacity-0 pointer-events-none';
                    calendarGridEl.appendChild(emptyCell);
                }
            }

            // Tampilkan Ringkasan Statistik
            const percentage = totalWorkdays > 0 ? Math.round((filledWorkdays / totalWorkdays) * 100) : 0;
            periodStatsEl.innerHTML = `Presensi: <strong class="text-slate-800 dark:text-slate-100">${filledWorkdays}</strong> / ${totalWorkdays} Hari Kerja (<span class="text-emerald-600 dark:text-emerald-400 font-bold">${percentage}%</span>)`;
        }

        prevBtn.addEventListener('click', () => {
            if (currentPeriodIndex > 0) {
                currentPeriodIndex--;
                renderCalendar(currentPeriodIndex);
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentPeriodIndex < periods.length - 1) {
                currentPeriodIndex++;
                renderCalendar(currentPeriodIndex);
            }
        });

        // Jalankan render awal
        renderCalendar(currentPeriodIndex);
    </script>
</body>
</html>