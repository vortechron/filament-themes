<?php

declare(strict_types=1);

namespace Vortechron\FilamentMaterial;

/**
 * Material 3 design tokens exposed to PHP.
 *
 * The values mirror the baseline colour scheme published in the Material
 * Design 3 specification and are kept here so the plugin, the stylesheet and
 * any consuming app all read the same numbers.
 */
final class MaterialTheme
{
    /**
     * Baseline Material 3 accent colours. Filament expands each hex into a
     * full 50-950 shade ramp.
     *
     * - primary   #6750a4  baseline primary (M3 key colour)
     * - secondary #625b71  baseline secondary
     * - danger    #b3261e  baseline error
     * - success   #2e6b4f  green tuned to the same chroma as the baseline
     * - warning   #7a5900  amber tuned to the same chroma as the baseline
     * - info      #00629e  blue tuned to the same chroma as the baseline
     * - gray      #79747e  baseline outline / neutral-variant
     *
     * @return array<string, string>
     */
    public static function colors(): array
    {
        return [
            'primary' => '#6750a4',
            'secondary' => '#625b71',
            'success' => '#2e6b4f',
            'info' => '#00629e',
            'warning' => '#7a5900',
            'danger' => '#b3261e',
            'gray' => '#79747e',
        ];
    }
}
