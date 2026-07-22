<?php

declare(strict_types=1);

namespace Hasnayeen\Themes\Contracts;

interface HasChangeableColor
{
    /**
     * @return array<string, array<int, string>|string>
     */
    public function getPrimaryColor(): array;
}
