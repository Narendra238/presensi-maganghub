<?php

namespace App\Filament\Resources\Presensis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PresensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn () => Auth::id())
                    ->required(),

                DatePicker::make('tanggal')
                    ->label('Tanggal Kegiatan')
                    ->default(now())
                    ->required()
                    ->unique(
                        table: 'presensis',
                        column: 'tanggal',
                        ignorable: fn ($record) => $record,
                        modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule) => $rule->where('user_id', Auth::id()),
                    )
                    ->rules([
                        fn () => function (string $attribute, $value, \Closure $fail) {
                            if (! $value) {
                                return;
                            }
                            $date = \Carbon\Carbon::parse($value);
                            $dateStr = $date->format('Y-m-d');
                            $holidays = config('holidays', []);

                            // 1. Cek Weekend (Sabtu & Minggu)
                            if ($date->isSaturday() || $date->isSunday()) {
                                $fail('Hari Sabtu dan Minggu adalah hari libur. Tidak perlu mengisi presensi.');
                                return;
                            }

                            // 2. Cek Hari Libur Nasional & Cuti Bersama
                            if (isset($holidays[$dateStr])) {
                                $fail("Tanggal ini adalah hari libur ({$holidays[$dateStr]}). Tidak perlu mengisi presensi.");
                            }
                        },
                    ])
                    ->validationMessages([
                        'unique' => 'Presensi pada tanggal ini sudah pernah diisi! Tidak bisa memasukkan tanggal yang sama.',
                    ])
                    ->columnSpanFull(),

                // === Field Kehadiran (3 kondisi) ===
                Select::make('kehadiran')
                    ->label('Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'tidak_hadir_dengan_keterangan' => 'Tidak Hadir Dengan Keterangan',
                        'tidak_hadir_tanpa_keterangan' => 'Tidak Hadir Tanpa Keterangan',
                    ])
                    ->default('hadir')
                    ->required()
                    ->live()
                    ->columnSpanFull(),

                // === Form untuk HADIR: 3 field ===
                Textarea::make('uraian_aktivitas')
                    ->label('Uraian Aktivitas')
                    ->placeholder('Tuliskan rincian kegiatan magang hari ini...')
                    ->rows(4)
                    ->required(fn (Get $get): bool => $get('kehadiran') === 'hadir')
                    ->visible(fn (Get $get): bool => $get('kehadiran') === 'hadir')
                    ->columnSpanFull(),

                Textarea::make('pembelajaran')
                    ->label('Pembelajaran yang Diperoleh')
                    ->placeholder('Apa ilmu atau insight baru yang didapatkan?')
                    ->rows(4)
                    ->required(fn (Get $get): bool => $get('kehadiran') === 'hadir')
                    ->visible(fn (Get $get): bool => $get('kehadiran') === 'hadir')
                    ->columnSpanFull(),

                Textarea::make('kendala')
                    ->label('Kendala yang Dialami')
                    ->placeholder('Kendala teknis / non-teknis (kosongkan jika tidak ada)...')
                    ->rows(3)
                    ->visible(fn (Get $get): bool => $get('kehadiran') === 'hadir')
                    ->columnSpanFull(),

                // === Form untuk TIDAK HADIR DENGAN KETERANGAN: 1 field ===
                Textarea::make('keterangan')
                    ->label('Alasan / Keterangan Tidak Hadir')
                    ->placeholder('Tuliskan alasan tidak hadir (contoh: sakit, izin keluarga, dll)...')
                    ->rows(4)
                    ->required(fn (Get $get): bool => $get('kehadiran') === 'tidak_hadir_dengan_keterangan')
                    ->visible(fn (Get $get): bool => $get('kehadiran') === 'tidak_hadir_dengan_keterangan')
                    ->columnSpanFull(),

                // === TIDAK HADIR TANPA KETERANGAN: tidak ada form tambahan ===
            ]);
    }
}

