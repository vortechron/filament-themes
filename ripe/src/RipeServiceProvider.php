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
}
