<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Theme storage mode
    |--------------------------------------------------------------------------
    |
    | This option determines how the theme will be set for the application.
    | "user" stores the selection on each user model. "global" stores one
    | selection for the whole application in its configured cache store.
    |
    | Supported: "user", "global"
    |
    */

    'mode' => 'user',

    /*
    |--------------------------------------------------------------------------
    | User menu icon
    |--------------------------------------------------------------------------
    |
    | The icon used for the Appearance link in the Filament user menu.
    |
    */

    'icon' => 'heroicon-o-swatch',

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    */

    'default' => [
        'theme' => 'default',
        'color' => 'blue',
    ],

    /*
    |--------------------------------------------------------------------------
    | User model attributes
    |--------------------------------------------------------------------------
    |
    | Change these when your user model stores the selection in custom columns.
    |
    */

    'user_attributes' => [
        'theme' => 'filament_theme',
        'color' => 'filament_theme_color',
    ],
];
