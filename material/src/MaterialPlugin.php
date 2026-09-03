<?php

declare(strict_types=1);

namespace Vortechron\FilamentMaterial;

use Filament\Contracts\Plugin;
use Filament\FontProviders\LocalFontProvider;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Font;

/**
 * Material 3 theme plugin for Filament.
 *
 * Applies the Material 3 baseline palette, the bundled Roboto font and the
 * prebuilt stylesheet to one panel.
 */
class MaterialPlugin implements Plugin
{
    public const ASSET_PACKAGE = 'vortechron/filament-material';

    public const ID = 'vortechron-material';

    protected bool $applyColors = true;

    protected bool $applyFont = true;

    protected bool $applyStyles = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return self::ID;
    }

    /**
     * Skip the Material 3 palette (keep the panel's existing colours).
     */
    public function withoutColors(): static
    {
        $this->applyColors = false;

        return $this;
    }

    /**
     * Skip loading the bundled Roboto font.
     */
    public function withoutFont(): static
    {
        $this->applyFont = false;

        return $this;
    }

    /**
     * Skip loading the Material stylesheet.
     */
    public function withoutStyles(): static
    {
        $this->applyStyles = false;

        return $this;
    }

    public function register(Panel $panel): void
    {
        if ($this->applyColors) {
            $panel->colors(MaterialTheme::colors());
        }

        if ($this->applyFont) {
            $font = Font::make('roboto', __DIR__.'/../resources/fonts/roboto');

            $panel
                ->assets([
                    $font,
                ], package: self::ASSET_PACKAGE)
                ->font(
                    'Roboto',
                    url: fn (): string => asset($font->getRelativePublicPath().'/index.css'),
                    provider: LocalFontProvider::class,
                );
        }

        if ($this->applyStyles) {
            $panel->assets([
                Css::make('theme', __DIR__.'/../resources/dist/theme.css'),
            ], package: self::ASSET_PACKAGE);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
