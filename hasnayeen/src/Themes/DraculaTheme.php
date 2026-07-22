<?php

declare(strict_types=1);

namespace Vortechron\FilamentHasnayeen\Themes;

use Vortechron\FilamentHasnayeen\Contracts\DarkModeOnly;
use Vortechron\FilamentHasnayeen\Contracts\Theme;

class DraculaTheme implements DarkModeOnly, Theme
{
    public function name(): string
    {
        return 'dracula';
    }

    public function stylesheetPath(): string
    {
        return __DIR__.'/../../resources/dist/dracula.css';
    }

    public function colors(): array
    {
        return [
            'primary' => '#9580ff',
            'custom' => '#6932f5',
            'secondary' => '#ff80bf',
            'info' => '#80ffea',
            'success' => '#8aff80',
            'warning' => '#f9f06b',
            'danger' => '#ff9580',
        ];
    }
}
