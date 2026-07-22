<?php

declare(strict_types=1);

namespace Vortechron\FilamentHasnayeen\Themes;

use Filament\Support\Colors\Color;
use Vortechron\FilamentHasnayeen\Contracts\Theme;

class NordTheme implements Theme
{
    public function name(): string
    {
        return 'nord';
    }

    public function stylesheetPath(): string
    {
        return __DIR__.'/../../resources/dist/nord.css';
    }

    public function colors(): array
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
