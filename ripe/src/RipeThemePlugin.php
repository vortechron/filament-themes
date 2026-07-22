<?php

declare(strict_types=1);

namespace Ripe\Theme;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;

/**
 * Ripe — Stripe-dashboard inspired Filament theme plugin.
 *
 * Usage (in a PanelProvider's panel() method):
 *
 *     use Ripe\Theme\RipeThemePlugin;
 *
 *     return $panel->plugin(RipeThemePlugin::make());
 *
 * What it does:
 *   1. Registers Stripe brand purple (#635BFF) as the panel `primary` color.
 *   2. Loads the prebuilt Ripe stylesheet (dist/theme.css) into the panel.
 *
 * No `npm run build` is required in the consuming app — the compiled CSS ships
 * with this package.
 */
final class RipeThemePlugin implements Plugin
{
    public static function make(): static
    {
        return new self;
    }

    public function getId(): string
    {
        return 'ripe-theme';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->colors([
                'primary' => Color::hex('#635bff'),
            ])
            ->assets([
                Css::make('ripe-theme', __DIR__.'/../dist/theme.css'),
            ], package: 'vortechron/filament-ripe');
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
