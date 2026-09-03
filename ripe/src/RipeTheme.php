<?php

declare(strict_types=1);

namespace Vortechron\FilamentRipe;

/**
 * Ripe design tokens exposed to PHP.
 *
 * The values mirror the Stripe Apps SDK v9 palette used by
 * resources/css/_tokens.css and are kept here so the plugin, the stylesheet and
 * any consuming app all read the same numbers.
 */
final class RipeTheme
{
    /**
     * Ripe brand colours as hex. Filament expands each into a full palette.
     *
     * Ripe deliberately registers only `primary`. Its status, surface and text
     * colours are painted by the `--ripe-*` custom properties in
     * resources/css/_tokens.css, so handing Filament a second palette here
     * would produce two competing sources of truth.
     *
     * - primary #533afd  Stripe Apps SDK v9 action colour (--ripe-primary)
     *
     * @return array<string, string>
     */
    public static function colors(): array
    {
        return [
            'primary' => '#533afd',
        ];
    }
}
