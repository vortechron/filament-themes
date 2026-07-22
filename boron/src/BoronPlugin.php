<?php

namespace Vortechron\FilamentBoron;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Css;

/**
 * Boron theme plugin for Filament.
 *
 * Registering this plugin applies the Boron colour palette and the Lexend
 * font to a panel. The visual signature (cream surfaces, hard offset card
 * shadows, black sidebar) lives in the theme CSS, which the host app compiles
 * through Vite and registers with ->viteTheme(...). See the package README.
 */
class BoronPlugin implements Plugin
{
    protected bool $applyColors = true;

    protected bool $applyFont = true;

    protected bool $applyStyles = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'boron';
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
     * Skip loading the Lexend Google font.
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
            $panel->font('Lexend');
        }

        if ($this->applyStyles) {
            $panel->assets([
                Css::make('boron-theme', __DIR__.'/../resources/css/boron.css'),
            ], package: 'vortechron/filament-boron');
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
