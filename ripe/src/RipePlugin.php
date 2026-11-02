<?php

declare(strict_types=1);

namespace Vortechron\FilamentRipe;

use Filament\Contracts\Plugin;
use Filament\FontProviders\LocalFontProvider;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Font;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

/**
 * Ripe theme plugin for Filament.
 *
 * Applies the Ripe palette, the bundled Inter font and the prebuilt stylesheet
 * to one panel.
 */
class RipePlugin implements Plugin
{
    public const ASSET_PACKAGE = 'vortechron/filament-ripe';

    public const ID = 'vortechron-ripe';

    protected bool $applyColors = true;

    protected bool $applyFont = true;

    protected bool $applyStyles = true;

    /**
     * @var array{heading: ?string, description: ?string, image: ?string}|null
     */
    protected ?array $authBranding = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return self::ID;
    }

    /**
     * Skip Ripe's colour palette (keep the panel's existing colours).
     */
    public function withoutColors(): static
    {
        $this->applyColors = false;

        return $this;
    }

    /**
     * Skip loading the bundled Inter font.
     */
    public function withoutFont(): static
    {
        $this->applyFont = false;

        return $this;
    }

    /**
     * Skip loading Ripe's stylesheet.
     */
    public function withoutStyles(): static
    {
        $this->applyStyles = false;

        return $this;
    }

    /**
     * Show a branded panel beside the sign-in, sign-up and password pages.
     * The heading defaults to the panel's brand name. The image is optional
     * and fills the panel behind the text.
     */
    public function authBranding(?string $heading = null, ?string $description = null, ?string $image = null): static
    {
        $this->authBranding = [
            'heading' => $heading,
            'description' => $description,
            'image' => $image,
        ];

        return $this;
    }

    public function register(Panel $panel): void
    {
        if ($this->authBranding !== null) {
            $branding = $this->authBranding;

            $panel->renderHook(
                PanelsRenderHook::SIMPLE_LAYOUT_START,
                fn (): HtmlString => $this->renderAuthBranding($branding),
            );
        }

        if ($this->applyColors) {
            $panel->colors(RipeTheme::colors());
        }

        if ($this->applyFont) {
            $font = Font::make('inter', __DIR__.'/../resources/fonts/inter');

            $panel
                ->assets([
                    $font,
                ], package: self::ASSET_PACKAGE)
                ->font(
                    'Inter',
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

    /**
     * @param  array{heading: ?string, description: ?string, image: ?string}  $branding
     */
    protected function renderAuthBranding(array $branding): HtmlString
    {
        $heading = $branding['heading'] ?? filament()->getBrandName();
        $html = '<aside class="ripe-auth-brand">';

        if (filled($branding['image'])) {
            $html .= '<img class="ripe-auth-brand__image" src="'.e($branding['image']).'" alt="">';
        }

        $html .= '<div class="ripe-auth-brand__body">';
        $html .= '<p class="ripe-auth-brand__heading">'.e($heading).'</p>';

        if (filled($branding['description'])) {
            $html .= '<p class="ripe-auth-brand__description">'.e($branding['description']).'</p>';
        }

        return new HtmlString($html.'</div></aside>');
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
