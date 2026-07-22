<?php

declare(strict_types=1);

namespace Vortechron\FilamentHasnayeen\Themes;

use Filament\Support\Colors\Color;
use Illuminate\Support\Arr;
use Vortechron\FilamentHasnayeen\Contracts\DarkModeOnly;
use Vortechron\FilamentHasnayeen\Contracts\SupportsCustomColor;
use Vortechron\FilamentHasnayeen\Contracts\Theme;

class SunsetTheme implements DarkModeOnly, SupportsCustomColor, Theme
{
    public function name(): string
    {
        return 'sunset';
    }

    public function stylesheetPath(): string
    {
        return __DIR__.'/../../resources/dist/sunset.css';
    }

    public function colors(): array
    {
        return Arr::except(Color::all(), ['gray', 'zinc', 'neutral', 'stone']);
    }

    public function defaultPrimaryColor(): array
    {
        return ['primary' => $this->colors()['orange']];
    }
}
