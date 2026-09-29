<?php

declare(strict_types=1);

/**
 * Defaults consumed by LocaleSwitchPlugin when a panel does not override the
 * matching fluent setter. Publish with:
 *
 *   php artisan vendor:publish --tag=filament-locale-switcher-config
 *
 * Per-panel calls (->locales(), ->labels(), …) always win over these values.
 */
return [

    /*
    |---------------------------------------------------------------------
    | Whitelist of locales the switcher will offer.
    | Any incoming value outside this list is rejected on resolve.
    |---------------------------------------------------------------------
    */
    'locales' => ['id', 'en', 'ar'],

    /*
    |---------------------------------------------------------------------
    | Display names — locale code → human label shown in the dropdown.
    | Missing entries fall back to strtoupper($code).
    |---------------------------------------------------------------------
    */
    'labels' => [
        'id' => 'Indonesia',
        'en' => 'English',
        'ar' => 'العربية',
    ],

    /*
    |---------------------------------------------------------------------
    | Optional emoji / icon per locale. Setting this also flips
    | `show_flags` on and renders the flag in the topbar button.
    |---------------------------------------------------------------------
    */
    'flags' => [
        'id' => '🇮🇩',
        'en' => '🇬🇧',
        'ar' => '🇸🇦',
    ],

    /*
    |---------------------------------------------------------------------
    | Where to persist the chosen locale:
    |   session — survives until session expires (default; guest-friendly)
    |   user    — writes to the auth user's column (requires migration)
    |   cookie  — 1-year cookie; survives logout, not synced cross-device
    |---------------------------------------------------------------------
    */
    'persist' => 'cookie',

    /*
    |---------------------------------------------------------------------
    | Column on the User model when persist=user. The package migration
    | adds `users.locale` by default.
    |---------------------------------------------------------------------
    */
    'user_column' => 'locale',

    /*
    |---------------------------------------------------------------------
    | UI placement: topbar | user-menu | both
    |---------------------------------------------------------------------
    */
    'placement' => 'topbar',

    /*
    |---------------------------------------------------------------------
    | Distance between the switcher and the right edge of the topbar
    | (i.e. the gap to leave for the user-menu / avatar). Any CSS length
    | value: '4.5rem', '60px', 'calc(2rem + 16px)', etc.
    |---------------------------------------------------------------------
    */
    'topbar_offset' => '4.5rem',

];
