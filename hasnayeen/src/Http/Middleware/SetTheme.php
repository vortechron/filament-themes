<?php

declare(strict_types=1);

namespace Hasnayeen\Themes\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Hasnayeen\Themes\Contracts\CanModifyPanelConfig;
use Hasnayeen\Themes\Contracts\HasOnlyDarkMode;
use Hasnayeen\Themes\Contracts\HasOnlyLightMode;
use Hasnayeen\Themes\Themes;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Themes $themes */
        $themes = app(Themes::class);
        $panel = Filament::getCurrentPanel();

        if (! $panel || ! $panel->hasPlugin('themes')) {
            return $next($request);
        }

        $plugin = ThemesPlugin::get();

        // Reset the original panel setting on every request for Octane safety.
        $panel->darkMode(
            $plugin->isPanelDarkModeEnabled(),
            $plugin->isPanelDarkModeForced(),
        );

        /**
         * In per-user mode there is nothing to theme until someone is signed
         * in. Bailing early also avoids registering user-menu items on guest
         * pages (e.g. login), where resolving the user menu would fail.
         */
        if (config('themes.mode') !== 'global' && ! Filament::auth()->check()) {
            return $next($request);
        }

        $currentTheme = $themes->getCurrentTheme();

        if (! $plugin->isPanelDarkModeForced()) {
            $panel->darkMode(
                ! $currentTheme instanceof HasOnlyLightMode,
                $currentTheme instanceof HasOnlyDarkMode,
            );
        }

        if ($currentTheme instanceof CanModifyPanelConfig) {
            $currentTheme->modifyPanelConfig($panel);
        }

        return $next($request);
    }
}
