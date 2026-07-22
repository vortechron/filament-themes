<?php

declare(strict_types=1);

namespace Hasnayeen\Themes;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ThemesServiceProvider extends PackageServiceProvider
{
    public static string $name = 'themes';

    public static string $viewNamespace = 'themes';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasAssets()
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews(static::$viewNamespace)
            ->hasMigration('add_themes_settings_to_users_table')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations();
            });
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Themes::class, fn (): Themes => new Themes);
    }

    public function packageBooted(): void
    {
        /**
         * Register every theme stylesheet so `php artisan filament:assets`
         * copies them into /public. They are marked `loadedOnRequest()` so
         * Filament does NOT auto-inject all of them on every page — otherwise
         * each theme's CSS would stack and the last one (with !important) would
         * win regardless of the user's selection. The SetTheme middleware is
         * responsible for injecting only the active theme's <link>.
         */
        FilamentAsset::register($this->getAssets(), $this->getAssetPackageName());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'vortechron/filament-hasnayeen';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return app(Themes::class)
            ->getThemes()
            ->map(fn (string $theme): Css => Css::make($theme::getName(), $theme::getPath())->loadedOnRequest())
            ->values()
            ->toArray();
    }
}
