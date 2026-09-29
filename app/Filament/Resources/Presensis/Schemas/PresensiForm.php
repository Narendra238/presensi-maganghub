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
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Activity Date',
                        'ar' => 'تاريخ النشاط',
                        default => 'Tanggal Kegiatan',
                    })
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
                            $locale = app()->getLocale();

                            // 1. Cek Weekend (Sabtu & Minggu)
                            if ($date->isSaturday() || $date->isSunday()) {
                                $fail(match ($locale) {
                                    'en' => 'Saturdays and Sundays are days off. Attendance is not required.',
                                    'ar' => 'يومي السبت والأحد عطلة. لا يتطلب تسجيل الحضور.',
                                    default => 'Hari Sabtu dan Minggu adalah hari libur. Tidak perlu mengisi presensi.',
                                });
                                return;
                            }

                            // 2. Cek Hari Libur Nasional & Cuti Bersama
                            if (isset($holidays[$dateStr])) {
                                $fail(match ($locale) {
                                    'en' => "This date is a holiday ({$holidays[$dateStr]}). Attendance is not required.",
                                    'ar' => "هذا التاريخ عطلة ({$holidays[$dateStr]}). لا يتطلب تسجيل الحضور.",
                                    default => "Tanggal ini adalah hari libur ({$holidays[$dateStr]}). Tidak perlu mengisi presensi.",
                                });
                            }
                        },
                    ])
                    ->validationMessages([
                        'unique' => fn () => match (app()->getLocale()) {
                            'en' => 'Attendance for this date has already been submitted!',
                            'ar' => 'تم تسجيل الحضور لهذا التاريخ مسبقاً!',
                            default => 'Presensi pada tanggal ini sudah pernah diisi! Tidak bisa memasukkan tanggal yang sama.',
                        },
                    ])
                    ->columnSpanFull(),

                // === Field Kehadiran (3 kondisi) ===
                Select::make('kehadiran')
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Attendance Status',
                        'ar' => 'حالة الحضور',
                        default => 'Kehadiran',
                    })
                    ->options(fn () => match (app()->getLocale()) {
                        'en' => [
                            'hadir' => 'Present',
                            'tidak_hadir_dengan_keterangan' => 'Excused Absence (Sick / Leave)',
                            'tidak_hadir_tanpa_keterangan' => 'Unexcused Absence',
                        ],
                        'ar' => [
                            'hadir' => 'حاضر',
                            'tidak_hadir_dengan_keterangan' => 'غياب بعذر (مرضي / إجازة)',
                            'tidak_hadir_tanpa_keterangan' => 'غياب بدون عذر',
                        ],
                        default => [
                            'hadir' => 'Hadir',
                            'tidak_hadir_dengan_keterangan' => 'Tidak Hadir Dengan Keterangan',
                            'tidak_hadir_tanpa_keterangan' => 'Tidak Hadir Tanpa Keterangan',
                        ],
                    })
                    ->default('hadir')
                    ->required()
                    ->live()
                    ->columnSpanFull(),

                // === Form untuk HADIR: 3 field ===
                Textarea::make('uraian_aktivitas')
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Activity Description',
                        'ar' => 'وصف النشاط',
                        default => 'Uraian Aktivitas',
                    })
                    ->placeholder(fn () => match (app()->getLocale()) {
                        'en' => 'Describe internship activities for today...',
                        'ar' => 'اكتب تفاصيل مهام التدريب لهذا اليوم...',
                        default => 'Tuliskan rincian kegiatan magang hari ini...',
                    })
                    ->rows(4)
                    ->required(fn (Get $get): bool => $get('kehadiran') === 'hadir')
                    ->visible(fn (Get $get): bool => $get('kehadiran') === 'hadir')
                    ->columnSpanFull(),

                Textarea::make('pembelajaran')
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Learnings Acquired',
                        'ar' => 'الدروس المستفادة',
                        default => 'Pembelajaran yang Diperoleh',
                    })
                    ->placeholder(fn () => match (app()->getLocale()) {
                        'en' => 'What new knowledge or insights did you gain?',
                        'ar' => 'ما المعرفة أو الخبرة الجديدة التي اكتسبتها؟',
                        default => 'Apa ilmu atau insight baru yang didapatkan?',
                    })
                    ->rows(4)
                    ->required(fn (Get $get): bool => $get('kehadiran') === 'hadir')
                    ->visible(fn (Get $get): bool => $get('kehadiran') === 'hadir')
                    ->columnSpanFull(),

                Textarea::make('kendala')
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Obstacles / Challenges',
                        'ar' => 'المعوقات والتحديات',
                        default => 'Kendala yang Dialami',
                    })
                    ->placeholder(fn () => match (app()->getLocale()) {
                        'en' => 'Technical / non-technical obstacles (leave blank if none)...',
                        'ar' => 'معوقات تقنية أو غير تقنية (اتركه فارغاً إن لم يوجد)...',
                        default => 'Kendala teknis / non-teknis (kosongkan jika tidak ada)...'
                    })
                    ->rows(3)
                    ->visible(fn (Get $get): bool => $get('kehadiran') === 'hadir')
                    ->columnSpanFull(),

                // === Form untuk TIDAK HADIR DENGAN KETERANGAN: 1 field ===
                Textarea::make('keterangan')
                    ->label(fn () => match (app()->getLocale()) {
                        'en' => 'Reason for Absence',
                        'ar' => 'سبب الغياب',
                        default => 'Alasan / Keterangan Tidak Hadir',
                    })
                    ->placeholder(fn () => match (app()->getLocale()) {
                        'en' => 'State reason for absence (e.g. sick, family matter, etc)...',
                        'ar' => 'اكتب سبب الغياب (مثال: مرضي، ظرف عائلي، إلخ)...',
                        default => 'Tuliskan alasan tidak hadir (contoh: sakit, izin keluarga, dll)...',
                    })
                    ->rows(4)
                    ->required(fn (Get $get): bool => $get('kehadiran') === 'tidak_hadir_dengan_keterangan')
                    ->visible(fn (Get $get): bool => $get('kehadiran') === 'tidak_hadir_dengan_keterangan')
                    ->columnSpanFull(),

                // === TIDAK HADIR TANPA KETERANGAN: tidak ada form tambahan ===
            ]);
    }
}

