<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Theme mode
    |--------------------------------------------------------------------------
    |
    | This option determines how the theme will be set for the application.
    | 'user' stores the selected theme on each user (requires the migration).
    | 'global' stores a single theme for all users in the cache.
    |
    | Supported: "user", "global"
    |
    */

    'mode' => 'user',

    /*
    |--------------------------------------------------------------------------
    | Theme Icon
    |--------------------------------------------------------------------------
    |
    | The icon used for the "Themes" link in the user menu.
    |
    */

    'icon' => 'heroicon-o-swatch',

    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    */

    'default' => [
        'theme' => 'default',
        'theme_color' => 'blue',
    ],
];
