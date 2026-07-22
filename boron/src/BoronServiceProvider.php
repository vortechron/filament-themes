<?php

declare(strict_types=1);

namespace Vortechron\FilamentBoron;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BoronServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-boron';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }

    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__.'/../resources/stubs/theme.css' => resource_path('css/filament/boron/theme.css'),
            __DIR__.'/../resources/css/theme.css' => resource_path('css/filament/boron/boron.css'),
        ], 'filament-boron-theme');
    }
}
