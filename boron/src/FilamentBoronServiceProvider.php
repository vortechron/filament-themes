<?php

namespace Vortechron\FilamentBoron;

use Illuminate\Support\ServiceProvider;

class FilamentBoronServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../resources/stubs/theme.css' => resource_path('css/filament/boron/theme.css'),
            __DIR__.'/../resources/css/boron.css' => resource_path('css/filament/boron/boron.css'),
        ], 'filament-boron-theme');
    }
}
