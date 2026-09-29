<?php

namespace App\Filament\Resources\Presensis;

use App\Filament\Resources\Presensis\Pages\CreatePresensi;
use App\Filament\Resources\Presensis\Pages\EditPresensi;
use App\Filament\Resources\Presensis\Pages\ListPresensis;
use App\Filament\Resources\Presensis\Schemas\PresensiForm;
use App\Filament\Resources\Presensis\Tables\PresensisTable;
use App\Models\Presensi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function getNavigationLabel(): string
    {
        return match (app()->getLocale()) {
            'en' => 'Monev Attendance',
            'ar' => 'حضور المتابعة',
            default => 'Presensi Monev',
        };
    }

    public static function getModelLabel(): string
    {
        return match (app()->getLocale()) {
            'en' => 'Attendance Report',
            'ar' => 'تقرير الحضور',
            default => 'Laporan Presensi',
        };
    }

    public static function getPluralModelLabel(): string
    {
        return match (app()->getLocale()) {
            'en' => 'Attendance Reports',
            'ar' => 'تقارير الحضور',
            default => 'Laporan Presensi',
        };
    }

    protected static ?string $recordTitleAttribute = 'tanggal';

    public static function form(Schema $schema): Schema
    {
        return PresensiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PresensisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPresensis::route('/'),
            'create' => CreatePresensi::route('/create'),
            'edit' => EditPresensi::route('/{record}/edit'),
        ];
    }
}
