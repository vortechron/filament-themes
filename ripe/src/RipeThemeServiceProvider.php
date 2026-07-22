<?php

declare(strict_types=1);

namespace Ripe\Theme;

use Illuminate\Support\ServiceProvider;

/**
 * Registers the Ripe theme package with Laravel.
 *
 * Auto-discovered via composer.json `extra.laravel.providers`, so a consuming
 * app does not need to register it manually.
 */
final class RipeThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Allow the host app to publish the raw source CSS if they want to
        // recompile it against their own Tailwind content globs.
        $this->publishes([
            __DIR__.'/../dist/theme.css' => public_path('css/ripe/theme.css'),
        ], 'ripe-theme-assets');

    }
}
