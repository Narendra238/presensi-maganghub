<?php

namespace App\Filament\Resources\Presensis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
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
                            if ($date->isSaturday() || $date->isSunday()) {
                                $fail('Hari Sabtu dan Minggu adalah hari libur. Tidak perlu mengisi presensi.');
                            }
                        },
                    ])
                    ->validationMessages([
                        'unique' => 'Presensi pada tanggal ini sudah pernah diisi! Tidak bisa memasukkan tanggal yang sama.',
                    ])
                    ->columnSpanFull(),

                Textarea::make('uraian_aktivitas')
                    ->label('Uraian Aktivitas')
                    ->placeholder('Tuliskan rincian kegiatan magang hari ini...')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('pembelajaran')
                    ->label('Pembelajaran yang Diperoleh')
                    ->placeholder('Apa ilmu atau insight baru yang didapatkan?')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('kendala')
                    ->label('Kendala yang Dialami')
                    ->placeholder('Kendala teknis / non-teknis (kosongkan jika tidak ada)...')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
