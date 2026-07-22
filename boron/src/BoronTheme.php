<?php

declare(strict_types=1);

namespace Vortechron\FilamentBoron;

/**
 * Boron design tokens, extracted from the Coderthemes "Boron" template's
 * compiled stylesheet. Kept in one place so both the plugin and any consuming
 * app can reference the exact same values.
 */
final class BoronTheme
{
    /**
     * Boron brand colours as hex. Filament expands each into a full palette.
     *
     * - primary   #313a46  dark slate (Boron --bs-primary)
     * - secondary #669776  sage green (Boron accent, active menu highlight)
     * - success   #70bb63
     * - info      #60addf
     * - warning   #ebb751
     * - danger    #ed6060
     * - gray      #8a969c
     *
     * @return array<string, string>
     */
    public static function colors(): array
    {
        return [
            'primary' => '#313a46',
            'secondary' => '#669776',
            'success' => '#70bb63',
            'info' => '#60addf',
            'warning' => '#ebb751',
            'danger' => '#ed6060',
            'gray' => '#8a969c',
        ];
    }
}
