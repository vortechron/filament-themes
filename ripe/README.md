# Ripe — Stripe Apps SDK v9 theme for Filament 5

A comprehensive Stripe Apps SDK v9 visual-system translation for **Filament
5**. Ripe maps Stripe's component density, type, spacing, keylines, actions,
forms, data presentation, feedback, and interaction states onto Filament's
public CSS hooks.

Not affiliated with or endorsed by Stripe.

## Requirements

- PHP `8.2+`
- Filament `5.x`
- A deployment step that runs `php artisan filament:assets`

## Install

When Ripe is published as an independent package, install it with:

```bash
composer require vortechron/filament-ripe
```

For this monorepo, install the `vortechron/filament-themes` bundle as shown in
the [root installation guide](../README.md#start-here-pick-one-theme), or use
the root guide's local path-repository instructions to require only Ripe.

## Register

Append **one** plugin call to the existing panel chain in
`app/Providers/Filament/AdminPanelProvider.php`:

```php
use Filament\Panel;
use Vortechron\FilamentRipe\RipePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... your existing configuration
        ->plugin(RipePlugin::make());
}
```

Publish Filament's package assets:

```bash
php artisan filament:assets
php artisan optimize:clear
```

No npm or Vite change is required in the consuming application. Do not register
a second theme plugin on the same panel.

## Options

Ripe sets its color palette, loads its bundled local Inter font, and registers
its stylesheet. Each part can be turned off on its own:

```php
RipePlugin::make()
    ->withoutColors() // keep the panel's existing color palette
    ->withoutFont()   // keep the panel's existing font
    ->withoutStyles() // load the palette and font only, no stylesheet
```

Use `->withoutStyles()` only when you load a customized Ripe stylesheet
yourself.

## Customize

Ripe's design tokens are CSS variables. Override them in the application's
Filament theme with the more specific `.fi-body` selector:

```css
.fi-body {
  --ripe-primary: #00b4d8;
  --ripe-radius-lg: 12px;
  --ripe-canvas: #f4f6fb;
}
```

The complete token list is in `resources/css/_tokens.css`, and the PHP palette
handed to Filament is in `src/RipeTheme.php`. The full Stripe Apps SDK v9 to
Filament mapping is documented in
[`STRIPE_APPS_V9_COVERAGE.md`](STRIPE_APPS_V9_COVERAGE.md).

To extend the theme with your own Tailwind classes, publish the editable source
files:

```bash
php artisan vendor:publish --tag=filament-ripe-theme
```

This writes `resources/css/filament/ripe/theme.css` and
`resources/css/filament/ripe/ripe.css`. Register the entrypoint on the panel
with `->viteTheme('resources/css/filament/ripe/theme.css')`, disable the
packaged stylesheet with `RipePlugin::make()->withoutStyles()`, and build it
with Vite.

Ripe includes no Stripe logos, images, React components, or proprietary runtime
assets. It is an independent visual translation.

## Package development

The consuming application does not run these commands. They are only for
maintainers changing Ripe's source CSS:

```bash
npm ci
npm run build        # minified, writes resources/dist/theme.css
npm run build:pretty # readable output
npm run dev          # rebuild on change
```

Source modules live in `resources/css/`:

| File | Contents |
| --- | --- |
| `_tokens.css` | Color, type, shape, spacing and elevation tokens |
| `_foundations.css` | Type scale, page surfaces, links, icons, auth pages |
| `_navigation.css` | Top bar, sidebar, breadcrumbs |
| `_components.css` | Buttons, cards, modals, menus, badges, tabs |
| `_forms.css` | Inputs, selects, checkboxes, radios, toggles |
| `_data.css` | Tables, pagination, infolists, empty states |
| `_feedback.css` | Notifications, callouts, global search |
| `_patterns.css` | Login layout, stats widgets, charts |
| `_dark.css` | The dark color scheme |

Commit the rebuilt `resources/dist/theme.css` with every source CSS change.

## Production check

Confirm the panel login, sidebar, tables, forms, dropdowns, modals, mobile
navigation, charts, loading/empty/error states, and both light and dark mode
after each Filament major upgrade.

## License

MIT.

Inter is bundled under the SIL Open Font License 1.1; its license text is at
`resources/fonts/inter/LICENSE`. Ripe makes no request to a third-party CDN at
runtime.
