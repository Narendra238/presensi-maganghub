<?php

use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $sudahPresensi = false;
    $totalLaporan = 0;
    $laporanTerbaru = null;
    if (Auth::check()) {
        // Jika user sedang login (tampilkan data milik user tersebut)
        $userId = Auth::id();
        $sudahPresensi = Presensi::where('user_id', $userId)
            ->whereDate('tanggal', Carbon::today())
            ->exists();
        $totalLaporan = Presensi::where('user_id', $userId)->count();
        $laporanTerbaru = Presensi::where('user_id', $userId)->latest('tanggal')->first();
    } else {
        // Jika belum login / publik (tampilkan total data yang ada di database)
        $totalLaporan = Presensi::count();
        $laporanTerbaru = Presensi::latest('tanggal')->first();
    }
    return view('welcome', compact('sudahPresensi', 'totalLaporan', 'laporanTerbaru'));
});
