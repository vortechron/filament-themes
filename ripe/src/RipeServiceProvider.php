<?php

declare(strict_types=1);

namespace Vortechron\FilamentRipe;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RipeServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-ripe';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }

    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__.'/../resources/stubs/theme.css' => resource_path('css/filament/ripe/theme.css'),
            __DIR__.'/../resources/dist/theme.css' => resource_path('css/filament/ripe/ripe.css'),
        ], 'filament-ripe-theme');
    }
}
