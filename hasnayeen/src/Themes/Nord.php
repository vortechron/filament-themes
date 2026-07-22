<?php

declare(strict_types=1);

namespace Hasnayeen\Themes\Themes;

use Filament\Support\Colors\Color;
use Hasnayeen\Themes\Contracts\Theme;

class Nord implements Theme
{
    public static function getName(): string
    {
        return 'nord';
    }

    public static function getPath(): string
    {
        return __DIR__.'/../../resources/dist/nord.css';
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function getThemeColor(): array
    {
        return [
            'primary' => Color::hex('#8FBCBB'),
            'secondary' => Color::hex('#2E3440'),
            'info' => Color::hex('#5E81AC'),
            'success' => Color::hex('#A3BE8C'),
            'warning' => Color::hex('#D08770'),
            'danger' => Color::hex('#BF616A'),
        ];
    }
}
