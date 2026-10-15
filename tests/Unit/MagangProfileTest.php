<?php

namespace Tests\Unit;

use App\Support\MagangProfile;
use Tests\TestCase;

class MagangProfileTest extends TestCase
{
    /**
     * Test mendapatkan nama dasar dari config/env.
     */
    public function test_it_returns_user_name_from_config(): void
    {
        config(['magang.user_name' => 'Peserta Magang']);

        $this->assertEquals('Peserta Magang', MagangProfile::name());
    }

    /**
     * Test mendapatkan gelar depan (Title Front) dari config/env.
     */
    public function test_it_returns_title_front_from_config(): void
    {
        config(['magang.title_front' => 'Ir.']);

        $this->assertEquals('Ir.', MagangProfile::titleFront());
    }

    /**
     * Test mendapatkan gelar belakang (Title Rear) dari config/env.
     */
    public function test_it_returns_title_rear_from_config(): void
    {
        config(['magang.title_rear' => 'S.Kom']);

        $this->assertEquals('S.Kom', MagangProfile::titleRear());
    }

    /**
     * Test format nama lengkap dengan gelar belakang saja.
     * Contoh: Muhammad Narendra Hawari, S.Kom
     */
    public function test_it_formats_full_name_with_rear_title(): void
    {
        config([
            'magang.title_front' => '',
            'magang.user_name' => 'Muhammad Narendra Hawari',
            'magang.title_rear' => 'S.Kom',
        ]);

        $this->assertEquals('Muhammad Narendra Hawari, S.Kom', MagangProfile::fullName());
    }

    /**
     * Test format nama lengkap dengan gelar depan dan gelar belakang.
     * Contoh: Ir. John Doe, M.T.
     */
    public function test_it_formats_full_name_with_front_and_rear_titles(): void
    {
        config([
            'magang.title_front' => 'Ir.',
            'magang.user_name' => 'Maulana Habibie',
            'magang.title_rear' => 'M.T.',
        ]);

        $this->assertEquals('Ir. Maulana Habibie, M.T.', MagangProfile::fullName());
    }

    /**
     * Test format nama tanpa gelar (gelar depan & belakang kosong).
     * Contoh: Parjo
     */
    public function test_it_formats_full_name_without_titles(): void
    {
        config([
            'magang.title_front' => '',
            'magang.user_name' => 'Parjo',
            'magang.title_rear' => '',
        ]);

        $this->assertEquals('Parjo', MagangProfile::fullName());
    }

    /**
     * Test penanganan jika gelar belakang sudah diawali tanda koma (mencegah koma ganda).
     */
    public function test_it_prevents_duplicate_commas_in_rear_title(): void
    {
        config([
            'magang.title_front' => '',
            'magang.user_name' => 'Peserta Magang',
            'magang.title_rear' => ', S.Kom',
        ]);

        $this->assertEquals('Peserta Magang, S.Kom', MagangProfile::fullName());
    }
}
