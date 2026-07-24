# Ripe Theme for Filament 5

A comprehensive Stripe Apps SDK v9 visual-system translation for **Filament
5**. Ripe maps Stripe's component density, type, spacing, keylines, actions,
forms, data presentation, feedback, and interaction states onto Filament's
public CSS hooks.

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

Register Ripe in the target panel provider:

```php
use Filament\Panel;
use Vortechron\FilamentRipe\RipePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(RipePlugin::make());
}
```

Publish Filament's package assets:

```bash
php artisan filament:assets
php artisan optimize:clear
```

No npm or Vite change is required in the consuming application.

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

The complete token list is in `resources/css/_tokens.css`. The full Stripe Apps
SDK v9 to Filament mapping is documented in
[`STRIPE_APPS_V9_COVERAGE.md`](STRIPE_APPS_V9_COVERAGE.md).

Ripe includes no Stripe logos, fonts, images, React components, or proprietary
runtime assets. It is an independent visual translation and is not affiliated
with or endorsed by Stripe.

## Package development

The consuming application does not run these commands. They are only for
maintainers changing Ripe's source CSS:

```bash
npm ci
npm run build
```

Commit the rebuilt `resources/dist/theme.css` with every source CSS change.

## Production check

Confirm the panel login, sidebar, tables, forms, dropdowns, modals, mobile
navigation, charts, loading/empty/error states, and both light and dark mode
after each Filament major upgrade.

## License

MIT.
