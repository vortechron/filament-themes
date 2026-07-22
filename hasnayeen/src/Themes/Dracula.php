<?php

declare(strict_types=1);

namespace Hasnayeen\Themes\Themes;

use Hasnayeen\Themes\Contracts\HasOnlyDarkMode;
use Hasnayeen\Themes\Contracts\Theme;

class Dracula implements HasOnlyDarkMode, Theme
{
    public static function getName(): string
    {
        return 'dracula';
    }

    public static function getPath(): string
    {
        return __DIR__.'/../../resources/dist/dracula.css';
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function getThemeColor(): array
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
