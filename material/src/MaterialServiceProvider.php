<?php

declare(strict_types=1);

namespace Vortechron\FilamentMaterial;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MaterialServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-material';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }

    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__.'/../resources/stubs/theme.css' => resource_path('css/filament/material/theme.css'),
            __DIR__.'/../resources/dist/theme.css' => resource_path('css/filament/material/material.css'),
        ], 'filament-material-theme');
    }
}
