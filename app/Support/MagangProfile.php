<?php

namespace App\Support;

class MagangProfile
{
    /**
     * Dapatkan nama user/peserta magang dari konfigurasi.
     */
    public static function name(): string
    {
        return (string) config('magang.user_name', 'Peserta Magang');
    }

    /**
     * Dapatkan gelar depan (Title Front).
     */
    public static function titleFront(): string
    {
        return trim((string) config('magang.title_front', ''));
    }

    /**
     * Dapatkan gelar belakang (Title Rear).
     */
    public static function titleRear(): string
    {
        return trim((string) config('magang.title_rear', ''));
    }

    /**
     * Format nama lengkap beserta gelar depan dan gelar belakang.
     * Contoh:
     * - Title Front: '', Name: 'Muhammad Narendra Hawari', Title Rear: 'S.Kom'
     *   => 'Muhammad Narendra Hawari, S.Kom'
     * - Title Front: 'Ir.', Name: 'Budi', Title Rear: 'M.Kom'
     *   => 'Ir. Budi, M.Kom'
     */
    public static function fullName(): string
    {
        $front = self::titleFront();
        $name = trim(self::name());
        $rear = self::titleRear();

        $fullName = $name;

        if ($front !== '') {
            $fullName = $front . ' ' . $fullName;
        }

        if ($rear !== '') {
            $fullName .= (str_starts_with($rear, ',') ? $rear : ', ' . $rear);
        }

        return $fullName;
    }
}
