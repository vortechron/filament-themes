<?php

declare(strict_types=1);

namespace Vortechron\FilamentHasnayeen\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Vortechron\FilamentHasnayeen\Contracts\DarkModeOnly;
use Vortechron\FilamentHasnayeen\Contracts\LightModeOnly;
use Vortechron\FilamentHasnayeen\Contracts\ModifiesPanel;
use Vortechron\FilamentHasnayeen\HasnayeenPlugin;
use Vortechron\FilamentHasnayeen\ThemeManager;

class ApplyTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel || ! $panel->hasPlugin(HasnayeenPlugin::ID)) {
            return $next($request);
        }

        $plugin = HasnayeenPlugin::get();
        $panel->darkMode(
            $plugin->isPanelDarkModeEnabled(),
            $plugin->isPanelDarkModeForced(),
        );

        $manager = app(ThemeManager::class);

        if ((! $manager->usesGlobalMode()) && (! Filament::auth()->check())) {
            return $next($request);
        }

        $theme = $manager->current();

        if (! $plugin->isPanelDarkModeForced()) {
            $panel->darkMode(
                ! $theme instanceof LightModeOnly,
                $theme instanceof DarkModeOnly,
            );
        }

        if ($theme instanceof ModifiesPanel) {
            $theme->modifyPanel($panel);
        }

        return $next($request);
    }
}
