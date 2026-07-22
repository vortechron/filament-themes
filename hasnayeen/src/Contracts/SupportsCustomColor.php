<?php

declare(strict_types=1);

namespace Vortechron\FilamentHasnayeen\Contracts;

interface SupportsCustomColor
{
    /**
     * @return array<string, array<int, string>|string>
     */
    public function defaultPrimaryColor(): array;
}
