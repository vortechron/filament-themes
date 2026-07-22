<?php

declare(strict_types=1);

namespace Vortechron\FilamentBoron;

use Filament\Contracts\Plugin;
use Filament\FontProviders\LocalFontProvider;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Font;

/**
 * Boron theme plugin for Filament.
 *
 * Applies the Boron palette, Lexend font, and prebuilt stylesheet to one panel.
 */
class BoronPlugin implements Plugin
{
    public const ASSET_PACKAGE = 'vortechron/filament-boron';

    public const ID = 'vortechron-boron';

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
     * Skip Boron's colour palette (keep the panel's existing colours).
     */
    public function withoutColors(): static
    {
        $this->applyColors = false;

        return $this;
    }

    /**
     * Skip loading the bundled Lexend font.
     */
    public function withoutFont(): static
    {
        $this->applyFont = false;

        return $this;
    }

    /**
     * Skip loading Boron's stylesheet.
     */
    public function withoutStyles(): static
    {
        $this->applyStyles = false;

        return $this;
    }

    public function register(Panel $panel): void
    {
        if ($this->applyColors) {
            $panel->colors(BoronTheme::colors());
        }

        if ($this->applyFont) {
            $font = Font::make('lexend', __DIR__.'/../resources/fonts/lexend');

            $panel
                ->assets([
                    $font,
                ], package: self::ASSET_PACKAGE)
                ->font(
                    'Lexend',
                    url: fn (): string => asset($font->getRelativePublicPath().'/index.css'),
                    provider: LocalFontProvider::class,
                );
        }

        if ($this->applyStyles) {
            $panel->assets([
                Css::make('theme', __DIR__.'/../resources/css/theme.css'),
            ], package: self::ASSET_PACKAGE);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
