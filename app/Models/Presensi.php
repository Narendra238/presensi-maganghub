<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'uraian_aktivitas',
        'pembelajaran',
        'kendala',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
