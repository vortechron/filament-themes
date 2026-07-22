<?php

declare(strict_types=1);

namespace Vortechron\FilamentHasnayeen;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HasnayeenServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-hasnayeen';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasMigration('add_filament_theme_settings_to_users_table')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations();
            });
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ThemeManager::class);
    }
}
