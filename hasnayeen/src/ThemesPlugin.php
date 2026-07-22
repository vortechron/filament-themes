<?php

declare(strict_types=1);

namespace Hasnayeen\Themes;

use Closure;
use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\View\PanelsRenderHook;
use Hasnayeen\Themes\Contracts\Theme;
use Hasnayeen\Themes\Filament\Pages\Themes as ThemesPage;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Illuminate\Support\HtmlString;

class ThemesPlugin implements Plugin
{
    protected Closure $canViewCallback;

    protected bool $panelDarkModeEnabled = true;

    protected bool $panelDarkModeForced = false;

    public function getId(): string
    {
        return 'themes';
    }

    public function register(Panel $panel): void
    {
        $this->panelDarkModeEnabled = $panel->hasDarkMode();
        $this->panelDarkModeForced = $panel->hasDarkModeForced();

        $panel
            ->pages([
                ThemesPage::class,
            ])
            ->middleware([
                SetTheme::class,
            ])
            ->assets([
                Css::make('themes-ui', __DIR__.'/../resources/dist/themes.css'),
            ], package: 'vortechron/filament-hasnayeen')
            ->userMenuItems([
                'themes' => Action::make('themes')
                    ->label(fn (): string => __('themes::themes.themes'))
                    ->icon(config('themes.icon'))
                    ->url(fn (): string => ThemesPage::getUrl())
                    ->visible(fn (): bool => static::canView()),
            ])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): HtmlString => new HtmlString($this->getThemeAssets()),
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function canViewThemesPage(Closure $callback): self
    {
        $this->canViewCallback = $callback;

        return $this;
    }

    public static function canView(): bool
    {
        if (isset(static::get()->canViewCallback)) {
            return (static::get()->canViewCallback)();
        }

        return true;
    }

    public function isPanelDarkModeEnabled(): bool
    {
        return $this->panelDarkModeEnabled;
    }

    public function isPanelDarkModeForced(): bool
    {
        return $this->panelDarkModeForced;
    }

    protected function getThemeAssets(): string
    {
        $theme = app(Themes::class)->getCurrentTheme();
        $href = FilamentAsset::getStyleHref(
            $theme::getName(),
            package: 'vortechron/filament-hasnayeen',
        );

        $cssPath = $theme::getPath();
        $hash = is_readable($cssPath) ? substr((string) md5_file($cssPath), 0, 8) : '0';
        $href .= (str_contains($href, '?') ? '&' : '?').'h='.$hash;

        $variables = [];

        foreach (app(Themes::class)->getCurrentThemeColor() as $name => $palette) {
            $palette = is_string($palette)
                ? Color::generatePalette($palette)
                : array_map(
                    fn (string|int $color): string|int => is_string($color)
                        ? Color::convertToOklch($color)
                        : $color,
                    $palette,
                );

            foreach ($palette as $shade => $color) {
                $variables[] = '--'.preg_replace('/[^a-zA-Z0-9_-]/', '', $name)
                    .'-'.preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $shade)
                    .':'.e((string) $color);
            }
        }

        return '<link rel="stylesheet" href="'.e($href).'">'
            .'<style>:root{'.implode(';', $variables).'}</style>';
    }

    /**
     * @param  array<string, class-string<Theme>>  $theme
     */
    public function registerTheme(array $theme, bool $override = false): self
    {
        app(Themes::class)->register($theme, $override);

        return $this;
    }
}
