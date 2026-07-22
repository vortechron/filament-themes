<?php

declare(strict_types=1);

namespace Vortechron\FilamentHasnayeen;

use Closure;
use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Vortechron\FilamentHasnayeen\Contracts\Theme;
use Vortechron\FilamentHasnayeen\Filament\Pages\Appearance;
use Vortechron\FilamentHasnayeen\Http\Middleware\ApplyTheme;

class HasnayeenPlugin implements Plugin
{
    public const ASSET_PACKAGE = 'vortechron/filament-hasnayeen';

    public const ID = 'vortechron-hasnayeen';

    protected ?Closure $canViewAppearancePageUsing = null;

    protected bool $panelDarkModeEnabled = true;

    protected bool $panelDarkModeForced = false;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(static::make()->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return self::ID;
    }

    public function register(Panel $panel): void
    {
        $this->panelDarkModeEnabled = $panel->hasDarkMode();
        $this->panelDarkModeForced = $panel->hasDarkModeForced();

        $panel
            ->pages([
                Appearance::class,
            ])
            ->middleware([
                ApplyTheme::class,
            ])
            ->assets($this->assets(), package: self::ASSET_PACKAGE)
            ->userMenuItems([
                'appearance' => Action::make('appearance')
                    ->label(fn (): string => __('filament-hasnayeen::themes.appearance'))
                    ->icon(config(ThemeManager::CONFIG_KEY.'.icon'))
                    ->url(fn (): string => Appearance::getUrl())
                    ->visible(fn (): bool => $this->isAppearancePageVisible()),
            ])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): HtmlString => new HtmlString($this->renderThemeAssets()),
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function canViewAppearancePage(Closure $callback): static
    {
        $this->canViewAppearancePageUsing = $callback;

        return $this;
    }

    public function isAppearancePageVisible(): bool
    {
        return $this->canViewAppearancePageUsing
            ? (bool) ($this->canViewAppearancePageUsing)()
            : true;
    }

    /**
     * @param  array<string, class-string<Theme>>  $themes
     */
    public function registerThemes(array $themes, bool $replace = false): static
    {
        app(ThemeManager::class)->register($themes, $replace);

        return $this;
    }

    public function isPanelDarkModeEnabled(): bool
    {
        return $this->panelDarkModeEnabled;
    }

    public function isPanelDarkModeForced(): bool
    {
        return $this->panelDarkModeForced;
    }

    /**
     * @return array<Css>
     */
    protected function assets(): array
    {
        $themeAssets = app(ThemeManager::class)
            ->all()
            ->map(function (string $themeClass): Css {
                $theme = app($themeClass);

                return Css::make('theme-'.$theme->name(), $theme->stylesheetPath())
                    ->loadedOnRequest();
            })
            ->values()
            ->all();

        return [
            Css::make('appearance', __DIR__.'/../resources/dist/appearance.css'),
            ...$themeAssets,
        ];
    }

    protected function renderThemeAssets(): string
    {
        $manager = app(ThemeManager::class);
        $theme = $manager->current();
        $href = FilamentAsset::getStyleHref(
            'theme-'.$theme->name(),
            package: self::ASSET_PACKAGE,
        );
        $hash = is_readable($theme->stylesheetPath())
            ? substr((string) md5_file($theme->stylesheetPath()), 0, 8)
            : '0';
        $href .= (str_contains($href, '?') ? '&' : '?').'h='.$hash;

        return '<link rel="stylesheet" href="'.e($href).'">'
            .'<style>:root{'.$this->renderColorVariables($manager->currentColors()).'}</style>';
    }

    /**
     * @param  array<string, array<int, string>|string>  $colors
     */
    protected function renderColorVariables(array $colors): string
    {
        $variables = [];

        foreach ($colors as $name => $palette) {
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

        return implode(';', $variables);
    }
}
