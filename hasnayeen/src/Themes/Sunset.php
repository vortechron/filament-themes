<?php

declare(strict_types=1);

namespace Hasnayeen\Themes\Themes;

use Filament\Support\Colors\Color;
use Hasnayeen\Themes\Contracts\HasChangeableColor;
use Hasnayeen\Themes\Contracts\HasOnlyDarkMode;
use Hasnayeen\Themes\Contracts\Theme;
use Illuminate\Support\Arr;

class Sunset implements HasChangeableColor, HasOnlyDarkMode, Theme
{
    public static function getName(): string
    {
        return 'sunset';
    }

    public static function getPath(): string
    {
        return __DIR__.'/../../resources/dist/sunset.css';
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function getThemeColor(): array
    {
        return Arr::except(Color::all(), ['gray', 'zinc', 'neutral', 'stone']);
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function getPrimaryColor(): array
    {
        return ['primary' => $this->getThemeColor()['orange']];
    }
}
