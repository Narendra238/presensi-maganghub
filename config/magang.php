<?php
return [
    // Identitas Profil Pengguna / Peserta Magang di Front-End
    'user_name' => env('USER_NAME', env('MAGANG_USER_NAME', env('NAMA_USER', env('NamaUser', 'Peserta Magang')))),
    'title_front' => env('USER_TITLE_FRONT', env('MAGANG_USER_TITLE_FRONT', env('TITLE_FRONT', env('Title_Front', '')))),
    'title_rear' => env('USER_TITLE_REAR', env('MAGANG_USER_TITLE_REAR', env('TITLE_REAR', env('Title_rear', '')))),

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
