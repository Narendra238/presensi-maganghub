<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Facades\Hash;
use Closure;

class EditProfile extends BaseEditProfile
{
    public static function getLabel(): string
    {
        return 'Profil Saya';
    }

    protected function getNameFormComponent(): Component
    {
        return parent::getNameFormComponent()
            ->label('Nama Lengkap');
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->label('Username / Email Login')
            ->helperText('Alamat email ini digunakan sebagai username utama untuk login ke dashboard admin.');
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->label('Kata Sandi Baru')
            ->helperText('Kosongkan kolom ini jika Anda tidak ingin mengubah kata sandi.')
            ->different('currentPassword')
            ->rule(function () {
                return function (string $attribute, $value, Closure $fail) {
                    if (filled($value) && auth()->check() && Hash::check($value, auth()->user()->password)) {
                        $fail('Kata sandi baru tidak boleh sama dengan kata sandi saat ini.');
                    }
                };
            })
            ->validationMessages([
                'different' => 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.',
            ]);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return parent::getPasswordConfirmationFormComponent()
            ->label('Konfirmasi Kata Sandi Baru');
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return parent::getCurrentPasswordFormComponent()
            ->label('Kata Sandi Saat Ini')
            ->helperText('Wajib diisi sebagai konfirmasi keamanan ketika ingin mengubah kata sandi.');
    }
}
