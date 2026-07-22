<?php

declare(strict_types=1);

namespace Hasnayeen\Themes\Contracts;

interface Theme
{
    public static function getName(): string;

    public static function getPath(): string;

    /**
     * @return array<string, array<int, string>|string>
     */
    public function getThemeColor(): array;
}
