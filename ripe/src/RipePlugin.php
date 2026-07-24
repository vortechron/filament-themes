<?php

declare(strict_types=1);

namespace Vortechron\FilamentRipe;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;

/**
 * Applies the Ripe palette and prebuilt stylesheet to one panel.
 */
class RipePlugin implements Plugin
{
    public const ASSET_PACKAGE = 'vortechron/filament-ripe';

    public const ID = 'vortechron-ripe';

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return self::ID;
    }

    public function register(Panel $panel): void
    {
        $panel
            ->colors([
                'primary' => Color::hex('#533afd'),
            ])
            ->assets([
                Css::make('theme', __DIR__.'/../resources/dist/theme.css'),
            ], package: self::ASSET_PACKAGE);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
