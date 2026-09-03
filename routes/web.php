<?php

use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $sudahPresensi = false;
    if (Auth::check()) {
        $sudahPresensi = Presensi::where('user_id', Auth::id())
            ->whereDate('tanggal', Carbon::today())
            ->exists();
    }
    return view('welcome', compact('sudahPresensi'));
});
